<?php

namespace dbp\Album;

class Utils
{
   public static function get_albums()
   {
      global $wpdb;

      return $wpdb->get_results(
         <<<EOD
         SELECT
            t.term_id,
            t.name,
            ma.meta_value AS artist,
            my.meta_value AS year,
            mp.meta_value AS producer,
            mc.meta_value AS cover,
            count
         FROM {$wpdb->terms} AS t
         LEFT JOIN {$wpdb->termmeta} AS ma
            ON ma.term_id = t.term_id
            AND ma.meta_key = 'album_artist'
         LEFT JOIN {$wpdb->termmeta} AS my
            ON my.term_id = t.term_id
            AND my.meta_key = 'album_year'
         LEFT JOIN {$wpdb->termmeta} AS mp
            ON mp.term_id = t.term_id
            AND mp.meta_key = 'album_producer'
         LEFT JOIN {$wpdb->termmeta} AS mc
            ON mc.term_id = t.term_id
            AND mc.meta_key = 'album_cover'
         JOIN {$wpdb->term_taxonomy} AS x
            ON x.term_id = t.term_id
         WHERE x.taxonomy = 'albuns'
         ORDER BY ma.meta_value ASC, my.meta_value DESC;
         EOD
      );
   }
}
