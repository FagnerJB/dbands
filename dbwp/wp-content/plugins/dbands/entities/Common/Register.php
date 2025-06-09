<?php

namespace dbp\Common;

class Register
{
   public function __construct()
   {
      add_post_type_support('page', 'excerpt');

      add_action('wp_enqueue_scripts', [$this, 'remove_wp_assets'], 15);
      add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
      add_action('cav_head_metas', [$this, 'prints_head_tags']);

      add_filter('get_custom_logo_image_attributes', [$this, 'add_logo_attrs']);
      add_filter('upload_mimes', [$this, 'remove_upload_types'], 10, 2);
   }

   public function add_logo_attrs($custom_logo_attrs)
   {
      $custom_logo_attrs['title'] = 'Ir para página inicial';

      return $custom_logo_attrs;
   }

   public function enqueue_styles(): void
   {
      wp_enqueue_style('fontawesome');
   }

   public function prints_head_tags(): void
   {
      echo '<link rel="alternate" type="application/rss+xml" title="Últimas notícias" href="' . get_post_meta(11649, '_menu_item_url', true) . '"/>';
   }

   public function remove_upload_types($types, $user)
   {
      if (user_can($user, 'manage_options')) {
         return $types;
      }

      $types = array_filter($types, fn($type) => !preg_match('/(text|video|application)\//', $type));

      if (user_can($user, 'edit_posts')) {
         return $types;
      }

      return array_filter($types, fn($type) => !preg_match('/(audio)\//', $type));
   }

   public function remove_wp_assets(): void
   {
      wp_deregister_style('admin-bar');
      wp_deregister_style('wp-block-library');
      wp_deregister_style('wpcom-notes-admin-bar');

      wp_deregister_script('admin-bar');
      wp_deregister_script('wp-embed');
      wp_deregister_script('backbone');
      wp_deregister_script('mustache');
      wp_deregister_script('underscore');
      wp_deregister_script('jquery-core');
      wp_deregister_script('hoverintent-js');
      wp_deregister_script('jquery-migrate');
      wp_deregister_script('wpcom-notes-common');
      wp_deregister_script('wpcom-notes-admin-bar');
      wp_deregister_script('jetpack-scan-show-notice');
   }
}
