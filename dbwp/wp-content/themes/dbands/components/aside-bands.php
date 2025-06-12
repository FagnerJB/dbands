<?php

use cavWP\Models\Post;
use dbp\Band\Band;
use dbp\Band\Utils;
use dbp\Common\Utils as CommonUtils;

if (!is_tag()) {
   $Post  = new Post();
   $bands = $Post->get('tags');
} else {
   $bands = [null];
}

if (!empty($bands) && !is_wp_error($bands)) {
   foreach ($bands as $key => $band) {
      $Band = new Band($band);

      $rel = '';

      if (is_singular()) {
         $rel = 'rel="tag"';
      }

      ?>
<div class="flex flex-col gap-2 rounded-sm p-3 bg-white">
   <a class="flex flex-col gap-1"
      href="<?php echo $Band->get('link'); ?>"
      <?php echo $rel; ?>>
      <?php

                  if ($logo = $Band->get_logo()) {
                     echo $logo;
                  } else {
                     echo '<div class="text-xl font-semibold">' . $Band->get('name') . '</div>';
                  }

      ?>
      <div class="font-medium">
         <?php echo $Band->get_genre(); ?>
      </div>
   </a>
   <div>
      <ul>
         <?php

         if (is_tag()) {
            $linfo = Utils::get_metas('info');

            foreach (array_keys($linfo) as $key) {
               $key = str_replace('band_', '', $key);

               if (!empty($Band->meta->{$key})) {
                  $value = $Band->get_meta_link($key, true, '<li>', '</li>');

                  if (!empty($value)) {
                     echo $value;
                  }
               }
            }
         }

      if (!is_tag()) {
         echo $Band->get_meta_link('itunes', true, '<li>', '</li>');
         echo $Band->get_meta_link('posts', true, '<li>', '</li>');
      }

      echo $Band->get_meta_link('lyrics', true, '<li>', '</li>');

      if (!is_tag()) {
         echo $Band->get_meta_link('tag', true, '<li>', '</li>');
      }

      ?>
      </ul>
      <?php

            if (is_tag()) {
               ?>
      <h3 class="font-medium text-lg mt-4">Buscar mais</h3>
      <ul>
         <?php

                     $search_options = CommonUtils::get_search_options();

               foreach ($search_options as $key => $option) {
                  if (!in_array($key, ['artista', 'videos'])) {
                     continue;
                  }

                  ?>
         <li>
            <a href="<?php echo CommonUtils::get_search_link($Band->get('name'), $key); ?>"
               title="<?php echo $option['search_page']; ?>"
               rel="nofollow">
               <i
                  class="<?php echo $option['service']; ?> text-center fa-fw"></i>
               <?php echo $option['subtitle']; ?>
            </a>
         </li>
         <?php
               }

               ?>
      </ul>
      <h3 class="font-medium text-lg mt-4">Links externos</h3>
      <ul>
         <?php

               $links = Utils::get_metas('links');

               foreach (array_keys($links) as $key) {
                  $key = str_replace('band_', '', $key);

                  if (!empty($Band->meta->{$key})) {
                     $value = $Band->get_meta_link($key, true, '<li>', '</li>');

                     if (!empty($value)) {
                        echo $value;
                     }
                  }
               }

               ?>
      </ul>
      <?php

            }

      ?>
   </div>
</div>
<?php

      if (is_tag() && !empty($Band->meta->itunes_album)) {
         ?>
<section class="flex flex-col gap-2 wjs">
   <h3 class="font-medium text-xl mt-5">
      <?php esc_html_e('Álbum recomendado no iTunes', 'dbands'); ?>
   </h3>
   <iframe height="450" width="100%" loading="lazy"
           src="https://embed.music.apple.com/br/album/album/<?php echo $Band->meta->itunes_album; ?>?itscg=30200&amp;itsct=music_box_player&amp;ls=1&amp;app=music&amp;mttnsubad=1612051984&amp;at=10lSby&amp;theme=dark"
           sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation-by-user-activation"
           allow="autoplay *; encrypted-media *; clipboard-write"
           style="border: 0px; border-radius: 12px; width: 100%; height: 450px; max-width: 660px;"></iframe>
</section>
<?php

      }

      if (is_tag() && !empty($Band->meta->spotify)) {
         ?>
<section class="flex flex-col gap-2 wjs">
   <h3 class="font-medium text-xl mt-5">
      <?php esc_html_e('Mais tocadas no Spotify', 'dbands'); ?>
   </h3>
   <iframe class="w-full"
           src="https://open.spotify.com/embed/artist/<?php echo $Band->meta->spotify; ?>" loading="lazy"
           width="350" height="390" frameborder="0" allowtransparency="true" allow="encrypted-media"
           title="<?php printf(esc_html__('Músicas mais tocadas de %s no Spotify', 'dbands'), $Band->get('name')); ?>"></iframe>
</section>
<?php

      }

      get_component('aside-edit-this', [
         'type' => 'term',
         'ID'   => $Band->ID,
         'text' => esc_html__('Editar esta banda', 'dbands'),
      ]);
   }
}
?>
