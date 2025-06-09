<?php

use dbp\Lyric\Lyric;

$Lyric = new Lyric();

?>
<div class="container-content">
   <main>
      <article id="lyric-<?php echo $Lyric->ID; ?>" <?php post_class(); ?>>
         <header class="mb-7">
            <h1>
               <?php echo $Lyric->get('title'); ?>
            </h1>
         </header>
         <div class="text-left whitespace-break-spaces"><?php echo $Lyric->get_content(); ?></div>
      </article>
   </main>
   <aside>
      <?php

      get_page_component('single-lyric', 'aside');

?>
   </aside>
</div>
