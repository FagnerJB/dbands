<section>
   <div class="flex flex-col gap-2">
      <h2 class="text-xl font-medium"><?php esc_html_e('Todos os meses', 'dbands'); ?></h2>
      <select class="py-2 px-2 bg-neutral-800 text-zinc-200 wjs" x-on:input="parseUrl($el.value)">
         <option>(selecione um mês)</option>
         <?php

         wp_get_archives([
            'format'          => 'option',
            'show_post_count' => true,
         ]);

      ?>
      </select>
      <details class="nojs">
         <summary>Mostrar publicações por mês</summary>
         <ul class="list-disc list-inside">
            <?php

            wp_get_archives([
               'format'          => 'html',
               'show_post_count' => true,
            ]);

         ?>
         </ul>
      </details>
   </div>
</section>
