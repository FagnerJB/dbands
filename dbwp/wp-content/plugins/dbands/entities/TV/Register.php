<?php

namespace dbp\TV;

use dbp\Services\Youtube;

final class Register
{
   public function __construct()
   {
      add_action('init', [$this, 'add_rewrite']);
      add_action('template_redirect', [$this, 'do_redirect']);
      add_action('refresh_tv_hook', [$this, 'refresh_tv']);

      if (!wp_next_scheduled('refresh_tv_hook')) {
         wp_schedule_event(strtotime('next 03:00'), 'daily', 'refresh_tv_hook');
      }
   }

   public function add_rewrite(): void
   {
      add_rewrite_rule('^watch/?', 'index.php?cav=youtube', 'top');
      add_rewrite_rule('^playlist/?', 'index.php?cav=youtube', 'top');
   }

   public function do_redirect(): void
   {
      $cav_template = get_query_var('cav', false);

      if ('youtube' !== $cav_template) {
         return;
      }

      $url = home_url('tv');

      if (isset($_GET['list'], $_GET['v'])) {
         $url .= '/' . $_GET['v'] . '/' . $_GET['list'];
      } elseif (isset($_GET['list'])) {
         $url .= '/' . $_GET['list'];
      } elseif (isset($_GET['v'])) {
         $url .= '/' . $_GET['v'];
      }

      if (wp_safe_redirect($url)) {
         exit;
      }
   }

   public function refresh_tv(): void
   {
      $dbtv = new Youtube(YT_CHANNEL_ID);
      $dbtv->get_feed();
   }
}
