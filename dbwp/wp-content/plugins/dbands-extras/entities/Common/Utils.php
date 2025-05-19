<?php

namespace dbp\Common;

class Utils
{
   public static function clean_domain($url)
   {
      $domain = preg_replace('|https?://|', '', $url);

      return untrailingslashit($domain);
   }

   public static function get_page()
   {
      return max(get_query_var('paged'), 1);
   }

   public static function remove_tags($text)
   {
      $text = preg_replace('@<(a)[^>]*?>.*?</\1>\.?@si', '', $text);

      return wp_strip_all_tags($text);
   }

   public static function is_response_error($thing)
   {
      return is_array($thing) && isset($thing['code'], $thing['message'], $thing['data']) && $thing['data']['status'] >= 400;
   }

   public static function get_social_links($name, $username = '')
   {
      return [
         'share' => [
            'icon'  => 'fa-solid fa-share',
            'label' => "Compartilhar no {$name}",
         ],
         'repost' => [
            'icon'  => 'fa-solid fa-retweet',
            'label' => 'Republicar',
         ],
         'like' => [
            'icon'  => 'fa-solid fa-heart',
            'label' => 'Curtir',
         ],
         'reply' => [
            'icon'  => 'fa-solid fa-comment',
            'label' => 'Responder',
         ],
         'link' => [
            'icon'  => 'fa-solid fa-link',
            'label' => "Esta publicação no {$name}",
         ],
         'profile' => [
            'icon'  => 'fa-solid fa-user-plus',
            'label' => "Seguir @{$username}",
         ],
      ];
   }

   public static function get_search_link($search_query, $search_type = 'site', $paged = 0)
   {
      $url = [home_url('busca')];

      if ('site' !== $search_type) {
         $url[] = $search_type;
      }

      if (!empty($search_query)) {
         $url[] = \apply_filters('get_search_query', $search_query);
      }

      if ($paged > 1 && 'site' === $search_type) {
         $url[] = 'page';
         $url[] = $paged;
      }

      return esc_url(implode('/', $url));
   }

   public static function get_search_options($only_keys = false)
   {
      $options = [
         'site' => [
            'selected'    => esc_html__('Em todo site', 'dbands'),
            'placeholder' => esc_html__('Pesquise no conteúdo deste site', 'dbands'),
            'title'       => esc_html__('Notícias', 'dbands'),
            'subtitle'    => esc_html__('Publicações deste site', 'dbands'),
            'search_page' => esc_html__('Publicações e letras traduzidas neste site', 'dbands'),
         ],
         'videos' => [
            'selected'    => esc_html__('Por vídeos', 'dbands'),
            'placeholder' => esc_html__('Pesquise por vídeos musicais no YouTube', 'dbands'),
            'title'       => esc_html__('Clipes', 'dbands'),
            'subtitle'    => esc_html__('Vídeos de música', 'dbands'),
            'search_page' => esc_html__('Vídeos de música do YouTube', 'dbands'),
            'service'     => 'fab fa-youtube',
         ],
         'artista' => [
            'selected'    => esc_html__('Por um artista', 'dbands'),
            'placeholder' => esc_html__('Pesquise por qualquer artista no Last.fm, e.g. Lady Gaga', 'dbands'),
            'title'       => esc_html__('Um artista', 'dbands'),
            'subtitle'    => esc_html__('Encontre artistas similares', 'dbands'),
            'search_page' => esc_html__('Artistas similares do Last.fm', 'dbands'),
            'service'     => 'fab fa-lastfm',
         ],
         'tag' => [
            'selected'    => esc_html__('Por um termo', 'dbands'),
            'placeholder' => esc_html__('Pesquise por nacionalidade ou gênero em inglês no Last.fm, e.g. russian metal', 'dbands'),
            'title'       => esc_html__('Termo em inglês', 'dbands'),
            'subtitle'    => esc_html__('Relacionados a um gênero ou país', 'dbands'),
            'search_page' => esc_html__('Artistas relacionados a tag do Last.fm', 'dbands'),
            'service'     => 'fab fa-lastfm',
         ],
         'usuario' => [
            'selected'    => esc_html__('Por um usuário', 'dbands'),
            'placeholder' => esc_html__('Pesquise por um usuário no Last.fm, e.g. FagnerJB', 'dbands'),
            'title'       => esc_html__('Usuário no Last.fm', 'dbands'),
            'subtitle'    => esc_html__('Artistas mais ouvidos do semestre', 'dbands'),
            'search_page' => esc_html__('Artistas mais ouvidos do último semestre de usuário no Last.fm', 'dbands'),
            'service'     => 'fab fa-lastfm',
         ],
      ];

      if (false === $only_keys) {
         return $options;
      }

      $keys = array_keys($options);

      if (true === $only_keys) {
         return $keys;
      }

      return null;
      // spotify = search => ID => related-artists
   }
}
