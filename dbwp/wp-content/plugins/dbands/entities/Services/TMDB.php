<?php

namespace dbp\Services;

class TMDB
{
   public $image_url = ' https://image.tmdb.org/t/p/';
   public $max_pages;
   private $base_url = 'https://api.themoviedb.org/3';
   private $genres;
   private $headers;
   private $language;
   private $providers_id;

   public function __construct()
   {
      $this->language = str_replace('_', '-', get_locale());
      $this->headers  = [
         'Authorization' => 'Bearer ' . TMDB_TOKEN ?? '',
         'accept'        => 'application/json',
      ];
   }

   public function get_genres($type = 'movie')
   {
      $request = wp_remote_get("{$this->base_url}/genre/{$type}/list?" . http_build_query([
         'language' => substr($this->language, 0, 2),
      ]), [
         'headers'        => $this->headers,
         'cache_duration' => '2 months',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($request)) {
         return [];
      }

      if (200 !== \wp_remote_retrieve_response_code($request)) {
         return [];
      }

      $body = json_decode(\wp_remote_retrieve_body($request), true);

      if (empty($body['genres'])) {
         return [];
      }

      foreach ($body['genres'] as $genre) {
         $this->genres[$type][$genre['id']] = $genre['name'];
      }

      return $this->genres;
   }

   public function get_items($page = 1)
   {
      $this->get_providers_list();

      $movies = $this->discover(page: $page);
      $series = $this->discover('tv', $page);

      if (empty($movies['results'])) {
         $movies['results']     = [];
         $movies['total_pages'] = 0;
      }

      if (empty($series['results'])) {
         $series['results']     = [];
         $series['total_pages'] = 0;
      }

      $this->max_pages = max($movies['total_pages'], $series['total_pages']);

      $all = array_merge($movies['results'], $series['results']);

      usort($all, fn($a, $b) => $a['popularity'] - $b['popularity']);

      return array_map(
         function($item) {
            $type     = empty($item['title']) ? 'tv' : 'movie';
            $release  = $item['release_date']   ?? $item['first_air_date'];
            $original = $item['original_title'] ?? $item['original_name'];
            $title    = $item['title']          ?? $item['name'];

            $full_title = $title . ' (';

            if ($title !== $original) {
               $full_title .= "{$original}, ";
            }

            if (!empty($release)) {
               $full_title .= substr($release, 0, 4);
            }

            $full_title .= ')';

            if (empty($item['poster_path'])) {
               $image = 'https://placehold.co/185x278/png?text=' . urlencode($title);
            } else {
               $image = $this->image_url.'w185'.$item['poster_path'];
            }

            if(empty($item['backdrop_path'])){
               $backdrop = 'https://placehold.co/780x278/png?text=' . urlencode($title);;
            }else{
               $backdrop = $this->image_url .'w780'. $item['backdrop_path'];
            }

            return [
               'id'         => $item['id'],
               'type'       => $type,
               'genres'     => implode(', ', array_map(fn($genre_id) => $this->genres[$type][$genre_id], $item['genre_ids'])),
               'full_title' => $full_title,
               'release'    => substr($release, 0, 4),
               'original'   => $original,
               'title'      => $title,
               'overview'   => $item['overview'] ?? '',
               'backdrop' => $backdrop,
               'image'      => $image,
            ];
         },
         $all,
      );
   }

   public function get_providers($id, $type = 'movie')
   {
      $request = wp_remote_get("{$this->base_url}/{$type}/{$id}/watch/providers", [
         'headers'        => $this->headers,
         'cache_duration' => '2 weeks',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($request)) {
         return [];
      }

      if (200 !== \wp_remote_retrieve_response_code($request)) {
         return [];
      }

      $body = json_decode(\wp_remote_retrieve_body($request), true);

      if (empty($body['results'])) {
         return [];
      }

      return $body['results'];
   }

   public function get_providers_list($type = 'movie', $region = 'BR')
   {
      $request = wp_remote_get("{$this->base_url}/watch/providers/{$type}?" . http_build_query([
         'language'     => $this->language,
         'watch_region' => $region,
      ]), [
         'headers'        => $this->headers,
         'cache_duration' => '1 month',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($request)) {
         $this->providers_id = [];
      }

      if (200 !== \wp_remote_retrieve_response_code($request)) {
         $this->providers_id = [];
      }

      $body = json_decode(\wp_remote_retrieve_body($request), true);

      if (empty($body['results'])) {
         $this->providers_id = [];
      }

      $providers = array_filter( $body['results'],fn($provider) => $provider['provider_name'] !== 'Pluto TV');

      $this->providers_id = array_map(fn($provider) => $provider['provider_id'], $providers);
   }

   private function discover($type = 'movie', $page = 1)
   {
      $this->get_genres($type);

      $request = \wp_remote_get("{$this->base_url}/discover/{$type}?" . http_build_query([
         'language'               => $this->language,
         'page'                   => $page,
         'timezone'               => \wp_timezone_string(),
         'with_original_language' => 'de',
         'watch_region'           => 'BR',
         'with_watch_providers'   => implode('|', $this->providers_id),
      ]), [
         'headers'        => $this->headers,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($request)) {
         return false;
      }

      if (200 !== \wp_remote_retrieve_response_code($request)) {
         return false;
      }

      return json_decode(\wp_remote_retrieve_body($request), true);
   }
}
