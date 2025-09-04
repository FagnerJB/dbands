<?php

use dbp\Band\Utils;

$suggests = Utils::get_suggests();

?>
<section class="flex flex-col gap-2">
   <div class="flex justify-between items-center">
      <h2 class="font-semibold text-sm sm:text-md lg:text-2xl">
         <?php esc_html_e('Nossas recomendações', 'dbands'); ?>
      </h2>
      <a class="btn whitespace-nowrap text-sm" href="<?php echo home_url('indice-bandas-alemas'); ?>">Veja todas</a>
   </div>
   <ul class="grid auto-rows-auto grid-cols-2 lg:grid-cols-3 gap-1 lg:gap-2">
      <?php foreach ($suggests as $suggested) { ?>
      <li class="aspect-row lg:aspect-feed overflow-hidden text-zinc-200">
         <a class="group relative"
            href="<?php echo $suggested->term->get('link'); ?>">
            <img class="size-full object-cover object-center group-hover:scale-110 group-focus-visible:scale-110 transition-all"
                 src="<?php echo $suggested->term->get_cover(); ?>"
                 alt="" loading="lazy">
            <div
                 class="absolute inset-0 z-0 flex flex-col justify-between py-2 px-3 bg-zinc-950/50 group-hover:bg-zinc-950/80 group-focus-visible:bg-zinc-950/80 text-shadow-sm transition-all">
               <div class="grow">
                  <div class="font-semibold text-xs md:text-md lg:text-xl">
                     <?php echo $suggested->term->get('name'); ?>
                  </div>
                  <div class="text-xxs md:text-sm">
                     <?php echo $suggested->term->get_genre(); ?>
                  </div>
               </div>
               <div class="text-xxs md:text-sm">
                  <?php echo $suggested->origin; ?>
               </div>
            </div>
         </a>
      </li>
      <?php }?>
   </ul>
</section>
