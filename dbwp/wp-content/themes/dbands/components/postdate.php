<?php

use cavWP\Models\Post;

$Post = new Post();

echo '<div class="flex flex-col gap-1 text-sm">';

echo '<div>';
printf(
   esc_html__('Publicado em %s.', 'dbands'),
   $Post->get('date', with_html: true),
);
echo '</div>';

if ($Post->has_modified()) {
   echo '<div>';
   printf(
      esc_html__('Modificado em %s.', 'dbands'),
      $Post->get('modified', with_html: true),
   );
   echo '</div>';
}

echo '</div>';
