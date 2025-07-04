<?php

namespace dbp\Album;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'register'], 9);
      add_action('albuns_add_form_fields', [$this, 'add_fields']);
      add_action('albuns_edit_form_fields', [$this, 'edit_fields']);
      add_action('saved_albuns', [$this, 'save_fields']);
      add_action('admin_enqueue_scripts', [$this, 'handle_assets']);
   }

   public function add_fields(): void
   {
      wp_nonce_field('album_edit', '_wpnonce_album');

      echo <<<'HTML'
            <div class="form-field">
               <label for="album_artist">Artista</label>
               <input id="album_artist" name="album_artist" type="text" value size="40" />
            </div>
            <div class="form-field">
               <label for="album_year">Ano de lançamento</label>
               <input id="album_year" name="album_year" type="number" value size="40" min="1899" max="2199" />
            </div>
            <div class="form-field">
               <label for="album_producer">Produtor</label>
               <input id="album_producer" name="album_producer" type="text" value size="40" />
            </div>
            <div class="form-field">
               <label for="album_cover">Capa do Álbum</label>
               <input id="album_cover" name="album_cover" type="hidden" value size="40" />
               <div class="cover-preview"></div>
               <button type="button" class="button" data-media-uploader-target="#album_cover" data-media-thumbnail-target=".cover-preview">Selecionar imagem</button>
            </div>
      HTML;
   }

   public function edit_fields($album): void
   {
      $album_artist    = get_term_meta($album->term_id, 'album_artist', true);
      $album_year      = get_term_meta($album->term_id, 'album_year', true);
      $album_producer  = get_term_meta($album->term_id, 'album_producer', true);
      $album_cover     = get_term_meta($album->term_id, 'album_cover', true);
      $album_thumbnail = empty($album_cover) ? '' : wp_get_attachment_image((int) $album_cover, 'medium');

      wp_nonce_field('album_edit', '_wpnonce_album');

      echo <<<HTML
      <tr class="form-field">
         <th scope="row"><label for="album_artist">Artista</label></th>
         <td>
            <input id="album_artist" name="album_artist" type="text" value="{$album_artist}" size="40" />
         </td>
      </tr>
      <tr class="form-field">
         <th scope="row"><label for="album_year">Ano de lançamento</label></th>
         <td>
            <input id="album_year" name="album_year" type="number" value="{$album_year}" size="40" min="1899" max="2199" />
         </td>
      </tr>
      <tr class="form-field">
         <th scope="row"><label for="album_producer">Produtor</label></th>
         <td>
            <input id="album_producer" name="album_producer" type="text" value="{$album_producer}" size="40" />
         </td>
      </tr>
      <tr class="form-field">
         <th scope="row"><label for="album_cover">Capa do Álbum</label></th>
         <td>
            <div class="cover-preview">{$album_thumbnail}</div>
            <input id="album_cover" name="album_cover" type="hidden" value="{$album_cover}" size="40" />
            <button type="button" class="button" data-media-uploader-target="#album_cover" data-media-thumbnail-target=".cover-preview">Selecionar imagem</button>
         </td>
      </tr>
      HTML;
   }

   public function handle_assets($suffix): void
   {
      if (!in_array($suffix, ['edit-tags.php', 'term.php'])) {
         return;
      }

      wp_enqueue_media();
      wp_register_script('media_modal', DBANDS_PLUGIN_URL . 'assets/media_modal.js', ['jquery']);
      wp_localize_script(
         'media_modal',
         'meta_image',
         [
            'title'  => esc_html__('Selecione ou envie uma imagem'),
            'button' => esc_html__('Select'),
         ],
      );
      wp_enqueue_script('media_modal');
   }

   public function register(): void
   {
      register_taxonomy('albuns', 'lyric', [
         'labels' => [
            'name'          => 'Álbuns',
            'singular_name' => 'Álbum',
            'edit_item'     => 'Editar álbum',
            'add_new_item'  => 'Adicionar novo álbum',
         ],
         'public'             => true,
         'publicly_queryable' => false,
         'show_in_nav_menus'  => false,
         'show_tagcloud'      => false,
         'show_in_rest'       => false,
         'rewrite'            => false,
      ]);
   }

   public function save_fields($album_ID)
   {
      if (empty($_POST)) {
         return;
      }

      check_admin_referer('album_edit', '_wpnonce_album');

      if (!current_user_can('edit_term', $album_ID)) {
         return $album_ID;
      }

      $keys = ['album_artist', 'album_year', 'album_cover', 'album_producer'];

      foreach ($keys as $key) {
         $value = $_POST[$key];

         if (empty($value) && !is_numeric($value)) {
            delete_term_meta($album_ID, $key);

            continue;
         }
         update_term_meta($album_ID, $key, trim($value));
      }
   }
}
