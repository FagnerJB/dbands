<?php

namespace dbp\Common;

class Register_Menu
{
   public function __construct()
   {
      add_action('save_post_nav_menu_item', [$this, 'save_nav_menu']);
      add_action('wp_nav_menu_item_custom_fields', [$this, 'add_menu_custom_field']);
      add_action('after_setup_theme', [$this, 'register_menus']);

      add_filter('page_menu_link_attributes', [$this, 'add_menu_class']);
      add_filter('nav_menu_link_attributes', [$this, 'add_menu_target'], 10, 2);
      add_filter('nav_menu_item_title', [$this, 'add_menu_icon'], 10, 2);
      add_filter('wp_nav_menu_items', [$this, 'add_menu_button'], 10, 2);
   }

   public function register_menus()
   {
      register_nav_menus(array(
         'about_pages'    => esc_html__('Páginas - Sobre', 'dbands'),
         'social_links'   => esc_html__('Links - Redes sociais', 'dbands'),
         // 'partners_links' => esc_html__('Links - Parceiros', 'dbands'),
         'api_links'      => esc_html__('Links - API', 'dbands')
      ));
   }


   public function add_menu_class($atts)
   {
      $atts['class'] = 'btn-alt';

      return $atts;
   }

   public function add_menu_custom_field($item_id)
   {
      $icon = get_post_meta($item_id, 'menu_icon', true);
      $icon = $icon ? $icon : "";

      $new_tab = get_post_meta($item_id, 'menu_newtab', true);
      $new_tab = $new_tab ? "checked" : "";

      echo <<<OPTIONS
      <p class="description description-wide">
         <label for="edit-menu-item-icon-$item_id">
            Classe do ícone<br>
            <input type="text" id="edit-menu-item-icon-$item_id" class="widefat edit-menu-item-icon" name="menu-item-icon[$item_id]" value="$icon">
         </label>
      </p>
      <p class="description description-wide">
         <label for="edit-menu-item-newtab-$item_id">
            Abrir em nova janela<br>
            <input type="checkbox" id="edit-menu-item-newtab-$item_id" class="widefat edit-menu-item-newtab" name="menu-item-newtab[$item_id]" value="true" $new_tab>
         </label>
      </p>
OPTIONS;
   }

   public function add_menu_button($items, $args)
   {
      if (false !== strpos($args->menu_class, 'menu-btns')) {
         $items = str_replace('<a', '<a class="btn-alt !gap-2"', $items);
      }

      return $items;
   }





   public function save_nav_menu()
   {
      if (!isset($_POST['menu-item-icon']) || !isset($_POST['menu-item-newtab'])) {
         return;
      }

      foreach ($_POST['menu-item-icon'] as $post_id => $icon) {
         if (empty($icon)) {
            delete_post_meta($post_id, 'menu_icon');
         } else {
            update_post_meta($post_id, 'menu_icon', $icon);
         }
      }

      foreach ($_POST['menu-item-newtab'] as $post_id => $newtab) {
         if (empty($newtab)) {
            delete_post_meta($post_id, 'menu_newtab');
         } else {
            update_post_meta($post_id, 'menu_newtab', $icon);
         }
      }
   }

   public function add_menu_icon($title, $item)
   {
      $icon_class = get_post_meta($item->ID, 'menu_icon', true);

      if ($icon_class) {
         return "<i class='$icon_class'></i> <span class='menu-item-text'>$title</span>";
      } else {
         return $title;
      }
   }

   public function add_menu_target($atts, $item)
   {
      $target_attr = get_post_meta($item->ID, 'menu_newtab', true);

      if ($target_attr) {
         $atts['target'] = '_blank';
      }

      return $atts;
   }
}
