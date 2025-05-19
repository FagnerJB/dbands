<?php

use dbp\Page\Page;

$Page = new Page();

?>
<div class="container-content">
   <section>
      <article id="page-<?php echo $Page->ID; ?>" <?php post_class('content'); ?>>
         <header class="mb-7">
            <h1><?php echo $Page->get('title'); ?></h1>
         </header>
         <section class="text-justify">
            <?php echo $Page->get('content'); ?>
         </section>
      </article>
   </section>
   <aside>
      <?php

      get_page_component('page', 'aside');

?>
   </aside>
</div>
