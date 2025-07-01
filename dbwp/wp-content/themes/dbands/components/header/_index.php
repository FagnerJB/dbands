<?php

do_action('get_header');

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
   <?php wp_head(); ?>
</head>

<body id="top" x-data="dbands" data-title="<?php echo wp_get_document_title(); ?>" <?php body_class('no-js'); ?> x-bind:class="{'overflow-hidden': tv.status === 'full'||gallery.open}">
   <?php wp_body_open(); ?>
   <noscript>
      <div class="flex gap-2 items-center justify-center p-4 mb-3 bg-red-800 text-zinc-200 text-center text-xs sm:text-base">
         <?php

      printf(
         esc_html__('%s ou %s para que este site funcione corretamente.', 'dbands'),
         '<a class="btn-alt" href="https://browser-update.org/pt/update-browser.html" target="_blank" rel="external">' . esc_html__('Atualize seu navegador', 'dbands') . '</a>',
         '<a class="btn-alt" href="https://www.enable-javascript.com/pt/" target="_blank" rel="external">' . esc_html__('ative o JavaScript', 'dbands') . '</a>',
      );

?>
      </div>
   </noscript>

   <nav class="absolute top-3 left-3 z-20">
      <ul>
         <li><a class="btn sr-only-focusable" href="#main">Pular ao conteúdo</a></li>
         <li><a class="btn sr-only-focusable" href="#footer-menu">Ir para o menu</a></li>
      </ul>
   </nav>

   <div id="loading-bar" class="fixed z-90 top-0 inset-x-0 h-0 flex items-center justify-center bg-red-800 text-xxs text-zinc-200 uppercase truncate overflow-hidden transition-all"></div>

   <div class="py-4 mb-3 bg-zinc-200 text-xs sm:text-base min-w-90" x-show="showCookies" x-transition x-cloak data-nosnippet>
      <div class="container flex justify-between gap-1 sm:gap-3 items-center">
         <span class="grow uppercase">
            <?php

   esc_html_e('Contém cookies. Pode conter anúncios personalizados.', 'dbands');

?>
         </span>
         <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
            <a class="btn-alt uppercase" href="https://dbands.com.br/politica-de-privacidade" rel="privacy-policy">Detalhes</a>
            <button class="btn-alt uppercase" x-on:click="showCookies = false" type="button">Fechar</button>
         </div>
      </div>
   </div>

   <header class="container bg-zinc-900 text-zinc-200 !px-0 md:mt-3" data-nosnippet>
      <div class="relative">
         <div id="cover" class="relative">
            <?php get_component(['header', 'cover']); ?>
         </div>
         <div class="absolute bottom-1 left-2 w-5/12 md:w-4/12 text-shadow-cover text-white">
            <?php the_custom_logo(); ?>
         </div>
      </div>
   </header>
   <?php

   get_component(['header', 'search']);

?>
