<?php

$lastfm = $args['card'];

use dbp\Common\Utils;

?>
<section class="flex flex-col gap-2">
   <ul class="grid auto-rows-auto grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1 sm:gap-3 flex-wrap">
      <li class="aspect-square xl:aspect-video overflow-hidden col-span-2 row-span-2 flex flex-col gap-3 py-3 px-3 bg-zinc-800 text-zinc-200">
         <h2 class="flex justify-between items-end text-base sm:text-xl font-semibold">
            <?php echo $lastfm['name']; ?>
         </h2>
         <p class="grow line-clamp-7 leading-6.5 whitespace-break-spaces text-sm sm:text-base"><?php echo $lastfm['wiki']; ?></p>
         <ul class="flex flex-wrap gap-1 sm:gap-2 text-xs sm:text-sm">
            <?php

            if (!empty($lastfm['tags'])) {
               foreach ($lastfm['tags'] as $item) {
                  $tag     = str_replace('https://www.last.fm/tag/', '', $item['url']);
                  $tag_url = Utils::get_search_link($tag, 'tag');

                  ?>
                  <li>
                     <a class="btn !gap-2" href="<?php echo $tag_url; ?>" rel="nofollow noindex">
                        <i class="fas fa-tag"></i> <?php echo $item['name']; ?>
                     </a>
                  </li>
            <?php

               }
            }

?>
            <li>
               <a class="btn !gap-2" href="<?php echo $lastfm['url']; ?>" target="_blank" rel="external">
                  <i class="fa-solid fa-users"></i>
                  <?php

      echo number_format($lastfm['count'], 0, '', ' ') . ' ';

if ('artista' === $args['search_type']) {
   esc_html_e('ouvintes', 'dbands');
} elseif ('tag' === $args['search_type']) {
   esc_html_e('usuários', 'dbands');
} elseif ('usuario' === $args['search_type']) {
   esc_html_e('artistas', 'dbands');
}

?>
               </a>
            </li>
            <li>
               <a class="btn !gap-2" href="<?php echo $lastfm['url']; ?>" target="_blank">
                  <i class="fa-brands fa-lastfm"></i> <?php esc_html_e('Leia mais no Last.fm', 'dbands'); ?>
               </a>
            </li>
         </ul>
      </li>
      <?php

      foreach ($lastfm['items'] as $artist) {
         ?>
         <li class="flex flex-col font-medium aspect-square xl:aspect-video overflow-hidden">
            <a class="group relative flex flex-col gap-1 justify-end py-1 sm:py-2 px-2 sm:px-3 size-full bg-neutral-800 text-zinc-200 hover:bg-neutral-700 focus-visible:bg-neutral-700" href="<?php echo $artist['url']; ?>" target="_blank">
               <div class="text-lg sm:text-xl truncate pr-2.5"><?php echo $artist['name']; ?></div>
               <?php if ('artista' === $args['search_type']) { ?>
                  <div class="text-sm truncate"><?php echo $artist['match']; ?>% similar</div>
               <?php } ?>
               <?php if ('usuario' === $args['search_type']) { ?>
                  <div class="text-sm truncate"><?php echo $artist['rank']; ?> reproduções</div>
               <?php } ?>
               <div class="absolute top-2 sm:top-3 right-2 sm:right-3">
                  <i class="fa-brands fa-square-lastfm"></i>
               </div>
            </a>
            <a class="flex justify-between items-center py-1 sm:py-2 px-3 bg-red-700 text-zinc-200 hover:bg-red-600" href="<?php echo Utils::get_search_link($artist['name'], 'videos'); ?>" title="Buscar vídeos de <?php echo $artist['name']; ?>" rel="nofollow noindex">
               Buscar vídeos <i class="fa-brands fa-youtube"></i>
            </a>
            <a class="flex justify-between items-center py-1 sm:py-2 px-3 bg-amber-400 text-zinc-800 hover:bg-yellow-300 focus-visible:bg-yellow-300" href="<?php echo Utils::get_search_link($artist['name'], 'artista'); ?>" title="Buscar similares de <?php echo $artist['name']; ?>" rel="nofollow noindex">
               Artistas similares <i class="fa-brands fa-lastfm"></i>
            </a>

         </li>
      <?php

      }

?>
   </ul>
</section>
