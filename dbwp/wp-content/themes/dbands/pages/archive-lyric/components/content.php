<?php

$post_type = get_queried_object();

use dbp\Album\Utils as AlbumUtils;

?>
<div class="container-content">
   <main class="content">
      <h1 class="mb-7">
         <?php echo $post_type->labels->archives; ?>
      </h1>
      <div>
      <?php

      $all_albums = AlbumUtils::get_albums();

if (!empty($all_albums)) {
   $artists = [];

   $output = '';

   foreach ($all_albums as $album) {
      $album_tracks = get_posts([
         'numberposts' => -1,
         'post_type'   => 'lyric',
         'order'       => 'ASC',
         'orderby'     => 'menu_order',
         'tax_query'   => [[
            'taxonomy' => 'albuns',
            'terms'    => $album->term_id,
         ]],
      ]);

      if (!in_array($album->artist, $artists)) {
         $artists[] = $album->artist;
         $attr      = esc_attr(sanitize_title($album->artist));

         $output .= <<<TITLE
         <h2 id="{$attr}">{$album->artist}</h2>
         TITLE;
      }

      if (!empty($album->cover)) {
         $album_cover = wp_get_attachment_image($album->cover, 'thumbnail', false, [
            'class' => 'size-30 sm:size-37 shrink-0',
         ]);
      } else {
         $album_cover = '<img class="size-30 sm:size-37 shrink-0" src="https://placehold.co/150x150/png?text=' . urlencode($album->name) . '" alt="">';
      }

      $tracks = '';

      foreach ($album_tracks as $track) {
         $link = get_permalink($track);

         $tracks .= <<<TRACK
         <li>{$track->menu_order}. <a href="{$link}">{$track->post_title}</a></li>
         TRACK;
      }

      $album_year = '';

      if (!empty($album->year)) {
         $album_year = " ({$album->year})";
      }

      $album_producer = '';

      if (!empty($album->producer)) {
         $album_producer = "<div>Produção: {$album->producer}</div>";
      }

      $count = sprintf(_n('%s tradução', '%s traduções', $album->count, 'dbands'), $album->count);

      $output .= <<<ALBUM
      <div class="flex gap-3 mb-5">
      {$album_cover}
      <details class="w-full">
         <summary class="h-30 sm:h-37 custom-marker">
            <div class="flex flex-col gap-1">
               <h3>{$album->name}{$album_year}</h3>
               {$album_producer}
               <div>{$count}</div>
            </div>
         </summary>
         <ul class="flex flex-col !list-none">
            {$tracks}
         </ul>
      </details>
      </div>
      ALBUM;
   }

   $summary = '<h2>Índice dos artistas</h2>';
   $summary .= '<ul class="flex flex-col gap-1">';

   foreach ($artists as $artist) {
      $attr = esc_attr(sanitize_title($artist));
      $summary .= "<li><a href=\"#{$attr}\">{$artist}</a></li>";
   }
   $summary .= '</ul>';

   echo $summary . $output;
}

?>
      </div>
   </main>
   <aside>
      <?php get_page_component('archive-lyric', 'aside'); ?>
   </aside>
</div>
