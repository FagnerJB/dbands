<?php

use dbp\News\News;

$News = new News();

?>
<div class="container-content">
   <main>
      <article id="single-<?php echo $News->ID; ?>" <?php post_class('content'); ?>>
         <h1 class="mb-7"><?php echo $News->get('title'); ?></h1>
         <div class="text-justify">
            <?php echo $News->get('content'); ?>
         </div>
      </article>
   </main>
   <aside>
      <?php

      get_page_component('single', 'aside');

?>
   </aside>
</div>
