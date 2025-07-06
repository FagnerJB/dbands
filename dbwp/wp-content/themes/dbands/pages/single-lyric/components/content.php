<?php

use dbp\Lyric\Lyric;

if (have_posts()) {
   $Lyric = new Lyric();

   the_post();

   ?>
<div class="container-content">
   <main>
      <article id="lyric-<?php echo $Lyric->ID; ?>" <?php post_class(); ?>>
         <header class="mb-12">
            <h1>
               <?php echo $Lyric->get('title'); ?>
            </h1>
            <ul class="flex flex-col gap-1 text-lg mb-4">
               <?php if ($single_name = $Lyric->get('single')) { ?>
               <li>
                  <?php printf(
                     esc_html__('%sSingle:%s %s', 'dbands'),
                     '<span class="font-medium">',
                     '</span>',
                     $single_name,
                  ); ?>
               </li>
               <?php }?>
               <?php $album = $Lyric->get('terms', taxonomy: 'albuns'); ?>
               <?php if (!empty($album)) { ?>
               <li>
                  <?php printf(
                     esc_html__('%sÁlbum:%s %s', 'dbands'),
                     '<span class="font-medium">',
                     '</span>',
                     $album[0]->name,
                  ); ?>
               </li>
               <?php } ?>
               <?php if ($composer = $Lyric->get('compositor')) { ?>
               <li>
                  <?php printf(
                     esc_html__('%sComposição:%s %s', 'dbands'),
                     '<span class="font-medium">',
                     '</span>',
                     $composer,
                  ); ?>
               </li>
               <?php } ?>
            </ul>

            <div class="flex flex-col md:flex-row gap-2">
               <div class="font-medium basis-1/2">
                  <?php if ($translator = $Lyric->get('tradutor')) { ?>
                  <?php printf(esc_html__('Traduzido por %s', 'dbands'), $translator); ?>
                  <?php } ?>
               </div>
               <div class="flex flex-col col-2">
                  <?php get_component('postdate'); ?>
               </div>
            </div>
         </header>
         <div class="text-left">
            <?php echo $Lyric->get('content'); ?>
         </div>
      </article>
   </main>
   <aside>
      <?php get_page_component('single-lyric', 'aside'); ?>
   </aside>
</div>
<?php } ?>
