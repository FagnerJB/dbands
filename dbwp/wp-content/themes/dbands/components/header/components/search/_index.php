<?php

use dbp\Common\Utils;

do_action('pre_get_search_form', $args);

$type    = get_query_var('search_type', 'site');
$label   = esc_html__('Busque neste site, vídeos musicais do YouTube ou recomendações do Last.fm', 'dbands');
$options = Utils::get_search_options();

?>
<?php if (!empty($options)) { ?>
<div id="search-anchor"></div>
<search id="search" class="container bg-zinc-900 text-zinc-200 py-2.5">
   <form method="get"
         action="<?php bloginfo('url'); ?>"
         x-on:submit.prevent="handleSearch($el)">
      <div class="flex flex-col md:flex-row gap-2 md:items-center">
         <div class="grow flex gap-3 items-center">
            <?php wp_nav_menu([
               'theme_location' => 'social_links',
               'container'      => '',
               'menu_id'        => '',
               'menu_class'     => 'flex text-lg sm:text-xl menu-only-icons',
            ]); ?>
            <label for="search-input" class="text-xl">
               <i class="fas fa-search"></i>
            </label>
            <input id="search-input" class="grow py-1 px-2 w-full" name="s" type="search" minlength="3" maxlength="42"
                   title="<?php echo $label; ?>"
                   placeholder="<?php echo $label; ?>"
                   x-bind:title="search.placeholder" x-bind:placeholder="search.placeholder"
                   value="<?php the_search_query(); ?>" required>
         </div>
         <div class="search-options hidden md:flex gap-2 items-center ">
            <?php get_component(['header', 'search', 'search_options']); ?>
            <button class="btn-alt gap-2 items-center" type="submit">
               <?php esc_html_e('Buscar', 'dbands'); ?>
            </button>
         </div>
      </div>
   </form>
</search>
<?php } ?>
