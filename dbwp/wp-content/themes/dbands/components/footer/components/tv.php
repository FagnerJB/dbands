<?php

$video_ID = get_query_var('video');

?>
<section id="tv" class="fixed z-80 bg-zinc-800 transition-all w-full" x-show="tv.status !== 'hidden'"
   x-bind:class="{
   'right-0 bottom-0 size-full': tv.status === 'full',
   'right-0 sm:right-1 bottom-0 sm:bottom-1 w-full sm:max-w-lg aspect-video': tv.status === 'small'
}" x-cloak>
   <div id="player-container" class="size-full"
      x-init="tv.play('<?php echo $video_ID; ?>')"></div>
   <div class="flex text-xl select-none z-80" x-show="tv.showButtons" x-bind:class="{
   'absolute left-5 bottom-32 flex-col': tv.status === 'full',
   'fixed right-1 bottom-[calc(100vw/16*9)] sm:bottom-73': tv.status === 'small'
   }">
      <button class="btn aspect-square justify-center"
         title="<?php esc_html_e('Próximo vídeo', 'dbands'); ?>"
         x-show="tv.showNext"
         x-on:click="tv.next()"
         x-cloak>
         <i class="fas fa-step-forward"></i>
      </button>
      <button class="btn aspect-square justify-center"
         title="<?php esc_html_e('Minimizar', 'dbands'); ?>"
         x-on:click="tv.status = 'small'"
         x-show="tv.status !== 'small'"
         x-on:keyup.escape.window="if(tv.status === 'full') tv.status='small'"
         x-cloak>
         <i class="fa-solid fa-compress"></i>
      </button>
      <button class="btn aspect-square justify-center"
         title="<?php esc_html_e('Tela cheia', 'dbands'); ?>"
         x-on:click="tv.status = 'full'"
         x-show="tv.status !== 'full'"
         x-cloak>
         <i class="fa-solid fa-expand"></i>
      </button>
      <button class="btn aspect-square justify-center"
         title="<?php esc_html_e('Fechar', 'dbands'); ?>"
         x-on:click="tv.current = ''">
         <i class="fas fa-times"></i>
      </button>
   </div>
</section>
