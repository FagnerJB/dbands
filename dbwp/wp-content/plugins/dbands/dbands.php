<?php

namespace dbp;

/*
 * Plugin Name: Essencial para Deutsche Bands
 * Plugin URI: https://dbands.com.br/
 * Description: Recursos para o site do Deutsche Bands.
 * Version: 2.0
 * Author: Fagner JB.
 * Author URI: https://fagnerjb.com/
 */

define('DBANDS_PLUGIN_FILE', __FILE__);
define('DBANDS_PLUGIN_URL', plugin_dir_url(__FILE__));

add_action('plugins_loaded', 'dbp\start_autoloader');
function start_autoloader(): void
{
   $AutoLoader = \cav_autoloader();
   $AutoLoader->add_namespace('dbp', implode(DIRECTORY_SEPARATOR, [plugin_dir_path(DBANDS_PLUGIN_FILE), 'entities']));

   new Common\Register();
   new Common\Register_Search();
   new Common\Register_Menu();
   new Common\Register_Endpoints();
   new Common\Register_Doodles();

   new Band\Register();
   new Lyric\Register();
   new Category\Register();
   new TV\Register();
   new TV\Admin_Page();
   new Album\Register();
   new Author\Register();
   new Ad\Register();
   new Services\Spotify\Register();
}
