<?php

namespace dbt\Common;

use dbp\Services\Spotify\Spotify;

class Register
{
   public function __construct()
   {
      add_action('cav_head_metas', [$this, 'add_metas']);
      add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
      add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
      add_action('wp_resource_hints', [$this, 'add_resources'], 10, 2);

      add_filter('cav_post_thumbnail_placeholder_img', [$this, 'sets_thumbnail_placeholder']);
      add_filter('the_content', [$this, 'add_embed_content']);
      add_filter('embed_oembed_html', [$this, 'edit_oembed'], 10, 2);
   }

   public function add_embed_content($content)
   {
      $post_ID = get_the_ID();

      if ($post_ID && 'lyric' === get_post_type($post_ID)) {
         if (!metadata_exists('post', $post_ID, 'spotify_track')) {
            $spotify       = new Spotify();
            $artist        = get_the_terms($post_ID, 'post_tag')[0]->name;
            $track         = get_the_title($post_ID);
            $spotify_track = $spotify->search("{$artist} {$track}", 'track');

            if (!$spotify_track) {
               add_post_meta($post_ID, 'spotify_track', $spotify_track);
            }
         }

         if (!empty($spotify_track) && 'notrack' !== $spotify_track) {
            $embed   = '<div class="text-center"><iframe src="https://open.spotify.com/embed/track/' . $spotify_track . '" width="300" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media" title="Esta faixa no Spotify"></iframe></div>';
            $content = $embed . $content;
         }
      }

      return $content;
   }

   public function add_metas(): void
   {
      if ('site' !== get_query_var('search_type', 'site')) {
         echo '<meta name="robots" content="noindex, nofollow">';
      }
   }

   public function add_resources($urls, $type)
   {
      if ('dns-prefetch' === $type) {
         $urls[] = [
            'href' => 'https://adservice.google.com',
         ];
      }

      if ('preconnect' === $type) {
         $urls[] = [
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
         ];
      }

      return $urls;
   }

   public function edit_oembed($oembed, $url)
   {
      if (!str_contains($url, 'youtube.com')) {
         return '<div class="aspect-video *:size-full">' . $oembed . '</div>';
      }

      ob_start();
      get_component('embed-video', [
         'oembed' => $oembed,
      ]);

      return ob_get_clean();
   }

   public function enqueue_scripts(): void
   {
      global $wp_query;

      wp_enqueue_script('main', get_theme_file_uri('assets/script.js'), ['youtube'], false, [
         'strategy' => 'defer',
      ]);

      wp_localize_script('main', 'dbands', [
         'mainUrl'  => get_bloginfo('url'),
         'catBase'  => get_option('category_base'),
         'tagBase'  => get_option('tag_base'),
         'apiBase'  => get_rest_url(null, 'db/v1'),
         'maxPages' => $wp_query->max_num_pages,
      ]);

      wp_enqueue_script('youtube', 'https://www.youtube.com/iframe_api', [], null, [
         'strategy' => 'defer',
      ]);
   }

   public function enqueue_styles(): void
   {
      wp_enqueue_style('main', get_theme_file_uri('assets/style.css'), ['fontawesome']);
   }

   public function sets_thumbnail_placeholder()
   {
      ob_start();
      get_component('img-placeholder');

      return ob_get_clean();
   }
}
