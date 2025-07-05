<?php

namespace dbp\Common;

class Cover
{
   public function get($key)
   {
      return match ($key) {
        'link'  => '#',
         'name' => 'Séries e Filmes',
      };
   }

   public function get_cover()
   {
      $cover = get_posts([
         'post_type' => 'attachment',
         'title' => 'streaming'
      ]);

      return wp_get_attachment_url($cover[0]->ID);
   }
}
