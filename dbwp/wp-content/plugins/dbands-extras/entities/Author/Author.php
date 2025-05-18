<?php

namespace dbp\Author;

use wpe\User;

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

   public function get_socials()
   {
      $meta_keys = Utils::get_metas();

      $socials = [];

      foreach ($meta_keys as $key => $meta_info) {
         if ('user_fav_bands' === $key) {
            continue;
         }

         $username = $this->get_meta($key);

         if (empty($username)) {
            continue;
         }

         $socials[$key] = array_merge($meta_info, ['username' => $username]);
      }

      return $socials;
   }
}
