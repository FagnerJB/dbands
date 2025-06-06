<?php

use cavWP\Models\Post;

$classes = 'grid-cols-2';

if (is_search()) {
   $classes = 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4';
}

if (is_home()) {
   $classes = 'grid-cols-1 md:grid-cols-2';
}

?>
<ul class="grid auto-rows-auto gap-x-1 md:gap-x-2 gap-y-3 md:gap-y-5 content-start <?php echo $classes; ?>">
   <?php

   while (have_posts()) {
      the_post();

      $Post = new Post();
      $type = $Post->get('type');

      get_component("item-{$type}");
   }

wp_reset_postdata();

?>
</ul>
