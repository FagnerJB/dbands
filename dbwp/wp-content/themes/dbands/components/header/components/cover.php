<?php

use dbp\Band\Band;

if (isset($_GET['slug_band'])) {
   $cover = new Band([
      'field' => 'slug',
      'value' => $_GET['slug_band'],
   ]);
} else {
   $cover = new Band('cover');
}

$title = sprintf(esc_attr__('Publicações sobre %s', 'dbands'), $cover->get('name'));

?>
<a class="block"
   href="<?php echo $cover->get('link'); ?>"
   title="<?php echo $title; ?>" tabindex="-1">
   <img class="w-full" src="<?php echo $cover->get_cover(); ?>"
        width="960" height="300" fetchpriority="high" alt="">
</a>
<?php

if (!empty($cover->meta->photo_credits)) {
   ?>
<div class="absolute bottom-0 right-0 rounded-tl py-1 px-2 bg-zinc-800/60 text-xxs md:text-sm">
   <?php echo $cover->get_meta_link('photo_credits'); ?>
</div>
<?php

}

?>
<div class="absolute top-2 md:top-3 right-1 md:right-3 flex justify-end">
   <div class="text-right mr-3 text-white text-shadow-cover">
      <div class="text-md md:text-3xl font-bold">
         <a href="<?php echo $cover->get('link'); ?>"
            title="<?php echo $title; ?>" tabindex="-1">
            <?php echo $cover->get('name'); ?>
         </a>
      </div>
      <?php

      if (!empty($cover->meta->genre)) {
         ?>
      <div class="text-xs md:text-lg font-semibold">
         <a href="<?php echo get_permalink(217) . '#' . sanitize_title($cover->get_genre()); ?>"
            title="<?php printf(esc_attr__('Mais bandas de %s', 'dbands'), $cover->get_genre()); ?>"
            tabindex="-1">
            <?php echo $cover->get_genre(); ?>
         </a>
      </div>
      <?php

      }

?>
      <ul class="flex justify-end gap-1 text-lg md:text-xl" aria-hidden="true">
         <?php

   if (!empty($cover->meta->itunes)) {
      ?>
         <li>
            <?php echo $cover->get_meta_link('itunes', false, attrs: [
               'tabindex' => -1,
            ]); ?>
         </li>
         <?php

   }

if (!empty($cover->meta->spotify)) {
   ?>
         <li>
            <?php echo $cover->get_meta_link('spotify', false, attrs: [
               'tabindex' => -1,
            ]); ?>
         </li>
         <?php

}

?>
      </ul>
   </div>
</div>
