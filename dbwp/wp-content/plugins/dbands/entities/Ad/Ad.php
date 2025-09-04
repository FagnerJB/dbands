<?php

namespace dbp\Ad;

class Ad
{
   private $position = '';

   public function __construct($position)
   {
      $this->position = $position;

      echo '<div>';
      $this->ad();
      $this->fallback();
      echo '</div>';
   }

   public function ad(): void
   {
      $positions = [
         'footer' => '1974786989',
         'aside'  => '1017762680',
      ];

      echo <<<HTML
      <ins class="adsbygoogle slot-{$this->position}"
         style="display:block;margin:auto;"
         data-ad-client="ca-pub-7971845527728300"
         data-ad-slot="{$positions[$this->position]}"
         data-ad-format="auto"
         data-full-width-responsive="true">
      </ins>
      HTML;
   }

   private function fallback(): void
   {
      $sizes = [
         'footer' => get_option('dbands-ads-placeholder-footer', 0),
         'aside'  => get_option('dbands-ads-placeholder-aside', 0),
      ];
      $link = get_option('dbands-ads-placeholder-link', '');

      $image_url = wp_get_attachment_url($sizes[$this->position]);

      echo <<<HTML
      <a href="{$link}" target="_blank" x-cloak>
         <img src="{$image_url}" alt="" />
      </a>
      HTML;
   }
}
