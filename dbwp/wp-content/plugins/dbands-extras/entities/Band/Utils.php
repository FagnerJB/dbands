<?php

namespace dbp\Band;

class Utils
{
   public static function select_random($select_by = 'cover')
   {
      if (!in_array($select_by, ['cover', 'logo'])) {
         return false;
      }

      $path = wp_upload_dir()['basedir'] . '/' . $select_by . 's/';

      if (!is_dir($path)) {
         return false;
      }

      $directory = opendir($path);

      while ($file = readdir($directory)) {
         if ($file !== "." && $file !== ".." && strlen($file) > 4) {
            $all_files[] = str_replace(array('.jpg', '.png'), '', $file);
         }
      }

      closedir($directory);

      return $all_files[array_rand($all_files)];
   }

   public static function get_suggests($limit = 6)
   {
      $users      = get_users(['meta_key' => 'user_fav_bands']);
      $already_in = [];

      foreach ($users as $user) {
         $user_favorites = explode(',', get_user_meta($user->ID, 'user_fav_bands', true));
         $user_picked    = $user_favorites[array_rand($user_favorites)];
         $band_name      = trim($user_picked);

         if (in_array($band_name, $already_in)) {
            continue;
         }

         $suggests[] = (object) [
            'term'   => new Band($band_name),
            'origin' => $user->display_name,
         ];
         $already_in[] = $band_name;
      }

      $popular_bands = get_terms([
         'taxonomy' => 'post_tag',
         'orderby'  => 'count',
         'order'    => 'DESC',
         'number'   => 15,
      ]);

      shuffle($popular_bands);

      foreach ($popular_bands as $band) {
         if (in_array($band->name, $already_in)) {
            continue;
         }

         if (count($suggests) === $limit) {
            break;
         }

         $suggests[] = (object) [
            'term'   => new Band($band),
            'origin' => sprintf(esc_html__('%d publicações'), $band->count),
         ];
      }

      if (empty($suggests)) {
         return false;
      }

      shuffle($suggests);

      return $suggests;
   }

   public static function get_youtube_info($channel_id)
   {
      $youtube_url = 'https://www.youtube.com/';

      if (1 === preg_match('/[a-zA-Z0-9_-]{24}/', $channel_id)) {
         $youtube_url .= 'channel/';
         $youtube_text = 'Canal no YouTube';
      } else {
         $youtube_url .= 'user/';
         $youtube_text = 'Canal ' . $channel_id . ' no YouTube';
      }

      $youtube_url .= $channel_id;

      return [esc_url($youtube_url), $youtube_text];
   }

   public static function get_prefix_years($active_years)
   {
      if (4 === strlen($active_years)) {
         $years_text = esc_html__('Fundação', 'dbands');
      } else {
         $years_text = esc_html__('Período de atividade', 'dbands');
      }

      return $years_text . ': ' . $active_years;
   }

   public static function change_upload_folder($param)
   {
      global $upload_subdir;
      $subdir = @$upload_subdir;

      $param['path']   = substr($param['path'], 0, -7) . $subdir;
      $param['url']    = substr($param['url'], 0, -7) . $subdir;
      $param['subdir'] = substr($param['subdir'], 0, -7) . $subdir;

      return $param;
   }

   public static function get_metas($category = 'all')
   {
      $metas = [
         'band_photo_credits' => [
            'name'        => 'Créditos',
            'description' => 'Autor, empresa, créditos para imagem',
            'category'    => 'cover',
         ],
         'band_genre' => [
            'name'        => 'Gênero',
            'description' => 'Principal gênero musical',
            'required'    => true,
            'category'    => 'info',
         ],
         'band_site' => [
            'name'        => 'Site oficial',
            'description' => 'Link para site oficial',
            'type'        => 'url',
            'category'    => 'links',
         ],
         'band_itunes' => [
            'name'        => 'Apple Music',
            'description' => 'Artista URI/número ID no iTunes/Apple Music',
            'category'    => 'links',
         ],
         'band_spotify' => [
            'name'        => 'Spotify',
            'description' => 'ID/URI do artista no Spotify',
            'category'    => 'links',
         ],
         'band_itunes_album' => [
            'name'        => 'Álbum no Apple Music ',
            'description' => 'Álbum ID no iTunes/Apple Music',
            'category'    => 'embed',
         ],
         'band_deezer' => [
            'name'        => 'Deezer',
            'description' => 'Link para página do artista no Deezer',
            'type'        => 'url',
            'category'    => 'links',
         ],
         'band_youtube' => [
            'name'        => 'YouTube',
            'description' => 'ID/URI do YouTube de 24 caracteres ou Usuário. Separe com <strong>, (vírgula)</strong> para vários',
            'category'    => 'links',
         ],
         'band_lastfm' => [
            'name'        => 'Last.fm',
            'description' => 'ID/URI no Last.fm',
            'category'    => 'links',
         ],
         'band_musixmatch' => [
            'name'        => 'MusixMatch',
            'description' => 'ID/URI no MusixMatch',
            'category'    => 'links',
         ],
         'band_facebook' => [
            'name'        => 'Facebook',
            'description' => 'Usuário no Facebook',
            'category'    => 'links',
         ],
         'band_instagram' => [
            'name'        => 'Instagram',
            'description' => 'Usuário no Instagram, sem @',
            'category'    => 'links',
         ],
         'band_twitter' => [
            'name'        => 'Twitter',
            'description' => 'Usuário no Twitter, sem @',
            'category'    => 'links',
         ],
         'band_active_years' => [
            'name'        => 'Atividade',
            'description' => 'Anos de atividade, e.g. 2000[ - 2006]',
            'category'    => 'info',
         ],
         'band_city' => [
            'name'        => 'Origem',
            'description' => 'Cidade e estado ou país de origem',
            'category'    => 'info',
         ],
      ];

      if ('all' === $category) {
         return $metas;
      }

      return array_filter($metas, fn($meta) => $meta['category'] === $category);
   }
}
