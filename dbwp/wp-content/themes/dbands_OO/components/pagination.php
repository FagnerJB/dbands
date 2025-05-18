<?php

use dbp\Common\Utils;

$page      = $args['page'] ?? Utils::get_page();;
$max_pages = $args['max_pages'] ?? $wp_query->max_num_pages;
$link      = $args['link'];
$next      = $page + 1;
$prev      = $page - 1;
$pages     = 2;

?>
<nav class="container py-3 bg-zinc-900 text-zinc-200">
   <ul class="flex justify-between items-center nojs">
      <li class="w-1/3">
         <a class="btn-alt" href="#top" title="Ir para o topo da página">
            <i class="fas fa-arrow-alt-circle-up"></i> Topo
         </a>
      </li>
      <li class="w-1/3 uppercase text-center font-medium">
         <?php

         if ($page > 1) {
            printf(esc_html__('Página %d', 'dbands'), $page);
         }

         ?>
      </li>
      <li class="w-1/3 flex justify-between">
         <?php

         if ($next <= $max_pages) {

         ?>
            <a class="btn-alt" href="<?php echo esc_url($link . '/page/' . $next) ?>" rel="prev">
               <i class="fas fa-arrow-left"></i>
               <?php esc_html_e('Antigos', 'dbands') ?>
            </a>
         <?php

         }

         if ($prev > 0) {

         ?>
            <a class="btn-alt" href="<?php echo esc_url($link . '/page/' . $prev) ?>" rel="next">
               <?php esc_html_e('Recentes', 'dbands') ?>
               <i class=" fas fa-arrow-right"></i>
            </a>
         <?php

         }

         ?>
      </li>
   </ul>
   <ul class="flex justify-between items-center wjs text-xs md:text-base" x-ref="archiveLink" data-archive-link="<?php echo $link ?>">
      <li class="w-1/3 flex gap-px sm:gap-2">
         <button class="btn-alt" type="button" title="Ir para o topo da página"
            x-on:click="$action('scroll', '#page-1')">
            <i class="fas fa-arrow-alt-circle-up"></i> Topo
         </button>
         <?php

         if ($max_pages > 1) {

            if ($page > 1) {

               $min_prev = $page - $pages < 2 ? 2 : $page - $pages;

               for ($p = $min_prev; $p < $page; $p++) {

         ?>
                  <button class="btn-alt" type="button" title="<?php printf(esc_html__('Ir para página %s', 'dbands'), $p) ?>"
                     x-show="pagination.currentPage >= <?php echo $p ?>"
                     x-on:click="$action('scroll','#page-<?php echo $p ?>')"
                     x-cloak>
                     <?php echo $p ?>
                  </button>
            <?php

               }
            }

            ?>
      </li>
      <li class="w-1/3 uppercase text-center font-medium">
         <span x-show="pagination.currentPage >= 2" x-cloak>
            <span class="hidden sm:inline">
               <?php esc_html_e('Página', 'dbands') ?>
            </span>
            <?php

            echo $page;

            ?>
            <span class="inline sm:hidden">/</span>
            <span class="hidden sm:inline"> de </span>
            <?php

            echo $max_pages;

            ?>
         </span>
      </li>
      <li class="w-1/3 flex justify-end gap-px sm:gap-2">
         <?php

            $min_next = $next <= 1 ? 2 : $next;

            for ($p = $min_next; $p <= $page + $pages; $p++) {

         ?>
            <button class="btn-alt" type="button" title="<?php printf(esc_html__('Ir para página %s', 'dbands'), $p) ?>"
               x-show="pagination.currentPage >= <?php echo $p ?>"
               x-on:click="$action('scroll','#page-<?php echo $p ?>')"
               x-cloak>
               <?php echo $p ?>
            </button>
         <?php

            }

         ?>
         <button class="btn-alt" type="button" title="Carregar mais páginas" x-show="!pagination.infinite" x-on:click="onInfinite()" x-cloak>
            <i class="fas fa-plus-circle"></i>
            <?php esc_html_e('Mais', 'dbands') ?>
         </button>
         <button class="btn-alt" type="button" title="Parar carregamento automático" x-show="pagination.infinite" x-on:click="pagination.infinite=false" x-cloak>
            <i class="fas fa-stop-circle"></i>
            <?php esc_html_e('Parar', 'dbands') ?>
         </button>
      <?php

         }

      ?>
      </li>
   </ul>
</nav>
