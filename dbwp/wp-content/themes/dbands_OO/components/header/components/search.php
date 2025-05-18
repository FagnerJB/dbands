<?php

use dbp\Common\Utils;

do_action('pre_get_search_form', $args);

$type = (string) get_query_var('search_type', 'site');

$options = Utils::get_search_options();

if (!empty($options)) {
   ?>
   <div id="search-anchor"></div>
   <search id="search" class="container bg-zinc-900 text-zinc-200 py-2.5">
      <form method="get" action="<?php bloginfo('url'); ?>" x-on:submit.prevent="handleSearch($el)">
         <div class="flex flex-col md:flex-row gap-2 md:items-center">
            <div class="grow flex gap-3 items-center">
               <?php

                  wp_nav_menu([
                     'theme_location' => 'social_links',
                     'container'      => '',
                     'menu_id'        => '',
                     'menu_class'     => 'flex gap-3 text-xl menu-only-icons',
                  ]);

   ?>
               <label for="search-input" class="text-xl">
                  <i class="fas fa-search"></i>
               </label>
               <input id="search-input" class="grow py-1 px-2 w-full" name="s" type="search" maxlength="42" placeholder="<?php echo $options[$type]['placeholder']; ?>" x-bind:placeholder="search.placeholder" required value="<?php the_search_query(); ?>">
            </div>

            <div class="search-options hidden md:flex gap-2 items-center">
               <div class="flex grow gap-2 items-center min-w-60" x-on:click.outside="search.select = false">
                  <div class="h-full grow relative flex items-center">
                     <button class="btn-alt flex gap-2 justify-between items-center w-full py-1 px-2 " type="button" x-on:click="search.select = !search.select">
                        <div class="flex gap-2 items-center">
                           <strong x-text="search.selected"><?php echo $options[$type]['selected']; ?></strong>
                        </div>
                        <i class="fa-solid fa-chevron-down"></i>
                     </button>
                     <div class="absolute top-full right-0 z-10 left-0 flex flex-col rounded-b bg-zinc-900 overflow-hidden" x-show="search.select" x-transition x-cloak>
                        <?php

            foreach ($options as $key => $option) {
               ?>
                           <label class="btn-item cursor-pointer py-1 px-2" tabindex="0" x-on:keyup.enter="search.type='<?php echo $key; ?>';document.getElementById('search-input').focus()">
                              <input class="hidden" name="search_type" type="radio" value="<?php echo $key; ?>" x-model.fill="search.type" <?php checked($type, $key); ?> required>
                              <input name="<?php echo $key; ?>-placeholder" type="hidden" value="<?php echo $option['placeholder']; ?>" disabled>
                              <input name="<?php echo $key; ?>-selected" type="hidden" value="<?php echo $option['selected']; ?>" disabled>
                              <div class="flex items-center gap-2">
                                 <i class="far" x-bind:class="{'fa-circle': search.type !== '<?php echo $key; ?>', 'fa-dot-circle': search.type === '<?php echo $key; ?>'}"></i>
                                 <strong><?php echo $option['title']; ?></strong>
                                 <?php

                        if (!empty($option['service'])) {
                           ?>
                                    <i class="<?php echo $option['service']; ?>"></i>
                                 <?php

                        }

               ?>
                              </div>
                              <small><?php echo $option['subtitle']; ?></small>
                           </label>
                        <?php

            }

   ?>
                     </div>
                  </div>
               </div>
               <button class="btn-alt gap-2 items-center" type="submit">
                  <?php esc_html_e('Buscar', 'dbands'); ?>
               </button>
            </div>

      </form>
   </search>
<?php

}

?>
