<?php

namespace dbp\Common;

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

      add_filter('rest_post_dispatch', [$this, 'parse_error']);
      add_filter('rest_url_prefix', [$this, 'change_api_prefix']);
   }

   public function change_api_prefix()
   {
      return 'api';
   }

   public function set_search_per_page($query): void
   {
      if ($query->is_main_query() && is_search()) {
         $query->set('posts_per_page', 15);
      }
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
      $key   = sanitize_text_field($request->get_param('key'));
      $value = sanitize_text_field($request->get_param('value'));
      $page  = sanitize_text_field($request->get_param('page'));

      $queries_keys = [
         'author'   => 'author_name',
         'category' => 'category_name',
         'page'     => 'pagename',
         'single'   => 'name',
         'lyric'    => 'name',
      ];

      if ('date' === $key) {
         $dates             = explode('/', $value);
         $query['year']     = $dates[0];
         $query['monthnum'] = $dates[1];
      } elseif (in_array($key, array_keys($queries_keys))) {
         $query[$queries_keys[$key]] = $value;
      } elseif ('home' !== $key) {
         $query[$key] = $value;
      }

      if ('lyric' === $key) {
         $query['post_type'] = $key;
      }

      // ARCHIVES
      if (in_array($key, ['author', 'category', 'date', 'home', 'tag'])) {
         $query['paged'] = $page;
      }

      if ('lyric' === $key) {
         $key = 'single-lyric';
      }

      global $wp_query;
      $wp_query = new WP_Query($query);

      // CONTENT
      ob_start();
      get_page_component($key, 'content');
      $content = ob_get_clean();

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
            'content' => "class={$classes}",
         ];

         $title = html_entity_decode(wp_get_document_title());

         $actions[] = [
            'action'  => 'title',
            'content' => $title,
         ];

         $actions[] = [
            'action'  => 'setAttr',
            'target'  => 'body',
            'content' => "data-title={$title}",
         ];

         $actions[] = [
            'action'  => 'ignore',
            'target'  => 'pagination.maxPage',
            'content' => $wp_query->max_num_pages,
         ];
      }

      wp_reset_query();

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
            'content' => "class={$classes}",
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
            'content' => "data-title={$title}",
         ];
      }

      wp_reset_query();

      return new WP_REST_Response($actions);
   }

   public function get_next_video($request)
   {
      $video_ID = sanitize_text_field($request->get_param('v'));
      $dbtv     = new Youtube(YT_CHANNEL_ID);

      return $dbtv->get_feed(1, $video_ID);
   }

   public function parse_error($response)
   {
      $data = $response->get_data();

      if (!Utils::is_response_error($data)) {
         return $response;
      }

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
}
