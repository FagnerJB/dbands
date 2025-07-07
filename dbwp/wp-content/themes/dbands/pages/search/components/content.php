<?php

use cavWP\Utils as CavUtils;
use dbp\Common\Utils;
use dbp\Services\Lastfm;
use dbp\Services\Youtube;

$page        = CavUtils::get_page();
$search_term = get_search_query();
$search_type = get_query_var('search_type', 'site');

$search_options = Utils::get_search_options();

if (!in_array($search_type, array_keys($search_options))) {
   exit;
}

if (!is_bot() && in_array($search_type, ['artista', 'tag', 'usuario', 'videos'])) {
   do_action('log_activity', 'search_api', details: [
      'search_type' => $search_type,
      'search_term' => $search_term,
   ]);

   if ('videos' === $search_type) {
      $youtube = new Youtube();
      $items   = $youtube->search([
         'q'          => $search_term,
         'maxResults' => 48,
      ]);
   } else {
      $LastFm = new Lastfm();

      if ('artista' === $search_type) {
         $lastfm = $LastFm->get_artist($search_term);
      } elseif ('tag' === $search_type) {
         $lastfm = $LastFm->get_tag($search_term);
      } elseif ('usuario' === $search_type) {
         $lastfm = $LastFm->get_user($search_term);
      }

      if (false !== $lastfm) {
         $items = $lastfm['items'];
      }
   }
}

?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <section>
      <?php if (1 === $page || !wp_is_serving_rest_request()) {          ?>
      <header class="mb-7">
         <h1>
            <?php if (empty($search_term)) {
               esc_html_e('Faça uma busca', 'dbands');
            } else {
               printf(esc_html__('Resultados para %s', 'dbands'), $search_term);
            }         ?>
         </h1>
         <div>
            <?php echo $search_options[$search_type]['search_page']; ?>
         </div>
      </header>
      <?php } ?>
      <?php if (!have_posts() || empty($items) && 'site' !== $search_type) {
         get_component('loop-empty');
      } else {
         if ('site' === $search_type) {
            get_component('loop-posts');
         } elseif (in_array($search_type, ['artista', 'tag', 'usuario'])) {
            get_component('loop-lastfm', [
               'search_type' => $search_type,
               'card'        => $lastfm,
            ]);
         } elseif ('videos' === $search_type) {
            get_component('loop-videos', [
               'videos' => $items,
            ]);
         }
      } ?>
   </section>
</div>
<?php latte_component('pagination', [
   'link'      => get_search_link(get_search_query()),
   'max_pages' => $wp_query->max_num_pages,
]); ?>
