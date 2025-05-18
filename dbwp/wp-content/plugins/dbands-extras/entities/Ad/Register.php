<?php

namespace dbp\Ad;

class Register
{
   public function __construct()
   {
      add_action('wp_enqueue_scripts', [$this, 'add_scripts'], 99);
      add_action('wpe_head_scripts', [$this, 'add_footer'], 99);
   }

   public function add_scripts()
   {
      wp_enqueue_script('adsbygoogle', 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7971845527728300', [], null, [
         'strategy' => 'async'
      ]);
   }

   public function add_footer()
   {
      echo '<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
   }
}
