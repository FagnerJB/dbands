<?php

namespace dbp\News;

class Register
{
   public function __construct()
   {
      add_action('add_meta_boxes', [$this, 'add_fields']);
      add_action('save_post_post', [$this, 'save_fields']);
   }

   public function add_fields(): void
   {
      add_meta_box('dbands_post_fields', esc_html__('Nas redes sociais', 'bb_kes'), [$this, 'fields_content'], ['post'], 'side', 'high');
   }

   public function fields_content($post): void
   {
      $facebook_link   = get_post_meta(11648, '_menu_item_url', true);
      $twitter_link    = get_post_meta(11651, '_menu_item_url', true);
      $social_postsIDs = get_post_meta($post->ID, 'social_postIDs', true);
      $facebook_ID     = $social_postsIDs['fb'] ?? '';
      $twitter_ID      = $social_postsIDs['tw'] ?? '';

      echo <<<SOCIALS
            <p><strong>Facebook</strong></p>
            <p>
               <a href="{$facebook_link}" target="_blank">dbands no Facebook</a> &bull;
               <a href="{$facebook_link}/posts/{$facebook_ID}" target="_blank">Testar</a>
            </p>
            <p>
               <label for="postFB">ID do post (/posts/*)</label><br>
               <input id="postFB" class="components-text-control__input" type="text" name="postFB" value="{$facebook_ID}">
            </p>
            <p><strong>Twitter</strong></p>
            <p>
               <a href="{$twitter_link}" target="_blank">dbands no Twitter</a> &bull;
               <a href="{$twitter_link}/status/{$twitter_ID}" target="_blank">Testar</a>
            </p>
            <p>
               <label for="postTW">ID do tweet (/status/*)</label><br>
               <input id="postTW" class="components-text-control__input" type="text" name="postTW" value="{$twitter_ID}">
            </p>
      SOCIALS;
   }

   public function save_fields($post_ID): void
   {
      if (wp_is_post_revision($post_ID) || !current_user_can('edit_post', $post_ID)) {
         return;
      }

      if (!empty($_POST['postFB']) || !empty($_POST['postTW'])) {
         if (!empty($_POST['postFB'])) {
            $update['fb'] = $_POST['postFB'];
         }

         if (!empty($_POST['postTW'])) {
            $update['tw'] = $_POST['postTW'];
         }

         update_post_meta($post_ID, 'social_postIDs', $update);
      }
   }
}
