<section class="flex flex-col gap-2">
   <h2 class="text-xl font-medium">
      <?php esc_html_e('Todos os autores', 'dbands'); ?>
   </h2>
   <ul class="flex flex-wrap gap-1">
      <?php

      $users = get_users([
         'orderby'             => 'post_count',
         'order'               => 'DESC',
         'has_published_posts' => ['post']
      ]);

      foreach ($users as $author) {

      ?>
         <li>
            <a class="btn-alt" href="<?php echo get_author_posts_url($author->ID) ?>" title="<?php printf(esc_html__('Publicações de %s', 'dbands'), $author->display_name) ?>">
               <?php echo $author->display_name ?>
            </a>
         </li>
      <?php

      }

      ?>
   </ul>
</section>
