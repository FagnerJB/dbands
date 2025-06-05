<?php

namespace dbp\Lyric;

use cavWP\Post;

class Lyric extends Post
{
   public function get_artists()
   {
      $bands = $this->get('tags');

      if (empty($bands) || is_wp_error($bands)) {
         return '';
      }

      return implode(', ', array_map(fn($band) => $band->name, $bands));
   }

   public function get_content()
   {
      $priority = has_filter('the_content', 'wpautop');
      remove_filter('the_content', 'wpautop', $priority);

      $content = parent::get('content');

      add_filter('the_content', 'wpautop', $priority);

      return $content;
   }

   public function get_fullname()
   {
      $bands = $this->get_artists();
      $title = $this->get('title');

      if (empty($bands)) {
         return $title;
      }

      return $bands . ' - ' . $title;
   }
}
