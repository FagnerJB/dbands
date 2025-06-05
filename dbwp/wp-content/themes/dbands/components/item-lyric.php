<?php

use dbp\Lyric\Lyric;

$Lyric = $args['post'] ?? new Lyric();
$first = 0 === $wp_query->current_post;
$title = sprintf(esc_attr__('Tradução de %s de %s', 'dbands'), $Lyric->get('title'), $Lyric->get_artists());

?>
<li class="size-full text-zinc-800 <?php echo $first ? 'aspect-video col-span-2' : 'aspect-row md:aspect-feed'; ?>">
   <article class="size-full">
      <a class="flex flex-col justify-end md:justify-between py-2 px-3 size-full bg-amber-400 hover:bg-yellow-300 focus-visible:bg-yellow-300" href="<?php echo $Lyric->get('link'); ?>" title="<?php echo $title; ?>" rel="bookmark">
         <div class="grow italic line-clamp-2 sm:line-clamp-2 md:line-clamp-4 text-xxs md:text-base">
            <?php $Lyric->get('excerpt'); ?>
         </div>
         <div>
            <h3>
               <div class="text-xs md:text-lg line-clamp-3"><?php echo $Lyric->get('title'); ?></div>
               <span class="sr-only">de</span>
               <div class="font-semibold text-xxs md:text-base"><?php echo $Lyric->get_artists(); ?></div>
            </h3>
            <?php

            if (is_search()) {
               ?>
               <div class="text-xxs md:text-sm">Traduzido por <?php echo $Lyric->get('tradutor'); ?></div>
            <?php

            }

?>
         </div>
      </a>
   </article>
</li>
