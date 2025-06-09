<?php

use dbp\Author\Author;
use cavWP\Utils;

$Author = new Author();
$page   = Utils::get_page();

?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <main>
      <?php

      if (1 === $page || !wp_is_serving_rest_request()) {
         ?>
         <h1 class="mb-7">
            <?php printf(esc_html__('Publicações de %s', 'dbands'), $Author->get('display_name')); ?>
         </h1>
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
      get_page_component('author', 'aside');
   }

?>
   </aside>
</div>
<?php

latte_component('pagination', [
   'link'      => $Author->get('link'),
   'max_pages' => $wp_query->max_num_pages,
]);

?>
