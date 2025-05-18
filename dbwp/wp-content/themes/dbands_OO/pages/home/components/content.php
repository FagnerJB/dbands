<?php

use dbp\Common\Utils;
use dbp\Services\Youtube;

$page = Utils::get_page();

?>
<div id="page-<?php echo $page ?>">
   <div class="container flex gap-3 py-5 bg-zinc-800 text-zinc-200">
      <section class="flex flex-col gap-2 w-1/2">
         <?php

         if (1 === $page || !wp_is_serving_rest_request()) {

         ?>
            <h2 class="font-semibold text-sm sm:text-base lg:text-2xl">
               <?php

               esc_html_e('Publicações', 'dbands');
               if ($page === 1) {
                  echo ' ';
                  esc_html_e(' recentes', 'dbands');
               }

               ?>
            </h2>
         <?php

         }

         if (have_posts()) {
            get_component('loop-posts');
         } else {
            get_component('loop-empty');
         }

         ?>
      </section>
      <div class="flex flex-col gap-5 content-start w-1/2">
         <?php

         $dbtv = new Youtube(YT_CHANNEL_ID);

         $videos = $dbtv->get_feed($page);

         if (!empty($videos)) {
         ?>
            <section class="flex flex-col gap-2">
               <?php

               if (1 === $page || !wp_is_serving_rest_request()) {

               ?>
                  <div class="flex justify-between items-center">
                     <h2 class="font-semibold text-sm sm:text-base lg:text-2xl">
                        <a href="<?php echo home_url('tv') ?>">
                           <?php

                           esc_html_e('Deutsche Bands TV', 'dbands');

                           ?>
                        </a>
                     </h2>
                     <a class="hidden md:flex md:btn whitespace-nowrap text-sm" href="<?php echo home_url('tv') ?>">Saiba mais</a>
                  </div>
               <?php

               }

               get_component('loop-videos', [
                  'videos' => $videos
               ]);


               ?>
            </section>
         <?php
         }

         get_page_component('home', 'lyrics');

         if ($page <= 1) {

            get_page_component('home', 'suggested');

            get_component('aside-categories');
         }

         ?>

      </div>
   </div>
   <?php

   get_component('pagination', [
      'link' => home_url(),
   ]);

   ?>
</div>
