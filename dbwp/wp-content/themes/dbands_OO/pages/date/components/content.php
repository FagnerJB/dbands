<?php

global $wp_locale;

use dbp\Common\Utils;

$page  = Utils::get_page();
$year  = get_query_var('year');
$month = get_query_var('monthnum');

?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <section>
      <?php

      if (1 === $page || !wp_is_serving_rest_request()) {
         ?>
         <header class="mb-7">
            <h1><?php printf('Em %s de %d', $wp_locale->get_month($month), $year); ?></h1>
         </header>
      <?php

      }

if (have_posts()) {
   get_component('loop-posts');
} else {
   get_component('loop-empty');
}

?>
   </section>
   <aside>
      <?php

if (1 === $page || !wp_is_serving_rest_request()) {
   get_page_component('date', 'aside');
}

?>
   </aside>
</div>
<?php

get_component('pagination', [
   'link' => '',
]);

?>
