<?php

namespace dbp\Services\Spotify;

class Spotify
{
   private $client           = SPOTIFY_CLIENT;
   private $secret           = SPOTIFY_SECRET;
   private $base_url         = 'https://api.spotify.com/v1';
   private $auth_url         = 'https://accounts.spotify.com';
   private $redirect         = '';
   private $token            = '';
   private $cookie_prefix    = '';
   private $transient_prefix = 'dbands_spotify_token_user_';

   public function __construct()
   {
      $this->redirect      = get_home_url(null, '/spotify', 'https');
      $this->cookie_prefix = md5('[x)4#dR{Oa)t1');
   }

   public function get_auth_link()
   {
      return "{$this->auth_url}/authorize?" . http_build_query([
         'client_id'     => $this->client,
         'response_type' => 'code',
         'redirect_uri'  => $this->redirect,
         'state'         => wp_create_nonce('spotify_state'),
         'scope'         => 'user-read-recently-played,user-read-currently-playing',
      ]);
   }

   public function save_token($code)
   {
      $basic_header   = base64_encode("{$this->client}:{$this->secret}");
      $token_response = wp_remote_post("{$this->auth_url}/api/token", [
         'headers' => [
            'Authorization' => "Basic {$basic_header}",
         ],
         'body' => [
            'grant_type'   => 'authorization_code',
            'code'         => $code,
            'redirect_uri' => $this->redirect,
         ],
      ]);

      if (is_wp_error($token_response) || 200 !== wp_remote_retrieve_response_code($token_response)) {
         return 'Não foi possível gerar o Token';
      }

      $token_body  = json_decode(wp_remote_retrieve_body($token_response), true, 512, JSON_OBJECT_AS_ARRAY);
      $this->token = $token_body['access_token'];

      $me_response = wp_remote_get("{$this->base_url}/me", [
         'headers' => [
            'Authorization' => "Bearer {$this->token}",
         ],
      ]);

      if (is_wp_error($me_response) || 200 !== wp_remote_retrieve_response_code($me_response)) {
         return 'Não foi possível identificar o usuário';
      }

      $me_body = json_decode(wp_remote_retrieve_body($me_response), true, 512, JSON_OBJECT_AS_ARRAY);
      $me_id   = $this->cookie_prefix . md5($me_body['id']);

      $user_obj = [
         'token'   => $this->token,
         'refresh' => $token_body['refresh_token'],
         'name'    => $me_body['display_name'],
         'avatar'  => $me_body['images'][0]['url'],
      ];

      set_transient("{$this->transient_prefix}{$me_id}", $user_obj, $token_body['expires_in']);

      setcookie('dbands:spotify:user', $me_id, $token_body['expires_in']);
   }

   public function search($term, $type)
   {
      $this->get_user($this->cookie_prefix . md5('fagnerjb'));

      if ($this->token) {
         $search_response = wp_remote_get("{$this->base_url}/search?" . http_build_query([
            'q'     => $term,
            'type'  => $type,
            'limit' => 1,
         ]), [
            'headers' => [
               'Authorization' => "Bearer {$this->token}",
            ],
         ]);

         if (is_wp_error($search_response) || 200 !== wp_remote_retrieve_response_code($search_response)) {
            return 'Não foi encontrar música';
         }

         $search_body = json_decode(wp_remote_retrieve_body($search_response), true, 512, JSON_OBJECT_AS_ARRAY);

         if (!empty($search_body["{$type}s"]['items'])) {
            return $search_body["{$type}s"]['items'][0]['id'];
         }
      }
   }

   public function fetch_tracks($id)
   {
      $tracks_fetched = wp_remote_get("{$this->base_url}/artists/{$id}/top-tracks?country=BR&track_number=9", [
         'cache_duration' => '2 weeks',
      ]);

      $tracks_fetched = wp_remote_retrieve_body($tracks_fetched);

      $tracks_return = [];

      if (is_array($tracks_fetched) && count($tracks_fetched['tracks']) > 0) {
         foreach ($tracks_fetched['tracks'] as $track) {
            $tracks_return[] = [
               'name' => (string) $track['name'],
               'id'   => (string) $track['id'],
            ];
         }
      }

      return $tracks_return;
   }

   private function get_user($user_hash): void
   {
      if (str_contains($user_hash, $this->cookie_prefix)) {
         $user_obj = get_transient("{$this->transient_prefix}{$user_hash}");

         if (false === $user_obj) {
            return;
         }

         $this->token = $user_obj['token'];
      }
   }
}
