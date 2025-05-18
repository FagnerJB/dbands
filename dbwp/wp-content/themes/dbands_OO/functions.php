<?php

namespace dbt;

add_filter('wpe_cdn_hostname', function () {
   return 'dbands.com.br';
});

add_filter('wpe_cdn_file_extensions', function () {
   return 'jpg|png|jpeg|gif|mp4';
});

$AutoLoader = \wpe_autoloader();
$AutoLoader->add_namespace('dbt', implode(DIRECTORY_SEPARATOR, [__DIR__, 'entities']));

new Common\Register();
new Shortcodes\all_lyrics();
new Shortcodes\group_bands();

add_action('admin_init', 'dbt\once_stuff');
function once_stuff()
{
   remove_image_size('2048x2048');
   remove_image_size('1536x1536');
}
