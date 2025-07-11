<?php

namespace dbp\Band;

class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'rename_labels']);
      add_action('post_tag_term_edit_form_tag', [$this, 'add_form_enctype']);
      add_action('post_tag_add_form_fields', [$this, 'add_fields']);
      add_action('post_tag_edit_form_fields', [$this, 'edit_fields']);
      add_action('created_post_tag', [$this, 'save_fields']);
      add_action('edit_post_tag', [$this, 'save_fields']);
      add_action('delete_term', [$this, 'delete'], 10, 4);

      add_filter('manage_edit-post_tag_columns', [$this, 'add_columns']);
      add_filter('manage_post_tag_custom_column', [$this, 'columns_content'], 10, 3);
   }

   public function add_columns($columns)
   {
      $columns_new = [
         'images' => esc_html__('Imagens', 'dbands'),
         'social' => esc_html__('Links', 'dbands'),
         'info'   => esc_html__('Informações', 'dbands'),
      ];
      $columns_start = array_splice($columns, 0, 2);
      $columns       = array_merge($columns_start, $columns_new, $columns);

      $columns['description'] = esc_html__('Minibiografia', 'dbands');

      return $columns;
   }

   public function add_fields(): void
   {
      foreach (Utils::get_metas() as $key => $details) {
         $required      = isset($details['required']) ? 'form-required' : '';
         $type          = $details['type'] ?? 'text';
         $required_attr = isset($details['required']) ? 'required="true"' : '';

         echo <<<HTML
         <div class="form-field {$required}">
            <label for="{$key}">{$details['name']}</label>
            <input id="{$key}" name="{$key}" type="{$type}" value size="40" aria-required="true" {$required_attr}>
            <p>{$details['description']}.</p>
         </div>
         HTML;
      }
   }

   public function add_form_enctype(): void
   {
      echo ' enctype="multipart/form-data"';
   }

   public function columns_content($content, $column, $tag_ID)
   {
      $Band = new Band($tag_ID);

      switch ($column) {
         case 'images':
            $content = '<div style="display:flex;flex-direction:column;gap:2px;font-size:16px">';

            if (!empty($Band->has_file('cover'))) {
               $content .= '<span>✅ Capa</span>';
            }

            if (!empty($Band->has_file('logo'))) {
               $content .= '<span>✅ Logo</span>';
            }

            $content .= '</div>';

            break;

         case 'social':
            $content = '<div style="display:flex;flex-flow:\'row wrap\';gap:5px;font-size:18px">';

            foreach (['site', 'spotify', 'itunes', 'deezer', 'youtube', 'facebook', 'instagram', 'twitter', 'lastfm', 'musixmatch'] as $meta) {
               $content .= $Band->get_meta_link($meta, false);
            }
            $content .= '</div>';

            break;

         case 'info':
            $content = $Band->get_genre();
            $content .= '<div style="display:flex;flex-flow:\'row wrap\';gap:5px;font-size:18px;margin-top:5px;">';

            foreach (['active_years', 'city', 'photo_credits'] as $meta) {
               $content .= $Band->get_meta_link($meta, false);
            }

            $content .= '</div>';
            break;

         default:
            break;
      }

      return $content;
   }

   public function delete($_term_ID, $_tax_ID, $type, $term): void
   {
      if ('post_tag' !== $type) {
         return;
      }

      add_filter('upload_dir', 'dbp\Band\Utils::change_upload_folder', 9);

      $upload_path = wp_upload_dir()['path'];

      if (file_exists("{$upload_path}/covers/{$term->slug}.jpg")) {
         unlink("{$upload_path}/covers/{$term->slug}.jpg");
      }

      if (file_exists("{$upload_path}/logos/{$term->slug}.png")) {
         unlink("{$upload_path}/logos/{$term->slug}.png");
      }

      remove_filter('upload_dir', 'dbp\Band\Utils::change_upload_folder', 9);
   }

   public function edit_fields($term): void
   {
      $Band = new Band($term);

      ?>
<tr class="form-field">
   <th scope="row">Prévia</th>
   <td>
      <a class="button"
         href="<?php $Band->get('link'); ?>"
         target="_blank">Visualizar página deste artista</a>
   </td>
</tr>
<tr class="form-field">
   <th scope="row"><label for="cover">Imagem do cabeçalho</label></th>
   <td>
      <?php if ($Band->has_file('cover')) { ?>
      <div class="row-cover">
         <img src="<?php echo $Band->get_cover(); ?>" width="100%"
              loading="lazy" />
      </div>
      <?php } ?>
      <input id="cover" name="cover" type="file"
             accept="image/png,image/x-png,image/x-citrix-png,image/bmp,image/jpeg,image/x-citrix-jpeg,image/tiff">
      <p class="description">Atualizar ou adicionar imagem de cabeçalho. <a class="button edit-attachment"
            href="https://www.photopea.com/"
            target="_blank"><?php esc_html_e('Preparar imagem', 'dbands'); ?></a>.
         Se a imagem não for exatamente <strong>960 x 300</strong>, será redimensionada e cortada para tal e convertida
         para JPEG.</p>
   </td>
</tr>
<tr class="form-field">
   <th scope="row"><label for="cover">Logotipo</label></th>
   <td>
      <?php

      if ($Band->has_file('logo')) {
         ?>
      <div class="row-logo">
         <?php echo $Band->get_logo(); ?>
      </div>
      <?php

      }

      ?>
      <input id="logo" name="logo" type="file"
             accept="image/png,image/x-png,image/x-citrix-png,image/bmp,image/jpeg,image/x-citrix-jpeg,image/tiff">
      <p class="description">Atualizar ou adicionar logotipo. <a class="button" href="https://www.photopea.com/"
            target="_blank"><?php esc_html_e('Preparar a imagem', 'dbands'); ?></a>
         salvando em PNG 8, escala de cinza, com 64 cores. Se a <strong>largura</strong> do logotipo não for
         <strong>160</strong> com altura variável, será redimensionada para tal e convertida para PNG.
      </p>
   </td>
</tr>
<?php

      foreach (Utils::get_metas() as $key => $details) {
         $value        = get_term_meta($term->term_id, $key, true);
         $required     = '';
         $autocomplete = '';

         if (isset($details['required'])) {
            $required = 'required="true" aria-required="true"';
         }

         if (!in_array($key, ['band_genre', 'band_city', 'band_photo_credits'])) {
            $autocomplete = ' autocomplete="off"';
         }

         ?>
<tr class="form-field <?php if (isset($details['required'])) {
   echo 'form-required';
} ?>">
   <th scope="row"><label
             for="<?php echo $key; ?>"><?php echo $details['name']; ?></label>
   </th>
   <td>
      <input id="<?php echo $key; ?>"
             name="<?php echo $key; ?>"
             type="<?php echo (isset($details['type'])) ? $details['type'] : 'text'; ?>"
             value="<?php echo $value; ?>" size="40"
             <?php echo $required . $autocomplete; ?>>
      <?php

      if (!empty($value) && isset($details['type']) && 'url' === $details['type']) {
         ?>
      <a class="button" href="<?php echo $value; ?>"
         target="_blank">Verificar página</a>
      <?php

      }

         ?>
      <p class="description">
         <?php echo $details['description']; ?>.
      </p>
   </td>
</tr>
<?php

      }
   }

   public function rename_labels(): void
   {
      global $wp_taxonomies;

      $new_labels = [
         'name'                  => 'Bandas',
         'singular_name'         => 'Banda',
         'menu_name'             => 'Bandas',
         'all_items'             => 'Todas as bandas',
         'edit_item'             => 'Editar banda',
         'view_item'             => 'Ver banda',
         'update_item'           => 'Atualizar banda',
         'add_new_item'          => 'Adicionar banda',
         'new_item_name'         => 'Nova banda',
         'search_items'          => 'Buscar banda',
         'popular_items'         => 'Bandas populares',
         'add_or_remove_items'   => 'Adicionar ou remover bandas',
         'choose_from_most_used' => 'Escolher das mais usadas bandas',
         'not_found'             => 'Nenhum banda encontrada',
         'back_to_items'         => 'Voltar para bandas',
         'no_terms'              => 'Sem bandas',
         'items_list_navigation' => 'Bandas',
         'items_list'            => 'Bandas',
      ];

      $wp_taxonomies['post_tag']->labels = (object) array_merge((array) $wp_taxonomies['post_tag']->labels, $new_labels);

      $wp_taxonomies['post_tag']->label = 'Bandas';
   }

   public function save_fields($term_id): void
   {
      add_filter('upload_dir', 'dbp\Band\Utils::change_upload_folder', 9);

      $tag = get_term_by('id', $term_id, 'post_tag');

      foreach ($_FILES as $type => $details) {
         if (!empty($_FILES[$type]['name'])) {
            global $upload_subdir;
            $upload_subdir = $type . 's';

            $extension         = ('cover' === $type) ? '.jpg' : '.png';
            $filename_original = $tag->slug . '_original' . $extension;
            $current_upload    = wp_upload_bits($filename_original, null, @file_get_contents($details['tmp_name']));
            $filename_target   = wp_upload_dir()['path'] . '/' . $tag->slug . $extension;
            $current_image     = wp_get_image_editor($current_upload['file']);

            if (!is_wp_error($current_image)) {
               $size = $current_image->get_size();

               if ('cover' === $type) {
                  if (960 !== $size['width'] || 300 !== $size['height']) {
                     $current_image->resize(960, 300, true);
                  }

                  $current_image->set_quality(80);
                  $current_image->save($filename_target, 'image/jpeg');
               } elseif ('logo' === $type) {
                  if (160 !== $size['width']) {
                     $current_image->resize(160, null, false);
                  }

                  $current_image->save($filename_target, 'image/png');
               }
               unlink($current_upload['file']);
            }
         }
      }

      foreach (Utils::get_metas() as $key => $details) {
         if (!empty($_POST[$key])) {
            $value = (isset($details['type']) && 'url' === $details['type']) ? esc_url($_POST[$key]) : sanitize_text_field($_POST[$key]);
            update_term_meta($term_id, $key, $value);
         } else {
            delete_term_meta($term_id, $key);
         }
      }

      remove_filter('upload_dir', 'dbp\Band\Utils::change_upload_folder', 9);
   }
}
?>
