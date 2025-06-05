<?php

namespace dbp\Common;

class Register_Contribua
{
   public function __construct()
   {
      add_action('wp_ajax_contribua_save', [$this, 'ajax_contribua_save']);
      add_action('wp_ajax_nopriv_contribua_save', [$this, 'ajax_contribua_save']);

      add_action('wp_enqueue_scripts', [$this, 'handle_assets']);
   }

   public function ajax_contribua_save(): void
   {
      check_ajax_referer('form_new_post_draft');

      $name  = sanitize_text_field($_POST['post_author']);
      $email = sanitize_email($_POST['post_email']);

      if (is_user_logged_in()) {
         $user_ID = get_current_user_id();
      } else {
         $user    = get_user_by('email', $email);
         $user_ID = is_a($user, 'WP_User') ? $user->ID : 45;
      }

      $post_tags = array_filter($_POST['post_tags'], fn($tag_ID) => is_numeric($tag_ID));

      $post_tags = array_map(fn($tag_ID) => (int) $tag_ID, $post_tags);

      $post_ID = wp_insert_post([
         'post_author'   => $user_ID,
         'post_content'  => $_POST['post_content'],
         'post_title'    => $_POST['post_title'],
         'post_excerpt'  => $_POST['post_except'],
         'post_category' => $_POST['post_category'],
         'tags_input'    => $post_tags,
         'meta_input'    => [
            'guess_name'  => $name,
            'guess_email' => $email,
         ],
      ]);

      wp_send_json([
         'postID' => $post_ID,
      ]);
   }

   public function handle_assets(): void
   {
      if (is_page('contribua')) {
         wp_enqueue_media();
         // https://gist.github.com/dbspringer/8661122

         wp_enqueue_style(
            'select2',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
         );
         wp_enqueue_script(
            'select2',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ['jquery-g'],
            false,
            true,
         );

         wp_register_script('media_modal', DBANDS_PLUGIN_URL . 'assets/media_modal.js', ['jquery-g']);
         wp_localize_script('media_modal', 'meta_image', [
            'title'  => esc_html__('Selecione ou envie uma imagem'),
            'button' => esc_html__('Select'),
         ]);
         wp_enqueue_script('media_modal');
      }
   }
}
