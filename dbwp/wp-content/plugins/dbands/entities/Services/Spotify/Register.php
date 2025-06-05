<?php

namespace dbp\Services\Spotify;

class Register
{
   public function __construct()
   {
      add_shortcode('spotify', [$this, 'sc_spotify']);
   }

   public function sc_spotify()
   {
      if (isset($_GET['state']) && !wp_verify_nonce($_GET['state'], 'spotify_state')) {
         return 'Requisição não confirmada';
      }

      if (isset($_GET['error'])) {
         return 'Erro: ' . $_GET['error'];
      }

      $output = '';

      if (isset($_GET['code'])) {
         $spotify = new Spotify();
         $spotify->save_token($_GET['code']);
         $output .= 'Entrou.';
      } elseif (isset($_COOKIE['dbands:spotify:user'])) {
         $output .= 'Entrou cookie.';
      } else {
         $spotify = new Spotify();
         $link    = $spotify->get_auth_link();
         $output .= <<<LINK
                  <a class="btn btn-secondary spotify-auth-btn" href="{$link}"><i class="fab fa-spotify"></i> Entrar com Spotify</a>
         LINK;
      }

      return $output;
   }
}
