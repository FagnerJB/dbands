<?php

namespace dbp\Lyric;

class Utils
{
   public  static function get_lyrics($page = 1)
   {
      $first_page = 6;
      $per_page = 9;

      $length = 1 === $page ? $first_page : $per_page;
      $offset = 1 === $page ? 0 : $first_page + (($page - 2) * $per_page);

      $lyrics_IDs = get_posts([
         'fields'         => 'ids',
         'post_type'      => 'lyric',
         'posts_per_page' => $length,
         'offset'         => $offset,
      ]);

      $lyrics = [];

      if (empty($lyrics_IDs)) {
         return $lyrics;
      }

      foreach ($lyrics_IDs as $lyrics_ID) {
         $lyrics[] = new Lyric($lyrics_ID);
      }

      return $lyrics;
   }
}
