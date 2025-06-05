<?php

namespace dbt\Shortcodes;

class group_bands
{
   public function __construct()
   {
      add_shortcode('group_bands', [$this, 'content']);
   }

   public function content($atts)
   {
      extract(shortcode_atts(['by' => ''], $atts));

      $bands_all = get_terms([
         'taxonomy'   => 'post_tag',
         'order'      => 'ASC',
         'hide_empty' => false,
      ]);

      if (empty($bands_all)) {
         return '';
      }

      $categories = [];

      if ('genre' === $by) {
         foreach ($bands_all as $band) {
            $genre = strtolower(trim(get_term_meta($band->term_id, 'band_genre', true)));

            if (array_key_exists($genre, $categories)) {
               $categories[$genre]['items'][] = $band;
               $categories[$genre]['count']++;
            } else {
               $categories[$genre]['items'] = [$band];
               $categories[$genre]['count'] = 0;
            }
         }

         ksort($categories);
      } elseif ('abc' === $by) {
         $alphabet = range('A', 'Z');

         foreach ($bands_all as $band) {
            $letter = strtoupper(remove_accents($band->name[0]));

            if (!in_array($letter, $alphabet)) {
               $letter = '#';
            }

            if (array_key_exists($letter, $categories)) {
               $categories[$letter]['items'][] = $band;
               $categories[$letter]['count']++;
            } else {
               $categories[$letter]['items'] = [$band];
               $categories[$letter]['count'] = 0;
            }
         }
      }

      $bands_total = count($bands_all);
      $column_size = floor($bands_total / 3);
      $in_column   = 0;
      $in_total    = 0;

      $html_return = '<div class="grid auto-rows-auto grid-cols-1 sm:grid-cols-3 gap-3 text-left">';

      foreach ($categories as $title => $category) {
         if (0 === $in_column) {
            $html_return .= '<div>';
         }

         $html_return .= '<h3 id="' . sanitize_title($title) . '" class="capitalize">' . $title . '</h3>';
         $html_return .= '<ul class="flex flex-col gap-1">';

         foreach ($category['items'] as $band) {
            $name  = apply_filters('the_title', $band->name);
            $link  = esc_url(get_tag_link($band->term_id));
            $title = sprintf(_n('Com %s publicação', 'Com %s publicações', $band->count, 'dbands'), $band->count);
            $html_return .= '<li><a href="' . $link . '" title="' . $title . '">' . $name . '</a></li>';
            $in_column++;
            $in_total++;
         }

         $html_return .= '</ul>';

         if ($in_total === $bands_total) {
            $html_return .= '</div>';

            break;
         }

         if ($in_column >= $column_size) {
            $html_return .= '</div>';
            $in_column = 0;
         }
      }

      $html_return .= '</div>';

      return $html_return;
   }
}
