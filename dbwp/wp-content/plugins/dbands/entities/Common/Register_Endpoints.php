<?php

namespace dbp\Common;

use cavWP\Parse_HTML;
use dbp\Services\TMDB;
use dbp\Services\Youtube;
use WP_Query;
use WP_REST_Response;
use WP_REST_Server;

class Register_Endpoints
{
   public function __construct()
   {
      add_action('rest_api_init', [$this, 'create_endpoints']);
      add_action('pre_get_posts', [$this, 'set_search_per_page'], 99);

      add_filter('rest_post_dispatch', [$this, 'parse_error'], 10, 3);
   }

   public function create_endpoints(): void
   {
      register_rest_route('db/v1', '/ajax', [
         'methods'             => WP_REST_Server::READABLE,
         'callback'            => [$this, 'get_ajax_content'],
         'permission_callback' => '__return_true',
         'args'                => [
            'key' => [
               'type'     => 'string',
               'enum'     => ['author', 'category', 'date', 'home', 'page', 'single', 'lyric', 'tag'],
               'required' => true,
            ],
            'value' => [
               'type'     => 'string',
               'required' => true,
            ],
            'page' => [
               'type'    => 'integer',
               'default' => 0,
            ],
         ],
      ]);

      register_rest_route('db/v1', '/providers', [
         'methods'             => WP_REST_Server::READABLE,
         'callback'            => [$this, 'get_providers_content'],
         'permission_callback' => '__return_true',
         'args'                => [
            'id' => [
               'type'     => 'numeric',
               'required' => true,
            ],
            'type' => [
               'type'     => 'string',
               'enum'     => ['movie', 'tv'],
               'required' => true,
            ],
            'title' => [
               'type'     => 'string',
               'required' => true,
            ],
         ],
      ]);

      $search_options = Utils::get_search_options(true);

      register_rest_route('db/v1', '/search', [
         'methods'             => WP_REST_Server::READABLE,
         'callback'            => [$this, 'get_search_content'],
         'permission_callback' => '__return_true',
         'args'                => [
            'q' => [
               'type'     => 'string',
               'required' => true,
            ],
            'search_type' => [
               'type'     => 'string',
               'enum'     => $search_options,
               'required' => true,
            ],
            'page' => [
               'type'    => 'integer',
               'default' => 0,
            ],
         ],
      ]);

      register_rest_route('db/v1', '/next-video', [
         'methods'             => WP_REST_Server::READABLE,
         'callback'            => [$this, 'get_next_video'],
         'permission_callback' => '__return_true',
         'args'                => [
            'v' => [
               'type'     => 'string',
               'required' => true,
            ],
         ],
      ]);
   }

   public function get_ajax_content($request)
   {
      $key   = $request->get_param('key');
      $value = $request->get_param('value');
      $page  = $request->get_param('page');

      $queries_keys = [
         'author'   => 'author_name',
         'category' => 'category_name',
         'page'     => 'pagename',
         'single'   => 'name',
         'lyric'    => 'name',
      ];

      if ('streaming' === $value) {
         $key               = 'page-streaming';
         $query['pagename'] = $value;
      } elseif ('traducoes' === $value) {
         $key = 'archive-lyric';

         $query['post_type'] = 'lyric';
      } elseif ('date' === $key) {
         $dates             = explode('/', $value);
         $query['year']     = $dates[0];
         $query['monthnum'] = $dates[1];
      } elseif (in_array($key, array_keys($queries_keys))) {
         $query[$queries_keys[$key]] = $value;
      } elseif ('home' !== $key) {
         $query[$key] = $value;
      }

      if ('lyric' === $key) {
         $key = 'single-lyric';

         $query['post_type'] = 'lyric';
      }

      // ARCHIVES
      if (in_array($key, ['author', 'category', 'date', 'home', 'tag', 'page-streaming'])) {
         $query['paged'] = $page;
      }

      global $wp_query;
      $wp_query = new WP_Query($query);

      // CONTENT
      ob_start();
      get_page_component($key, 'content');
      $content = ob_get_clean();

      $minify  = new Parse_HTML($content);
      $content = $minify->get_html();

      if ($page > 1) {
         $actions[] = [
            'action'  => 'append',
            'target'  => '#content',
            'content' => $content,
         ];
      } else {
         $actions[] = [
            'action'  => 'html',
            'target'  => '#content',
            'content' => $content,
         ];

         // COVER + SCROLL
         if (in_array($key, ['tag', 'author', 'single', 'single-lyric'])) {
            ob_start();
            get_component(['header', 'cover']);
            $cover = ob_get_clean();

            $minify = new Parse_HTML($cover);
            $cover  = $minify->get_html();

            $actions[] = [
               'action'  => 'html',
               'target'  => '#cover',
               'content' => $cover,
            ];

            $actions[] = [
               'action' => 'scroll',
               'target' => '#top',
            ];
         } else {
            $actions[] = [
               'action' => 'scroll',
               'target' => '#main',
            ];
         }

         // CLASS
         $classes   = implode(' ', get_body_class());
         $actions[] = [
            'action'  => 'setAttr',
            'target'  => 'body',
            'content' => 'class',
            'extra'   => $classes,
         ];

         $title = html_entity_decode(wp_get_document_title());

         $actions[] = [
            'action'  => 'title',
            'content' => $title,
         ];

         $actions[] = [
            'action'  => 'setAttr',
            'target'  => 'body',
            'content' => 'data-title',
            'extra'   => $title,
         ];

         $actions[] = [
            'action' => 'cb',
            'target' => 'refreshAds()',
         ];
      }

      wp_reset_query();

      return new WP_REST_Response($actions);
   }

