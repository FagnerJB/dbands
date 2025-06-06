<?php

namespace dbp\News;

use cavWP\Models\Post;

class News extends Post
{
   public function get_social_posts()
   {
      $social_posts = $this->get('social_postIDs');

      if (empty($social_posts)) {
         $social_posts = $this->get('cav-social_posts');
      }

      if (!empty($social_posts['tw'])) {
         $social_posts['twitter'] = $social_posts['tw'];
         unset($social_posts['tw']);
      }

      if (!empty($social_posts['fb'])) {
         $social_posts['facebook'] = $social_posts['fb'];
         unset($social_posts['fb']);
      }

      return $social_posts;
   }
}
