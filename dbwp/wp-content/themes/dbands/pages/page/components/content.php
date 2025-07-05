<?php

use dbp\Page\Page;

$Page = new Page();

the_post();

?>
<div class="container-content">
   <main>
      <article id="page-<?php echo $Page->ID; ?>" <?php post_class('content'); ?>>
         <h1 class="mb-7">
            <?php echo $Page->get('title'); ?>
         </h1>
         <div class="text-justify">
            <?php echo $Page->get('content'); ?>
         </div>
      </article>
   </main>
   <aside>
      <?php get_component('aside-common'); ?>
   </aside>
</div>
