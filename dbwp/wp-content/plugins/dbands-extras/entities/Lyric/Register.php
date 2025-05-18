<?php

namespace dbp\Lyric;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register']);
      add_action('manage_lyric_posts_custom_column', [$this, 'columns_content'], 10, 2);
      add_action('add_meta_boxes', [$this, 'add_meta_fields']);
      add_action('save_post_lyric', [$this, 'save_fields'], 10, 2);

      add_filter('wpe_head_metatags', [$this, 'sets_metatags']);
      add_filter('manage_lyric_posts_columns', [$this, 'add_columns']);
   }

   public function sets_metatags($metas)
   {
      if (!is_singular('lyric')) {
         return $metas;
      }

      $Lyric = new Lyric();

      unset($metas['og:article:published_time'], $metas['og:article:modified_time'], $metas['author'], $metas['og:article:tag']);

      $metas['og:type'] = 'music.song';

      $metas['og:music:musician'] = $Lyric->get_artists();
      $metas['og:title']          = $Lyric->get_fullname() . ' (tradução)';

      $metas['og:music:album:track'] = $Lyric->get('order');

      $albums = $Lyric->get('terms', taxonomy: 'albuns');

      if (!empty($albums)) {
         $metas['og:music:album'] = $albums[0]->name;
      }

      $metas['description'] = sprintf(
         esc_attr__('%s, letra de %s traduzida por %s', 'dbands'),
         $Lyric->get('title'),
         $Lyric->get_artists(),
         $Lyric->get('tradutor'),
      );

      return $metas;
   }

   public function register(): void
   {
      register_post_type('lyric', [
         'labels' => [
            'name' => 'Letras',
         ],
         'public'        => true,
         'menu_position' => 5,
         'menu_icon'     => 'dashicons-playlist-audio',
         'supports'      => ['title', 'editor', 'page-attributes', 'custom-fields', 'post-formats', 'author', 'excerpt'],
         'rewrite'       => [
            'slug' => 'letra',
         ],
         'taxonomies' => [
            'post_tag',
            'albums',
         ],
      ]);
   }

   public function add_columns($columns)
   {
      $columns_new = [
         'album'      => esc_html__('Álbum', 'dbands'),
         'translator' => esc_html__('Tradutor', 'dbands'),
      ];
      $columns_start = array_splice($columns, 0, 3);

      return array_merge($columns_start, $columns_new, $columns);
   }

   public function columns_content($column_name, $post_ID): void
   {
      switch ($column_name) {
         case 'album':
            $albums = array_map(fn($album) => $album->name, get_the_terms($post_ID, 'albuns'));

            echo implode(', ', $albums);

            break;

         case 'translator':
            $translator = get_post_meta($post_ID, 'tradutor', true);

            if ($translator) {
               echo $translator;
            }

            break;

         default:
            break;
      }
   }

   public function add_meta_fields($post_type): void
   {
      if ('lyric' !== $post_type) {
         return;
      }

      add_meta_box('dbands_lyrics_meta_box', 'Informações', [$this, 'meta_content'], 'lyric', 'normal', 'high');
   }

   public function meta_content($post): void
   {
      $composer   = get_post_meta($post->ID, 'compositor', true);
      $translator = get_post_meta($post->ID, 'tradutor', true);
      $spotify    = get_post_meta($post->ID, 'spotify_track', true);
      $single     = get_post_meta($post->ID, 'single', true);

      wp_nonce_field('lyric_edit', '_wpnonce_lyric');

      echo <<<META_BOX
      <table width="100%">
         <tr><td>
            <p><label for="tradutor"><strong>Tradução</strong></label><br>
            <input id="tradutor" type="text" name="tradutor" value="{$translator}" class="widefat"><br>
            <small>Nome do(s) tradutor(es).</small></p>
         </td></tr>
         <tr><td>
            <p><label for="compositor"><strong>Composição</strong></label><br>
            <input id="compositor" type="text" name="compositor" value="{$composer}" class="widefat"><br>
            <small>Nome do(s) compositor(es).</small></p>
         </td></tr>
         <tr><td>
            <p><label for="single"><strong>Single</strong></label><br>
            <input id="single" type="text" name="single" value="{$single}" class="widefat"><br>
            <small>Se não em álbum principal, nome do single.</small></p>
         </td></tr>
         <tr><td>
            <p><label for="spotify_track"><strong>Faixa no Spotify</strong></label><br>
            <input id="spotify_track" type="text" name="spotify_track" value="{$spotify}" class="widefat"><br>
            <small>ID da faixa no Spotify.</small></p>
         </td></tr>
      </table>
      META_BOX;
   }

   public function save_fields($post_ID, $post)
   {
      if (empty($_POST)) {
         return;
      }

      check_admin_referer('lyric_edit', '_wpnonce_lyric');

      if (empty($post_ID)) {
         $post_ID = $post->ID;
      }

      if (!current_user_can('edit_post', $post_ID)) {
         return $post_ID;
      }

      $keys = ['compositor', 'tradutor', 'spotify_track', 'single'];

      foreach ($keys as $key) {
         $value = $_POST[$key];

         if (empty($value) && !is_numeric($value)) {
            delete_post_meta($post_ID, $key);

            continue;
         }

         update_post_meta($post_ID, $key, trim($value));
      }

      $terms = get_terms([
         'taxonomy'   => ['post_tag', 'albuns'],
         'fields'     => 'names',
         'object_ids' => $post_ID,
      ]);

      if (count($terms) <= 1) {
         return;
      }

      $searchable = implode(' ', $terms);
      $insert     = "<!-- {$searchable} -->";

      $check = substr($post->post_content, -strlen($insert), strlen($insert));

      if ($insert === $check) {
         return;
      }

      wp_update_post([
         'ID'           => $post_ID,
         'post_content' => $post->post_content . "\n\r" . $insert,
      ], false, false);
   }
}
