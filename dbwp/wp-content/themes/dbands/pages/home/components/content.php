<?php

use cavWP\Utils;
use dbp\Services\Youtube;

$page = Utils::get_page();

?>
<div id="page-<?php echo $page; ?>">
   <div class="container flex flex-col sm:flex-row gap-3 py-5 bg-zinc-800 text-zinc-200">
      <section class="flex flex-col gap-2 sm:w-1/2">
         <?php if (1 === $page || !wp_is_serving_rest_request()) { ?>
         <h2 class="font-semibold text-sm sm:text-base lg:text-2xl">
            <?php esc_html_e('Publicações', 'dbands'); ?>
            <?php if (1 === $page) {
               echo ' ';
               esc_html_e(' recentes', 'dbands');
            } ?>
         </h2>
         <?php } ?>
         <?php if (have_posts()) {
            get_component('loop-posts');
         } else {
            get_component('loop-empty');
         } ?>
      </section>
      <div class="flex flex-col gap-5 content-start sm:w-1/2">
         <?php $dbtv   = new Youtube(); ?>
         <?php $videos = $dbtv->get_feed($page); ?>
         <?php if (!empty($videos)) { ?>
         <section class="flex flex-col gap-2">
            <?php if (1 === $page || !wp_is_serving_rest_request()) { ?>
            <div class="flex justify-between items-center">
               <h2 class="font-semibold text-sm sm:text-base lg:text-2xl">
                  <a
                     href="<?php echo home_url('tv'); ?>">
                     <?php esc_html_e('dbTV Metal', 'dbands'); ?>
                  </a>
               </h2>
               <a class="btn whitespace-nowrap text-sm"
                  href="<?php echo home_url('tv'); ?>">Saiba
                  mais</a>
            </div>
            <?php } ?>
            <?php get_component('loop-videos', [
               'videos' => $videos,
            ]); ?>
         </section>
         <?php }?>
         <?php get_page_component('home', 'lyrics'); ?>
         <?php if ($page <= 1) { ?>
         <?php get_page_component('home', 'suggested'); ?>
         <?php get_component('aside-categories'); ?>
         <?php } ?>
      </div>
   </div>
   <?php latte_component('pagination', [
      'link'      => home_url(),
      'max_pages' => $wp_query->max_num_pages,
   ]); ?>
</div>
