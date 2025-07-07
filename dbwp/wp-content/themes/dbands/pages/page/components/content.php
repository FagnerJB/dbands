<?php

use dbp\Page\Page;

if (have_posts()) {
   $Page = new Page();

   the_post();

   ?>
<div class="container-content">
   <main>
      <article id="page-<?php echo $Page->ID; ?>" <?php post_class(); ?>>
         <header class="mb-7">
         <h1>
            <?php echo $Page->get('title'); ?>
         </h1>
         </header>
         <div class="text-justify content">
            <?php echo $Page->get('content'); ?>
         </div>
      </article>
   </main>
   <aside>
      <?php get_component('aside-common'); ?>
   </aside>
</div>
<?php } ?>
