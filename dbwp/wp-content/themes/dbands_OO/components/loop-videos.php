<?php

use dbp\TV\Utils;

if (is_search()) {
   $classes = 'grid-cols-2 md:grid-cols-4';
}

if (is_home()) {
   $classes = 'grid-cols-1 md:grid-cols-2';
}

?>
<ul class="grid auto-rows-auto gap-1 lg:gap-2 <?php echo $classes; ?>">
   <?php

   foreach ($args['videos'] as $video) {
      ?>
      <li class="relative aspect-video overflow-hidden">
         <a class="group block" href="<?php echo get_permalink(8261) . '/' . $video['id']; ?>">
            <img class="size-full object-cover object-center group-hover:scale-110 group-focus:scale-110 transition-all" src="<?php echo Utils::get_video_thumb($video['id']); ?>" width="320" height="180" alt="" loading="lazy">
            <div class="absolute inset-0 z-0 flex flex-col justify-end gap-2 py-3 px-4 bg-zinc-900/50 group-hover:bg-zinc-900/80 group-focus:bg-zinc-900/80 text-zinc-200 text-shadow-sm transition-all">
               <div class="absolute top-3 right-3 text-xs md:text-base">
                  <i class="fa-brands fa-youtube"></i>
               </div>
               <div class="flex flex-col justify-end overflow-hidden h-14 group-hover:h-26 group-focus:h-26 transition-all">
                  <h3 class="font-semibold text-xs md:text-base line-clamp-2 group-hover:line-clamp-4 group-focus:line-clamp-4">
                     <?php echo $video['title']; ?>
                  </h3>
               </div>
               <div class="truncate text-xxs md:text-sm">
                  <?php

                     printf(
                        esc_html__('%s há %s', 'dbands'),
                        $video['author'],
                        human_time_diff(strtotime($video['date'])),
                     );

      ?>
               </div>
            </div>
         </a>
      </li>
   <?php

   }

?>
</ul>
