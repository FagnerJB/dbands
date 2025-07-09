<?php

use dbp\TV\Utils;

$oembed = $args['oembed'];

$video_ID = substr($oembed, strpos($oembed, '/embed/') + 7, 11);

$oembed = str_replace('<iframe', '<iframe class="size-full"', $oembed);
$oembed = str_replace('?feature=oembed', '?autoplay=1&fs=0&rel=0', $oembed);

?>
<div class="embed">
   <div class="aspect-video mt-3" x-data="{show: false}">
      <a class="relative" href="https://www.youtube.com/watch?v=<?php echo $video_ID; ?>" target="_blank" x-on:click.prevent="show=!show" x-show="!show"><img src="https://i.ytimg.com/vi/<?php echo $video_ID; ?>/maxresdefault.jpg"><i class="absolute top-1/2 left-1/2 -translate-1/2 text-5xl text-white text-shadow-sm fa-brands fa-youtube"></i></a><template x-if="show"><?php echo $oembed; ?></template>
   </div>
   <div class="flex bg-zinc-800 text-zinc-200">
      <a class="flex gap-2 py-1 md:py-3 px-2 md:px-5 items-center hover:bg-neutral-700 focus-visible:bg-neutral-700"
         <?php Utils::video_attrs($video_ID); ?>><i
            class="fa-solid fa-expand"></i><span
               class="hidden md:inline"><?php esc_html_e('Assistir em tela cheia', 'dbands'); ?></span><span
               class="inline md:hidden"><?php esc_html_e('Tela cheia', 'dbands'); ?></span></a>
      <button class="flex gap-2 py-1 md:py-2 px-2 md:px-4 items-center hover:bg-neutral-700 focus-visible:bg-neutral-700 wjs"
              x-on:click="tv.play('<?php echo $video_ID; ?>', 'small')"><i
            class="fa-solid fa-compress"></i><span
               class="hidden md:inline"><?php esc_html_e('Assistir fixado', 'dbands'); ?></span><span
               class="inline md:hidden"><?php esc_html_e('Fixado', 'dbands'); ?></span></button>
   </div>
</div>
