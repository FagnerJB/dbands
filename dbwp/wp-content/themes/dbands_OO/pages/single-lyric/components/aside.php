<?php

use dbp\Lyric\Lyric;

$Lyric = new Lyric();

?>
<div id="aside" class="sticky top-3 flex flex-col gap-5">
   <?php

   get_component('aside-edit-this', [
      'type' => 'post',
      'text' => esc_html__('Editar esta letra', 'dbands'),
   ]);

   ?>
   <div class="flex flex-col col-2">
      <h2 class="font-semibold text-xl">
         <?php echo $Lyric->get('title') ?>
      </h2>
      <ul class="flex flex-col gap-1">
         <?php

         if ($single_name = $Lyric->get('single')) {

         ?>
            <li>
               <?php

               printf(
                  esc_html__('%sSingle:%s %s', 'dbands'),
                  '<span class="font-medium">',
                  '</span>',
                  $single_name
               );

               ?>
            </li>
         <?php

         }

         $album = $Lyric->get('terms', taxonomy: 'albuns');

         if (!empty($album)) {

         ?>
            <li>
               <?php

               printf(
                  esc_html__('%sÁlbum:%s %s', 'dbands'),
                  '<span class="font-medium">',
                  '</span>',
                  $album[0]->name
               );

               ?>
            </li>
         <?php

         }

         if ($composer = $Lyric->get('compositor')) {

         ?>
            <li>
               <?php

               printf(
                  esc_html__('%sComposição:%s %s', 'dbands'),
                  '<span class="font-medium">',
                  '</span>',
                  $composer
               );

               ?>
            </li>
         <?php

         }

         ?>
      </ul>
   </div>
   <div class="flex flex-col col-2">
      <?php

      if ($translator = $Lyric->get('tradutor')) {

      ?>
         <div class="font-semibold text-xl">
            <?php

            printf(esc_html__('Traduzido por %s', 'dbands'), $translator)

            ?>
         </div>
      <?php

      }

      get_component('aside-postdate');

      ?>
   </div>
   <?php

   get_component('aside-bands');

   get_component('aside-ad');

   ?>
</div>
