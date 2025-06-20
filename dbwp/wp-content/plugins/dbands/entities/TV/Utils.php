<?php

namespace dbp\TV;

class Utils
{
   public static function get_video_thumb($video_ID)
   {
      return 'https://i.ytimg.com/vi/' . $video_ID . '/mqdefault.jpg';
   }

   public static function video_attrs($video_ID): void
   {
      if (empty($video_ID)) {
         return;
      }

      $tv_link = get_permalink(8261);

      if (11 === strlen($video_ID)) {
         $yt_link = 'https://www.youtube.com/watch?v=';
      } else {
         $yt_link = 'https://www.youtube.com/playlist?list=';
      }

      echo " href=\"{$yt_link}{$video_ID}\" target=\"_blank\" x-bind:href=\"'{$tv_link}/{$video_ID}'\"";
   }
}
