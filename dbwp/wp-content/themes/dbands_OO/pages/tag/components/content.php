<?php

use dbp\Band\Band;
use dbp\Common\Utils;

$page = Utils::get_page();
$Band = new Band();

?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <section>
      <?php

      if (1 === $page || !wp_is_serving_rest_request()) {
         ?>
         <header class="mb-7">
            <h1>
               <?php

                  printf(esc_html__('Publicações sobre %s', 'dbands'), $Band->get('name'));

         ?>
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
   get_page_component('tag', 'aside');
}

?>
   </aside>
</div>
<?php

get_component('pagination', [
   'link' => $Band->get('link'),
]);

?>
