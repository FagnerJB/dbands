<?php

namespace dbp\Author;

class Utils
{
   public static function get_metas()
   {
      return [
         'user_fav_bands' => [
            'name'        => 'Bandas recomendadas',
            'placeholder' => 'Rammstein, Scorpions, Alphaville',
            'description' => 'Nome das bandas favoritas ou recomendadas do site. Separe com <strong>, (vírgula) </strong> para vários'
         ],
         'user_spotify' => [
            'name'        => 'Spotify',
            'placeholder' => 'dbands',
            'description' => 'ID/URI do perfil no Spotify',
            'link'        => 'https://open.spotify.com/user/%s',
            'text'        => 'Spotify',
            'title'       => 'Perfil no Spotify',
            'icon'        => 'fab fa-spotify'
         ],
         'user_lastfm' => [
            'name'        => 'Last.fm',
            'placeholder' => 'dbands',
            'description' => 'Usuário no Last.fm',
            'link'        => 'https://www.last.fm/user/%s',
            'text'        => 'Last.fm',
            'title'       => 'Perfil no Last.fm',
            'icon'        => 'fab fa-lastfm'
         ],
         'user_instagram' => [
            'name'        => 'Instagram',
            'placeholder' => 'dbands_com_br',
            'description' => 'Usuário no Instagram, sem @',
            'link'        => 'https://www.instagram.com/%s',
            'text'        => '@%s',
            'title'       => 'Perfil no Instagram',
            'icon'        => 'fab fa-instagram'
         ],
         'user_youtube' => [
            'name'        => 'Playlist de recomendações no YouTube',
            'placeholder' => 'PLTg2AhCnKU-L4-2j5yEEEyNWDsdnzqVB1',
            'description' => 'ID/URI de playlist pública do YouTube',
            'link'        => 'https://www.youtube.com/playlist?list=%s',
            'text'        => 'Playlist no YouTube',
            'title'       => 'Playlist no YouTube',
            'icon'        => 'fab fa-youtube'
         ],
         'user_twitter' => [
            'name'        => 'X',
            'placeholder' => 'dbands_com_br',
            'description' => 'Usuário no X, sem @',
            'link'        => 'https://x.com/%s',
            'text'        => '@%s',
            'title'       => 'Perfil no Twitter',
            'icon'        => 'fab fa-x-twitter'
         ],
         'user_facebook' => [
            'name'        => 'Facebook',
            'placeholder' => 'dbands',
            'description' => 'Usuário no Facebook',
            'link'        => 'https://fb.com/%s',
            'text'        => 'Facebook',
            'title'       => 'Perfil no Facebook',
            'icon'        => 'fab fa-facebook'
         ],
      ];
   }
}
