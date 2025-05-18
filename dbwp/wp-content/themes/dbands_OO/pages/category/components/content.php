<?php

use dbp\Category\Category;
use dbp\Common\Utils;

$Category = new Category();
$page     = Utils::get_page();

?>
<div id="page-<?php echo $page ?>" class="container-content">
   <section>
      <?php

      if ($page === 1 || !wp_is_serving_rest_request()) {

      ?>
         <header class="mb-7">
            <h1>
               <i class="fas fa-hashtag"></i>
               <?php echo $Category->get('name') ?>
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
         get_page_component('category', 'aside');
      }

      ?>
   </aside>
</div>
<?php

get_component('pagination', [
   'link' => $Category->get('link'),
]);

?>
