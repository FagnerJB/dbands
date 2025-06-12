<?php

global $wp_locale;

use cavWP\Utils;

$page  = Utils::get_page();
$year  = get_query_var('year');
$month = get_query_var('monthnum');

?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <main>
      <?php

      if (1 === $page || !wp_is_serving_rest_request()) {
         ?>
      <h1 class="mb-7">
         <?php printf('%s de %d', $wp_locale->get_month($month), $year); ?>
      </h1>
      <h2 class="sr-only">
         <?php printf('Publicações em %s de %d', $wp_locale->get_month($month), $year); ?>
      </h2>
      <?php

      }

if (have_posts()) {
   get_component('loop-posts');
} else {
   get_component('loop-empty');
}

?>
   </main>
   <aside>
      <?php

if (1 === $page || !wp_is_serving_rest_request()) {
   get_page_component('date', 'aside');
}

?>
   </aside>
</div>
<?php

latte_component('pagination', [
   'link'      => Utils::get_date_link(true, true),
   'max_pages' => $wp_query->max_num_pages,
]);

?>
