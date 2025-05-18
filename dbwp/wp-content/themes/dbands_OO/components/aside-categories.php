<?php

if (is_home()) {
   $title_class = 'font-semibold text-sm md:text-2xl';
} else {
   $title_class = 'font-medium text-xl';
}

?>
<section class="flex flex-col gap-2">
   <h2 class="<?php echo $title_class ?>">
      <?php esc_html_e('Todas as categorias', 'dbands'); ?>
   </h2>
   <?php

   get_component('tags', [
      'with_links' => true,
      'categories' => get_categories([
         'orderby' => 'count',
         'order'   => 'DESC'
      ])
   ]);

   ?>
</section>
