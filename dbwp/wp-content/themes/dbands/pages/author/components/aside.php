<?php

use cavWP\Utils as CavUtils;
use dbp\Author\Author;

$Author = new Author();

?>
<div id="aside" class="sticky top-3 flex flex-col gap-5">
   <div class="flex gap-2 items-start">
      <?php echo $Author->get('avatar'); ?>
      <section class="flex flex-col gap-px with-links">
         <h2 class="text-xl font-semibold">
            <span class="sr-only">Sobre</span>
            <?php echo $Author->get('display_name'); ?>
         </h2>
         <?php if ($description = $Author->get('description')) { ?>
         <p>
            <?php echo nl2br($description); ?>
         </p>
         <?php } ?>
         <?php if ($user_url = $Author->get('user_url')) { ?>
         <a class="self-start text-sm"
            href="<?php echo esc_url($user_url); ?>" target="_blank">
            <?php echo CavUtils::clean_domain($user_url); ?>
         </a>
         <?php } ?>
      </section>
   </div>
   <?php if ($favorites = $Author->get_favorites('term')) { ?>
   <section class="flex flex-col gap-2">
      <h3 class="text-xl font-medium">
         <?php esc_html_e('Bandas favoritas', 'dbands'); ?>
      </h3>
      <?php get_component('tags', [
         'bands'      => $favorites,
         'with_links' => true,
      ]); ?>
   </section>
   <?php } ?>
   <?php $socials = $Author->get_socials(); ?>
   <?php if (!empty($socials)) { ?>
   <section class="flex flex-col gap-2">
      <h3 class="text-xl font-medium">
         <?php esc_html_e('Suas redes sociais', 'dbands'); ?>
      </h3>
      <ul class="flex flex-wrap gap-1">
         <?php foreach ($socials as $key => $meta) { ?>
         <li>
            <a class="btn-alt"
               href="<?php echo $meta['profile']; ?>"
               title="<?php echo $meta['name']; ?>"
               target="_blank"
               rel="external">
               <i class="<?php echo $meta['icon']; ?>"></i>
               <?php echo $meta['raw']; ?>
            </a>
         </li>
         <?php } ?>
      </ul>
   </section>
   <?php } ?>
   <?php get_component('aside-authors'); ?>
   <?php get_component('aside-ad'); ?>
</div>
