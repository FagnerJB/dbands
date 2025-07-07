<?php

use dbp\Author\Author;
use dbp\News\News;

if (have_posts()) {
   $News = new News();

   $Author = new Author($News->get('author'));
   $title  = sprintf(esc_html__('Publicações de %s', 'dbands'), $Author->get('name'));

   the_post();

   ?>
<div class="container-content">
   <main>
      <article id="single-<?php echo $News->ID; ?>" <?php post_class(); ?>>
         <header>
            <?php get_component('tags', [
               'post'       => $News,
               'with_links' => true,
            ]); ?>
            <h1 class="mt-3">
               <?php echo $News->get('title'); ?>
            </h1>
            <div class="mb-4 text-lg md:text-xl font-normal">
               <?php echo $News->get('excerpt'); ?>
            </div>
            <div class="mb-12 flex flex-col md:flex-row gap-2">
               <div class="flex gap-2 items-start basis-1/2">
                  <a class="shrink-0"
                     href="<?php echo $Author->get('link'); ?>"
                     title="<?php echo $title; ?>" rel="author">
                     <?php echo $Author->get('avatar', size: 48); ?>
                  </a>
                  <div class="flex flex-col gap-1">
                     <a class="text-base font-medium"
                        href="<?php echo $Author->get('link'); ?>"
                        title="<?php echo $title; ?>" rel="author">
                        <?php printf(esc_html__('Por %s', 'dbands'), $Author->get('name')); ?>
                     </a>
                     <p class="text-sm line-clamp-2">
                        <?php echo $Author->get('description'); ?>
                     </p>
                  </div>
               </div>
               <?php get_component('postdate'); ?>
            </div>
         </header>
         <div class="content text-justify">
            <?php echo $News->get('content'); ?>
         </div>
      </article>
   </main>
   <aside>
      <?php get_page_component('single', 'aside'); ?>
   </aside>
</div>
<?php } ?>
