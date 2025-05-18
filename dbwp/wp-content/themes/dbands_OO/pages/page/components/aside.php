<?php

use dbp\Services\Youtube;
use dbp\TV\Utils;

$dbtv   = new Youtube(YT_CHANNEL_ID);
$videos = $dbtv->get_feed();
$first  = key($videos);

?>
<div id="aside" class="sticky top-3 flex flex-col gap-5">
   <section class="flex flex-col gap-2">
      <h2 class="text-xl font-medium">
         Deutsche Bands TV
      </h2>
      <div class="flex gap-2">
         <a class="flex flex-col" href="<?php echo home_url('tv/' . $first); ?>">
            <img class="w-full aspect-video" src="<?php echo Utils::get_video_thumb($first); ?>" width="320" height="180">
            <div class="btn-alt !py-2 !px-4">
               <i class="fas fa-play"></i> Recentes
            </div>
         </a>

         <a class="flex flex-col" href="<?php echo home_url('tv/PLTg2AhCnKU-L4-2j5yEEEyNWDsdnzqVB1'); ?>">
            <img class="w-full aspect-video" src="<?php echo Utils::get_video_thumb('bBqrlBFsf0s'); ?>" width="320" height="180">
            <div class="btn-alt !py-2 !px-4">
               <i class="fas fa-play"></i> Recomendações
            </div>
         </a>
      </div>
   </section>

   <section class="flex flex-col gap-2">
      <h2 class="text-xl font-medium">
         <?php esc_html_e('Todas as páginas', 'dbands'); ?>
      </h2>
      <ul class="flex flex-wrap gap-1">
         <?php

         wp_list_pages([
            'title_li'    => '',
            'sort_column' => 'menu_order',
         ]);

?>
      </ul>
   </section>
   <?php

   get_component('aside-authors');

wp_nav_menu([
   'theme_location'  => 'social_links',
   'container'       => 'div',
   'container_class' => 'flex flex-col gap-2',
   'menu_class'      => 'menu-btns flex flex-wrap gap-1',
   'items_wrap'      => '<h2 class="text-xl font-medium">' . esc_html__('Siga-nos', 'dbands') . '</h2><ul id="%1$s" class="%2$s">%3$s</ul>',
]);

get_component('aside-ad');

?>
</div>
