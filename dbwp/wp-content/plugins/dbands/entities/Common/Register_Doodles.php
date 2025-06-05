<?php

namespace dbp\Common;

class Register_Doodles
{
   private $current = false;
   private $dates   = [
      // October 31th
      'halloween' => [
         'start' => 'October 25th',
         'end'   => 'November 1st',
      ],
      // May 4th
      'starwars' => [
         'start' => 'May 3rd',
         'end'   => 'May 5th',
      ],
      // April 1st
      'aprilsfool' => [
         'start' => 'March 31th',
         'end'   => 'April 2nd',
      ],
      // December 25th - January 1st
      'xmas' => [
         'start' => 'December 24th',
         'end'   => 'January 2nd',
      ],
      // July 13th
      'rockday' => [
         'start' => 'July 7th',
         'end'   => 'July 14th',
      ],
   ];

   public function __construct()
   {
      if ($this->is_between()) {
         add_filter('theme_mod_custom_logo', [$this, 'change_logo']);
      }
   }

   public function change_logo($logo_ID)
   {
      $new_logo = get_posts([
         'name'      => $this->current,
         'fields'    => 'ids',
         'post_type' => 'attachment',
      ]);

      if (empty($new_logo)) {
         return $logo_ID;
      }

      return $new_logo[0];
   }

   private function is_between()
   {
      $today = time();

      foreach ($this->dates as $day => $dates) {
         $start = strtotime($dates['start']);

         if ('xmas' === $day && 'Jan' !== date('M')) {
            $year = date('Y', strtotime('next year'));
            $end  = strtotime($dates['end'] . ' ' . $year);
         } else {
            $end = strtotime($dates['end']);
         }

         if ($start <= $today && $today < $end) {
            $this->current = $day;

            return $day;
            break;
         }
      }

      return false;
   }
}
