<?php

use dbp\Author\Author;
use dbp\Common\Utils;

$Author = new Author();
$page   = Utils::get_page();

?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <section>
      <?php

      if (1 === $page || !wp_is_serving_rest_request()) {
         ?>
      <header class="mb-7">
         <h1>
            <?php printf(esc_html__('Publicações de %s', 'dbands'), $Author->get('display_name')); ?>
         </h1>
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
