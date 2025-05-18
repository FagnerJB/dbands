<?php

use dbp\News\News;

$News  = new News();
$first = 0 === $wp_query->current_post;

?>
<li class="<?php echo $first ? 'md:col-span-2' : '' ?>">
   <article>
      <a class="group block" href="<?php echo $News->get('link') ?>" rel="bookmark">
         <div class="relative w-full overflow-hidden <?php echo $first ? 'aspect-square md:aspect-video' : 'aspect-square' ?>">
            <?php

            $size = $first ? 'medium_large' : 'medium';

            echo $News->get('thumbnail', size: $size, with_html: true, attrs: [
               'class' => 'size-full object-cover object-center opacity-90 group-hover:opacity-100 group-focus:opacity-100 group-hover:scale-110 group-focus:scale-110 transition-all',
            ]);

            ?>
            <div class="absolute bottom-2 inset-x-0 px-2">
               <?php

               get_component('tags', [
                  'post' => $News,
               ]);

               ?>
            </div>
         </div>
         <div class="flex flex-col items-start gap-1 py-2">
            <h3 class="font-semibold text-sm line-clamp-2 <?php echo $first ? 'md:text-xl' : 'md:text-lg md:line-clamp-3' ?>">
               <?php echo $News->get('title'); ?>
            </h3>
            <div class="text-xxs md:text-sm">
               <?php

               printf(
                  esc_html__('por %s há %s', 'dbands'),
                  get_the_author(),
                  human_time_diff(strtotime(get_the_date('Y-m-j H:i:s')))
               );

               ?>
            </div>
         </div>
      </a>
   </article>
</li>
