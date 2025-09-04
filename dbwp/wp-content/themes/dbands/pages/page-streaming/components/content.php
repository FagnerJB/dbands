<?php

use cavWP\Utils as CavUtils;
use dbp\Services\TMDB;

if (have_posts()) {
   $page  = CavUtils::get_page();
   $tmdb  = new TMDB();
   $items = $tmdb->get_items($page);

   the_post();

   ?>
<div id="page-<?php echo $page; ?>" class="container-content">
   <article class="w-full">
      <?php if (1 === $page || !wp_is_serving_rest_request()) { ?>
      <header>
         <h1 class="mb-4"><?php the_title(); ?></h1>
         <div class="mb-7 text-xl font-normal">
            <?php the_excerpt(); ?>
         </div>
      </header>
      <div class="mb-10 text-justify content">
         <?php the_content(); ?>
      </div>
      <?php } ?>
      <ul class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-1">
         <?php foreach ($items as $item) {
            $type_label = 'movie' === $item['type'] ? esc_html__('Filme', 'dbands') : esc_html__('Série', 'dbands');
            echo <<<HTML
               <li>
                  <button class="size-full overflow-hidden" x-on:click="tmdbItem = \$el.dataset;\$rest.get(`\${dbands.apiBase}/providers`, {id: \$el.dataset.id, type: \$el.dataset.type, title: \$el.dataset.original});tmdbModal.showModal()" data-id="{$item['id']}" data-type="{$item['type']}" data-genres="{$item['genres']}" data-release="{$item['release']}" data-original="{$item['original']}" data-title="{$item['title']}" data-overview="{$item['overview']}" data-backdrop="{$item['backdrop']}">
                     <img class="w-full aspect-poster object-cover" src="{$item['image']}" alt="{$item['full_title']}" title="{$item['full_title']}" />
                     <div class="flex justify-between text-sm bg-zinc-300 py-1 px-2.5 font-normal">
                        <div>{$type_label}</div>
                        <div>{$item['release']}</div>
                     </div>
                  </button>
               </li>
            HTML;
         } ?>
      </ul>
      <?php if (1 === $page || !wp_is_serving_rest_request()) { ?>
      <dialog id="tmdbModal" class="m-auto overflow-y-auto backdrop:bg-black/60"
              x-on:click.self="tmdbModal.close()">
         <div class="relative max-w-2xl bg-zinc-200">
            <button class="absolute top-3 right-3 text-white drop-shadow drop-shadow-black"
                    x-on:click="tmdbModal.close()">
               <i class="fa-solid fa-circle-xmark"></i>
            </button>
            <img class="w-full h-55 object-cover object-top" x-bind:src="tmdbItem?.backdrop" alt="" />
            <div class="py-3 px-5">
               <h2 class="font-medium text-xl" x-text="tmdbItem?.title"></h2>
               <div class="italic" x-show="tmdbItem?.title !== tmdbItem?.original"
                    x-text="'Título original: '+tmdbItem?.original"></div>
               <div class="flex gap-3 mt-1 text-md">
                  <span class="font-medium" x-text="tmdbItem?.type === 'movie' ? 'Filme' : 'Série'"></span>
                  <span x-text="tmdbItem?.release"></span>
                  <span x-text="tmdbItem?.genres"></span>
               </div>

               <p class="my-4" x-text="tmdbItem?.overview"></p>

               <h3 class="font-medium">Onde assistir <span class="text-xs">por JustWatch</span></h3>
               <div class="providers">
                  <div class="flex items-center justify-center min-h-10 text-2xl">
                     <i class="fa-solid fa-spinner fa-spin-pulse fa-beat"></i>
                  </div>
               </div>
            </div>
         </div>
      </dialog>
      <div class="flex justify-between items-center mt-4 text-sm">
         <p>This product uses the TMDB API but is not endorsed or certified by TMDB.</p>
         <a href="https://www.themoviedb.org/" target="_blank" rel="nofollow external">
            <?php CavUtils::render_svg(get_theme_file_path('assets/tmdb.svg'), 'h-10'); ?>
         </a>
      </div>
      <?php } ?>
   </article>
</div>
<?php latte_component('pagination', [
   'link'      => get_permalink($post),
   'max_pages' => $tmdb->max_pages,
]); ?>
<?php } ?>
