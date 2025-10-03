<?php

namespace dbp\Author;

use cavWP\Models\User;

class Author extends User
{
   public function get_favorites($format = 'name')
   {
      $favorites = $this->get('user_fav_bands');

      if (empty($favorites)) {
         return false;
      }

      $favorites = explode(',', $favorites);

      if ('name' === $format) {
         return array_map('trim', $favorites);
      }

      if ('term' === $format) {
         return array_map(fn($term) => get_term_by('name', trim($term), 'post_tag'), $favorites);
      }
   }
}
