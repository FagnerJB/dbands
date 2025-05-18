<?php

namespace dbp\Common;

class Register_Search
{
   public function __construct()
   {
      add_action('init', [$this, 'add_rewrite']);
      add_action('init', [$this, 'add_redirect']);

      add_filter('get_search_query', [$this, 'clean_search']);
      add_filter('search_link', [$this, 'change_search_link'], 10, 2);
   }

   public function add_redirect(): void
   {
      if (wp_is_serving_rest_request() || wp_doing_ajax()) {
         return;
      }

      $s = $_GET['s'] ?? false;

      if (!$s) {
         return;
      }

      $search_type = $_GET['search_type'] ?? 'site';
      $page        = $_GET['paged']       ?? 0;
      $url         = Utils::get_search_link($s, $search_type, $page);

      if (wp_safe_redirect($url)) {
         exit;
      }
   }

   public function change_search_link($_, $search_query)
   {
      $search_type = get_query_var('search_type', 'site');

      return Utils::get_search_link($search_query, $search_type);
   }

   public function add_rewrite(): void
   {
      // TV
      add_rewrite_tag('%video%', '([a-zA-Z0-9-_]{11})');
      add_rewrite_tag('%playlist%', '([a-zA-Z0-9-_]{18,34})');
      add_rewrite_rule('^tv/([^/]*)/([^/]*)?', 'index.php?page_id=8261&video=$matches[1]&playlist=$matches[2]', 'top');
      add_rewrite_rule('^tv/([^/]*)/?', 'index.php?page_id=8261&video=$matches[1]', 'top');

      // SEARCH
      add_rewrite_tag('%search_type%', '([a-z]{3,7})');

      $search_options = Utils::get_search_options();

      foreach ($search_options as $key => $search_option) {
         if ('site' === $key) {
            continue;
         }

         add_rewrite_rule("^busca/{$key}/([^/]*)/?", 'index.php?search_type=' . $key . '&s=$matches[1]', 'top');
      }

      add_rewrite_rule('^busca/([^/]*)/page/([0-9]*)?', 'index.php?paged=$matches[2]&s=$matches[1]', 'top');
      add_rewrite_rule('^busca/([^/]*)/?', 'index.php?s=$matches[1]', 'top');
      add_rewrite_rule('^busca/?', 'index.php?s=', 'top');

      // SPOTIFY
      add_rewrite_tag('%tool%', '([a-z]{3,7})');
      add_rewrite_rule('^spotify/([^/]*)/?', 'index.php?page_id=11768&tool=$matches[1]', 'top');
   }

   public function clean_search($text)
   {
      $text = trim($text);
      $text = substr($text, 0, 66);
      $text = remove_accents($text);

      return strtolower($text);
   }
}
