<?php

namespace dbp\Ad;

class Ad
{
   private $position  = '';
   private $positions = [
      'footer' => [
         'slot'   => '1974786989',
         'width'  => 728,
         'height' => 90,
      ],
      'aside' => [
         'slot'   => '1017762680',
         'width'  => 300,
         'height' => 250,
      ],
   ];

   public function __construct($position)
   {
      if (is_user_logged_in()) {
         return;
      }

      $this->position = $position;

      echo '<div class="flex flex-col gap-1">';
      $this->ad();
      $this->fallback();
      echo '</div>';
   }

   public function ad(): void
   {
      echo <<<HTML
      <ins class="adsbygoogle slot-{$this->position}"
         style="display:block;margin:auto;"
         data-ad-client="ca-pub-7971845527728300"
         data-ad-slot="{$this->positions[$this->position]['slot']}"
         data-ad-format="auto"
         data-full-width-responsive="true">
      </ins>
      HTML;
   }

   private function fallback(): void
   {
      $image_ID = get_option('dbands-ads-placeholder-' . $this->position, 0);
      $link     = get_option('dbands-ads-placeholder-link', '');
      $text     = get_option('dbands-ads-placeholder-text', '');

      $image_url = wp_get_attachment_url($image_ID);

      echo <<<HTML
      <a class="flex justify-center" href="{$link}" target="_blank" title="{$text}" rel="external" x-cloak>
         <img src="{$image_url}" alt="" width="{$this->positions[$this->position]['width']}" height="{$this->positions[$this->position]['height']}" />
      </a>
      HTML;
   }
}
