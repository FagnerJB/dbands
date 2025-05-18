<?php

use wpe\Post;

$Post = new Post();

?>
<div class="text-sm mt-1">
   <?php

   printf(
      esc_html__('Publicado em %s.', 'dbands'),
      $Post->get('date', with_html: true),
   );

if ($Post->has_modified()) {
   echo '<wbr /> ';
   printf(
      esc_html__('Modificado em %s.', 'dbands'),
      $Post->get('modified', with_html: true),
   );
}

?>
</div>
