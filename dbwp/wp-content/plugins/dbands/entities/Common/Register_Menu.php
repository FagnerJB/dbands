<?php

namespace dbp\Common;

class Register_Menu
{
   public function __construct()
   {
      add_action('after_setup_theme', [$this, 'register_menus']);

      add_filter('page_menu_link_attributes', [$this, 'add_menu_class']);
      add_filter('wp_nav_menu_items', [$this, 'add_menu_button'], 10, 2);
   }

   public function add_menu_button($items, $args)
   {
      if (str_contains($args->menu_class, 'menu-btns')) {
         $items = str_replace('<a', '<a class="btn-alt !gap-2"', $items);
      }

      return $items;
   }

   public function add_menu_class($atts)
   {
      $atts['class'] = 'btn-alt';

      return $atts;
   }

   public function register_menus(): void
   {
      register_nav_menus([
         'about_pages'  => esc_html__('Páginas - Sobre', 'dbands'),
         'social_links' => esc_html__('Links - Redes sociais', 'dbands'),
         'api_links'    => esc_html__('Links - API', 'dbands'),
      ]);
   }
}
