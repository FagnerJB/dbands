<section>
   <div class="flex flex-col gap-2">
      <h2 class="text-xl font-medium"><?php esc_html_e('Todos os meses', 'dbands') ?></h2>
      <select class="py-2 px-2 bg-neutral-800 text-zinc-200" x-on:input="parseUrl($el.value)">
         <option>(selecione um mês)</option>
         <?php

         wp_get_archives([
            'format'          => 'option',
            'show_post_count' => true,
         ]);

         ?>
      </select>
   </div>
</section>
