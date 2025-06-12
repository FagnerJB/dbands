<?php

use cavWP\Utils;
use dbp\Band\Band;

$page = Utils::get_page();
$Band = new Band();

?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <main>
      <?php

      if (1 === $page || !wp_is_serving_rest_request()) {
         ?>
      <header class="mb-7">
         <h1>
            <?php echo $Band->get('name'); ?>
         </h1>
         <h2 class="sr-only">
            <?php printf(esc_html__('Publicações sobre %s', 'dbands'), $Band->get('name')); ?>
         </h2>
      </header>
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
   get_page_component('tag', 'aside');
}

?>
   </aside>
</div>
<?php

latte_component('pagination', [
   'link'      => $Band->get('link'),
   'max_pages' => $wp_query->max_num_pages,
]);

?>
