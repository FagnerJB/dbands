<?php

use dbp\Author\Author;
use dbp\News\News;

$News   = new News();
$Author = new Author($News->get('author'));
$title  = sprintf(esc_html__('Publicações de %s', 'dbands'), $Author->get('name'));

?>
<div id="aside" class="sticky top-3 flex flex-col gap-5">
   <?php

   get_component('aside-edit-this', [
      'type' => 'post',
      'text' => esc_html__('Editar esta publicação', 'dbands'),
   ]);

?>
   <div>
      <div class="flex gap-2 items-start">
         <a class="shrink-0"
            href="<?php echo $Author->get('link'); ?>"
            title="<?php echo $title; ?>" rel="author">
            <?php echo $Author->get('avatar', size: 75); ?>
         </a>
         <h2 class="flex flex-col gap-1">
            <span class="text-xl font-semibold">
               <a href="<?php echo $Author->get('link'); ?>"
                  title="<?php echo $title; ?>" rel="author">
                  <?php

               printf(esc_html__('Por %s', 'dbands'), $Author->get('name'));

?>
               </a>
            </span>
            <p class="text-sm line-clamp-2">
               <?php echo $Author->get('description'); ?>
            </p>
         </h2>
      </div>
      <?php

      get_component('aside-postdate');

?>
   </div>
   <?php

get_component('tags', [
   'post'       => $News,
   'with_links' => true,
]);

get_component('aside-bands');

get_component('aside-share');

get_component('aside-ad');

?>
</div>
