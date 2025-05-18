<?php

namespace dbp\TV;

class Utils
{
   public static function get_video_thumb($video_ID)
   {
      return 'https://i.ytimg.com/vi/' . $video_ID . '/mqdefault.jpg';
   }
}