   public function get_next_video($request)
   {
      $video_ID = $request->get_param('v');
      $dbtv     = new Youtube();

      return $dbtv->get_feed(1, $video_ID);
   }

   public function get_providers_content($request)
   {
      $item_ID   = $request->get_param('id');
      $item_type = $request->get_param('type');
      $title     = $request->get_param('title');

      $tmdb      = new TMDB();
      $providers = $tmdb->get_providers($item_ID, $item_type);

      $link    = '';
      $content = 'Não está disponível no Brasil no momento.';

      if (!empty($providers['BR'])) {
         $content = '<ul class="flex flex-wrap gap-3">';

         foreach ($providers['BR'] as $type => $providers) {
            if ('link' === $type) {
               $link = $providers;
               continue;
            }

            $content .= '<li>';
            $content .= '<span class="font-medium text-sm">' . Utils::get_provider_type_name($type) . '</span>';
            $content .= '<ul class="flex gap-1 pt-1">';

            foreach ($providers as $provider) {
               $content .= '<li>';
               $content .= '<a href="' . Utils::get_provider_link($provider, $title, $link) . '" target="_blank" rel="external nofollow">';
               $content .= '<img class="rounded-md" src="' . $tmdb->image_url . 'w45' . $provider['logo_path'] . '" alt="' . $provider['provider_name'] . '" title="' . $provider['provider_name'] . '" />';
               $content .= '</a>';
               $content .= '</li>';
            }
            $content .= '</ul>';
            $content .= '</li>';
         }
         $content .= '</ul>';
      }

      $minify  = new Parse_HTML($content);
      $content = $minify->get_html();

      $actions = [
         'action'  => 'html',
         'target'  => '#tmdbModal .providers',
         'content' => $content,
      ];

      return new WP_REST_Response($actions);
   }

   public function get_search_content($request)
   {
      $s    = $request->get_param('q');
      $type = $request->get_param('search_type');
      $page = $request->get_param('page');

      global $wp_query;
      $wp_query = new WP_Query([
         's'              => $s,
         'posts_per_page' => 15,
         'paged'          => $page,
      ]);
      $wp_query->set('search_type', $type);

      ob_start();

      get_page_component('search', 'content');

      $content = ob_get_clean();

      $minify  = new Parse_HTML($content);
      $content = $minify->get_html();

      if ($page > 1) {
         $actions[] = [
            'action'  => 'append',
            'target'  => '#content',
            'content' => $content,
         ];
      } else {
         $classes   = implode(' ', get_body_class());
         $actions[] = [
            'action'  => 'setAttr',
            'target'  => 'body',
            'content' => 'class',
            'extra'   => $classes,
         ];

         $actions[] = [
            'action'  => 'html',
            'target'  => '#content',
            'content' => $content,
         ];

         $actions[] = [
            'action' => 'scroll',
            'target' => '#search-anchor',
         ];

         $title = html_entity_decode(wp_get_document_title());

         $actions[] = [
            'action'  => 'title',
            'content' => $title,
         ];

         $actions[] = [
            'action'  => 'setAttr',
            'target'  => 'body',
            'content' => 'data-title',
            'extra'   => $title,
         ];

         $actions[] = [
            'action' => 'cb',
            'target' => 'refreshAds()',
         ];
      }

      wp_reset_query();

      return new WP_REST_Response($actions);
   }

   public function parse_error($response, $_server, $request)
   {
      if (!str_starts_with($request->get_route(), 'db/v1')) {
         return $response;
      }

      if (!$response->is_error()) {
         return $response;
      }

      $data = $response->get_data();

      return new WP_REST_Response([
         [
            'action'  => 'addClass',
            'target'  => '#loading-bar',
            'content' => 'error',
         ],
         [
            'action'  => 'text',
            'target'  => '#loading-bar',
            'content' => $data['message'],
         ],
         [
            'action'  => 'delay',
            'content' => [
               [
                  'action'  => 'removeClass',
                  'target'  => '#loading-bar',
                  'content' => 'error',
               ],
               [
                  'action'  => 'text',
                  'target'  => '#loading-bar',
                  'content' => '',
               ],
            ],
         ],
      ], 400);
   }

   public function set_search_per_page($query): void
   {
      if ($query->is_main_query() && is_search()) {
         $query->set('posts_per_page', 15);
      }
   }
}
