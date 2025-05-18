<?php

use dbp\News\News;

$News = new News();

?>
<div class="container-content">
   <section>
      <article id="single-<?php echo $News->ID ?>" <?php post_class('content') ?>>
         <header class="mb-7">
            <h1><?php echo $News->get('title') ?></h1>
         </header>
         <div class="text-justify">
            <?php echo $News->get('content'); ?>
         </div>
      </article>
   </section>
   <aside>
      <?php

      get_page_component('single', 'aside');

      ?>
   </aside>
</div>
