<div class="container-content">
   <section>
      <article id="page-404" <?php post_class('content'); ?>>
         <header class="mb-7">
            <h1>
               <?php esc_html_e('Página não encontrada', 'dbands'); ?>
            </h1>
         </header>
         <div class="text-justify">
            <?php get_component('loop-empty'); ?>
         </div>
      </article>
   </section>
</div>
