   <noscript>
      <div
           class="flex gap-2 items-center justify-center p-4 mb-3 bg-red-800 text-zinc-200 text-center text-xs sm:text-md">
         <?php

      printf(
         esc_html__('%s ou %s para que este site funcione corretamente.', 'dbands'),
         '<a class="btn-alt" href="https://browser-update.org/pt/update-browser.html" target="_blank" rel="external">' . esc_html__('Atualize seu navegador', 'dbands') . '</a>',
         '<a class="btn-alt" href="https://www.enable-javascript.com/pt/" target="_blank" rel="external">' . esc_html__('ative o JavaScript', 'dbands') . '</a>',
      );

         ?>
      </div>
   </noscript>
