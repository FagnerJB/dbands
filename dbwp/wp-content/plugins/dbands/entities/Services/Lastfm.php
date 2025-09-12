<?php

namespace dbp\Services;

if (!defined('LASTFM_APIKEY')) {
   return;
}

use dbp\Common\Utils;

class Lastfm
{
   private $apikey  = LASTFM_APIKEY ?? '';
   private $args    = [];
   private $baseurl = 'https://ws.audioscrobbler.com/2.0/';
   private $lang;

   public function __construct()
   {
      $this->lang = esc_html__('pt', 'dbands');
      $this->args = [
         'format'  => 'json',
         'api_key' => $this->apikey,
      ];
   }

   public function get_artist($artist)
   {
      $artist_fetched = \wp_remote_get($this->baseurl . '?' . http_build_query([
         ...$this->args,
         'method'      => 'artist.getinfo',
         'artist'      => $artist,
         'lang'        => $this->lang,
         'autocorrect' => 1,
      ]), [
         'cache_key'      => 'lastfm-artist-' . $this->lang . '-' . $artist,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($artist_fetched)) {
         return false;
      }

      $artist_content = json_decode(\wp_remote_retrieve_body($artist_fetched), true);

      if (empty($artist_content['artist'])) {
         return false;
      }

      $artist_return['name']  = $artist_content['artist']['name'];
      $artist_return['url']   = $artist_content['artist']['url'];
      $artist_return['wiki']  = Utils::remove_tags($artist_content['artist']['bio']['summary']);
      $artist_return['count'] = (int) $artist_content['artist']['stats']['listeners'];
      $artist_return['tags']  = [];

      if (!empty($artist_content['artist']['tags'])) {
         foreach ($artist_content['artist']['tags']['tag'] as $tag) {
            $artist_return['tags'][] = [
               'name' => $tag['name'],
               'url'  => $tag['url'],
            ];
         }
      }

      $artist_return['items'] = $this->get_artist_similar($artist);

      return $artist_return;
   }

   public function get_tag($tag)
   {
      $tag_fetched = \wp_remote_get($this->baseurl . '?' . http_build_query([
         ...$this->args,
         'method' => 'tag.getinfo',
         'tag'    => $tag,
         'lang'   => $this->lang,
      ]), [
         'cache_key'      => 'lastfm-tag-' . $this->lang . '-' . $tag,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($tag_fetched)) {
         return false;
      }

      $tag_content = \json_decode(\wp_remote_retrieve_body($tag_fetched), true);

      if (empty($tag_content['tag'])) {
         return false;
      }

      $tag_return['name']  = $tag_content['tag']['name'];
      $tag_return['wiki']  = Utils::remove_tags($tag_content['tag']['wiki']['summary']);
      $tag_return['count'] = (int) $tag_content['tag']['reach'];
      $tag_return['url']   = 'https://www.last.fm/tag/' . urlencode($tag);
      $tag_return['tags']  = $this->get_tag_similares($tag);
      $tag_return['items'] = $this->get_tag_artists($tag);

      return $tag_return;
   }

   public function get_user($username)
   {
      $user_fetched = \wp_remote_get($this->baseurl . '?' . http_build_query([
         ...$this->args,
         'method' => 'user.getinfo',
         'user'   => $username,
      ]), [
         'cache_key'      => 'lastfm-user-' . $username,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($user_fetched)) {
         return false;
      }

      $user_content = json_decode(\wp_remote_retrieve_body($user_fetched), true);

      if (empty($user_content['user'])) {
         return false;
      }

      $user_return['name']  = $user_content['user']['realname'];
      $user_return['count'] = $user_content['user']['artist_count'];
      $user_return['url']   = $user_content['user']['url'];
      $user_return['wiki']  = $user_content['user']['country'];

      $user_return['items'] = $this->get_user_artists($username);

      return $user_return;
   }

   private function get_artist_similar($artist)
   {
      $similares_fetched = \wp_remote_get($this->baseurl . '?' . http_build_query([
         ...$this->args,
         'method'      => 'artist.getsimilar',
         'artist'      => $artist,
         'autocorrect' => 1,
         'limit'       => 48,
      ]), [
         'cache_key'      => 'lastfm-artistSimilar-' . $artist,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($similares_fetched)) {
         return false;
      }

      $similares_content = json_decode(\wp_remote_retrieve_body($similares_fetched), true);

      if (empty($similares_content['similarartists']['artist'])) {
         return false;
      }

      foreach ($similares_content['similarartists']['artist'] as $artist) {
         $similares_return[] = [
            'name'  => (string) $artist['name'],
            'url'   => (string) $artist['url'],
            'match' => number_format(((float) $artist['match']) * 100, 1),
         ];
      }

      return $similares_return;
   }

   private function get_tag_artists($tag)
   {
      $tag_fetched = \wp_remote_get($this->baseurl . '?' . http_build_query([
         ...$this->args,
         'method' => 'tag.gettopartists',
         'tag'    => $tag,
         'limit'  => 48,
      ]), [
         'cache_key'      => 'lastfm-tagArtists-' . $tag,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($tag_fetched)) {
         return false;
      }

      $tag_content = \json_decode(\wp_remote_retrieve_body($tag_fetched), true);

      if (empty($tag_content['topartists']['artist'])) {
         return false;
      }

      foreach ($tag_content['topartists']['artist'] as $artist) {
         $tag_return[] = [
            'name' => (string) $artist['name'],
            'url'  => (string) $artist['url'],
         ];
      }

      return $tag_return;
   }

   private function get_tag_similares($tag)
   {
      $tag_fetched = \wp_remote_get($this->baseurl . '?' . http_build_query([
         ...$this->args,
         'method' => 'tag.getsimilar',
         'tag'    => $tag,
      ]), [
         'cache_key'      => 'lastfm-tagSimilar-' . $tag,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($tag_fetched)) {
         return false;
      }

      $tag_content = json_decode(\wp_remote_retrieve_body($tag_fetched), true);

      if (empty($tag_content['similartags']['tag'])) {
         return false;
      }

      foreach ($tag_content['similartags']['tag'] as $tag) {
         $similar_return[] = [
            'name' => (string) $tag['name'],
            'url'  => (string) $tag['url'],
         ];
      }

      return $similar_return;
   }

   private function get_user_artists($username, $period = '6month')
   {
      // overall | 7day | 1month | 3month | 6month | 12month

      $artists_fetched = \wp_remote_get($this->baseurl . '?' . http_build_query([
         ...$this->args,
         'method' => 'user.gettopartists',
         'user'   => $username,
         'period' => $period,
         'limit'  => 48,
      ]), [
         'cache_key'      => 'lastfm-userArtists-' . $period . '-' . $username,
         'cache_duration' => '1 week',
         'cache_type'     => 'disk',
      ]);

      if (\is_wp_error($artists_fetched)) {
         return false;
      }

      $artists_content = json_decode(\wp_remote_retrieve_body($artists_fetched), true);

      if (empty($artists_content['topartists']['artist'])) {
         return false;
      }

      foreach ($artists_content['topartists']['artist'] as $artist) {
         $artists_return[] = [
            'name' => $artist['name'],
            'url'  => $artist['url'],
            'rank' => $artist['playcount'],
         ];
      }

      return $artists_return;
   }
}
