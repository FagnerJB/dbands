<?php

namespace dbt;

add_filter('cav_cdn_hostname', fn() => 'dbands.com.br');
add_filter('cav_cdn_file_extensions', fn() => 'jpg|png|jpeg|gif|mp4');

add_action('wp_loaded', 'dbt\load_theme');
function load_theme(): void
{
   $AutoLoader = \cav_autoloader();
   $AutoLoader->add_namespace('dbt', implode(DIRECTORY_SEPARATOR, [__DIR__, 'entities']));

   new Common\Register();
   new Shortcodes\group_bands();

   add_action('admin_init', 'dbt\once_stuff');
   function once_stuff(): void
   {
      remove_image_size('2048x2048');
      remove_image_size('1536x1536');
   }
}
