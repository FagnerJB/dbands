<?php

use dbp\Common\Utils as CommonUtils;
use dbp\Lyric\Utils;

$page   = CommonUtils::get_page();
$lyrics = Utils::get_lyrics($page);

if (!empty($lyrics)) {
   ?>
   <section class="flex flex-col gap-2">
      <?php

         if (1 === $page || !wp_is_serving_rest_request()) {
            ?>
         <div class="flex justify-between items-center">
            <h2 class="font-semibold text-sm sm:text-base lg:text-2xl">
               <a href="<?php echo home_url('traducoes'); ?>">
                  <?php

                        esc_html_e('Traduções', 'dbands');

            if (1 === $page) {
               echo ' ';
               esc_html_e('recentes', 'dbands');
            }

            ?>
               </a>
            </h2>
            <a class="hidden md:flex md:btn whitespace-nowrap text-sm" href="<?php echo home_url('traducoes'); ?>">Veja todas</a>
         </div>
      <?php

         }

   ?>
      <ul class="grid auto-rows-auto grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1 lg:gap-2">
         <?php

      foreach ($lyrics as $lyric) {
         get_component('item-lyric', [
            'post' => $lyric,
         ]);
      }

   wp_reset_postdata();

   ?>
      </ul>
   </section>
<?php

}

?>
