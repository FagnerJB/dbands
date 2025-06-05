<?php

use dbp\Author\Author;
use dbp\Common\Utils as CommonUtils;

$Author = new Author();

?>
<div id="aside" class="sticky top-3 flex flex-col gap-5">
   <div class="flex gap-2 items-start">
      <?php echo $Author->get('avatar'); ?>
      <section class="flex flex-col gap-px with-links">
         <h2 class="text-xl font-semibold">
            <span class="sr-only">Sobre</span> <?php echo $Author->get('display_name'); ?>
         </h2>
         <?php

         if ($description = $Author->get('description')) {
            ?>
            <p>
               <?php echo nl2br($description); ?>
            </p>
         <?php

         }

if ($user_url = $Author->get('user_url')) {
   ?>
            <a class="self-start text-sm" href="<?php echo esc_url($user_url); ?>" target="_blank">
               <?php echo CommonUtils::clean_domain($user_url); ?>
            </a>
         <?php

}

?>
      </section>
   </div>
   <?php

   if ($favorites = $Author->get_favorites('term')) {
      ?>
      <section class="flex flex-col gap-2">
         <h3 class="text-xl font-medium"><?php esc_html_e('Bandas favoritas', 'dbands'); ?></h3>
         <?php

            get_component('tags', [
               'bands'      => $favorites,
               'with_links' => true,
            ]);

      ?>
      </section>
   <?php
   }

$socials = $Author->get_socials();

if (!empty($socials)) {
   ?>
      <section class="flex flex-col gap-2">
         <h3 class="text-xl font-medium"><?php esc_html_e('Suas redes sociais', 'dbands'); ?></h3>
         <ul class="flex flex-wrap gap-1">
            <?php

            foreach ($socials as $key => $meta) {
               ?>
               <li>
                  <a class="btn-alt" href="<?php printf($meta['link'], $meta['username']); ?>" title="<?php echo $meta['title']; ?>" target="_blank">
                     <i class="<?php echo $meta['icon']; ?>"></i> <?php printf($meta['text'], $meta['username']); ?>
                  </a>
               </li>
            <?php

            }

   ?>
         </ul>
      </section>
   <?php

}

get_component('aside-authors');

get_component('aside-ad');

?>
</div>
