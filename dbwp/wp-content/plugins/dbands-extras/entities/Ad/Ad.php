<?php

namespace dbp\Ad;

class Ad
{
   private $position = '';

   public function __construct($position)
   {
      $this->position = $position;
   }

   public function ad()
   {
      $positions = [
         'footer' => '1974786989',
         'aside'  => '1017762680',
      ];

      return <<<AD
      <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-7971845527728300"
         data-ad-slot="{$positions[$this->position]}"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
      AD;
   }

   public function placeholder()
   {
      $positions = [
         'footer' => '728x90',
         'aside'  => '300x250',
      ];

      return "<img src=\"https://placehold.co/{$positions[$this->position]}/png\" />";
   }

   public function echo(): void
   {
      if ('production' === wp_get_environment_type()) {
         echo $this->ad();
      } else {
         echo $this->placeholder();
      }
   }
}
