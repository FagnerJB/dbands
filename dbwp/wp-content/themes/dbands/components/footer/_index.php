<?php

use dbp\Ad\Ad;

do_action('get_footer');

get_component(['footer', 'gallery']);
get_component(['footer', 'tv']);

?>
<div x-on:scroll.window.debounce.passive="handleScroll"></div>
<div x-on:popstate.window.debounce="handlePopState"></div>
<footer id="footer">
   <div class="container py-3 bg-zinc-800 text-zinc-200">
      <h2 class="sr-only"><?php esc_html_e('Links do rodapé', 'dbands'); ?></h2>
      <div class="flex justify-center items-center pt-3">
         <?php

         $ad = new Ad('footer');
$ad->echo();

?>
      </div>
      <div id="footer-menu" class="flex flex-col lg:flex-row gap-5 mt-3">
         <div class="flex flex-col gap-3 lg:w-1/3">
            <div class="max-w-80">
               <?php the_custom_logo(); ?>
            </div>
            <p><?php bloginfo('description'); ?></p>
         </div>
         <div class="with-links flex flex-col lg:flex-row gap-5 lg:gap-3 lg:w-2/3 mt-8.5">
            <?php

   foreach (get_nav_menu_locations() as $menu) {
      $menu_obj = wp_get_nav_menu_object($menu);

      if (isset($menu_obj->term_id)) {
         wp_nav_menu([
            'menu'            => $menu_obj->term_id,
            'menu_class'      => '',
            'container'       => 'nav',
            'container_class' => 'lg:w-1/3',
            'items_wrap'      => '<h3 class="font-bold text-lg mb-2">' . $menu_obj->name . '</h3><ul id="%1$s" class="flex flex-wrap lg:flex-col gap-3 lg:gap-1 %2$s">%3$s</ul>',
         ]);
      }
   }

?>

         </div>
      </div>
   </div>
   <div class="container flex flex-col gap-1 mt-3 mb-6 text-center text-white text-sm md:text-base" aria-hidden="true">
      <div class="with-links">
         <span class="whitespace-nowrap">2010-<?php echo date('Y'); ?> - <?php bloginfo('name'); ?> &mdash;</span> <wbr>
         <a class="whitespace-nowrap" href="<?php echo get_permalink(11543); ?>">Acessibilidade</a> &bull;
         <a class="whitespace-nowrap" href="<?php echo get_permalink(11542); ?>" rel="terms-of-service">Termos de Uso</a> &bull;
         <a class="whitespace-nowrap" href="<?php echo get_permalink(11537); ?>" rel="privacy-policy">Política de Privacidade</a>
      </div>
      <a href="https://ctrl.altvers.net" target="_blank" title="Este projeto faz parte de CtrlAltVerso" rel="external">🌌</a>
   </div>
</footer>
<?php

wp_footer();

?>
</body>

</html>
