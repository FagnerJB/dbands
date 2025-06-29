<?php

namespace dbp\Band;

use Automattic\Jetpack\Image_CDN\Image_CDN_Core;
use cavWP\Models\Term;
use cavWP\Utils as CavUtils;
use dbp\Author\Author;
use dbp\Services\Spotify\Spotify;

class Band extends Term
{
   public $meta;

   public function __construct($term = null)
   {
      if ('cover' === $term) {
         if (is_single() && $bands = get_the_tags(get_queried_object())) {
            shuffle($bands);

            foreach ($bands as $band) {
               parent::__construct($band, 'post_tag');

               if ($this->get_file('cover')) {
                  break;
               }
            }
         } elseif (is_author()) {
            $User      = new Author();
            $favorites = $User->get_favorites();

            if (!empty($favorites)) {
               shuffle($favorites);

               foreach ($favorites as $band) {
                  parent::__construct($band, 'post_tag');

                  if ($this->get_file('cover')) {
                     break;
                  }
               }
            }
         } elseif (is_tag()) {
            parent::__construct(null, 'post_tag');
         }

         if (empty($this->get_file('cover'))) {
            parent::__construct([
               'field' => 'slug',
               'value' => Utils::select_random($term),
            ], 'post_tag');
         }
      } else {
         parent::__construct($term, 'post_tag');
      }

      if (empty($this->data)) {
         return;
      }

      foreach (array_keys(Utils::get_metas()) as $key) {
         $array_metas[str_replace('band_', '', $key)] = $this->get_meta($key);
      }

      $this->meta = (object) $array_metas;

      if (empty($this->meta->spotify)) {
         $spotify = new Spotify();
         $band_ID = $spotify->search($this->data->name, 'artist');

         if (!empty($band_ID)) {
            add_term_meta($this->data->term_id, 'band_spotify', $band_ID);
            $this->meta->spotify = $band_ID;
         }
      }
   }

   public function get_cover()
   {
      return $this->get_file('cover');
   }

   public function get_genre()
   {
      return Utils::capitalize_genre($this->meta->genre);
   }

   public function get_logo()
   {
      $logo = $this->get_file('logo');

      if (empty($logo)) {
         return false;
      }

      return sprintf(
         '<img class="self-start" src="%s" width="%s" height="%s" alt="%s" loading="lazy" />',
         $logo['url'],
         $logo['width'],
         $logo['height'],
         $this->data->name,
      );
   }

   public function get_meta_link($meta, $withText = true, $start = '', $end = '', $attrs = [])
   {
      switch ($meta) {
         case 'tag':
            $link = $this->get('link');
            $icon = 'fas fa-plus-circle';
            $text = 'Todas as informações';

            break;

         case 'site':
            if (empty($this->meta->site)) {
               break;
            }
            $link  = $this->meta->site;
            $icon  = 'fas fa-globe-europe';
            $text  = CavUtils::clean_domain($this->meta->site);
            $title = sprintf(esc_html__('Site oficial de %s', 'dbands'), $this->data->name);

            break;

         case 'active_years':
         case 'years':
            if (empty($this->meta->active_years)) {
               break;
            }
            $icon = 'fas fa-calendar';
            $text = Utils::get_prefix_years($this->meta->active_years);

            break;

         case 'photo_credits':
            if (empty($this->meta->photo_credits)) {
               break;
            }
            $icon = 'fa-regular fa-copyright';
            $text = $this->meta->photo_credits;

            break;

         case 'city':
            if (empty($this->meta->city)) {
               break;
            }
            $icon = 'fas fa-map-marked-alt';
            $text = sprintf(esc_html__('Origem: %s', 'dbands'), $this->meta->city);

            break;

         case 'spotify':
            if (empty($this->meta->spotify) || 'noartist' === $this->meta->spotify) {
               break;
            }
            $link = 'https://open.spotify.com/artist/' . $this->meta->spotify;
            $icon = 'fab fa-spotify';
            $text = esc_html__('Ouça no Spotify', 'dbands');

            break;

         case 'spotify-code':
            if (empty($this->meta->spotify) || 'noartist' === $this->meta->spotify) {
               break;
            }
            $link  = 'https://open.spotify.com/artist/' . $this->meta->spotify;
            $text  = '<img class="band-spotify-code img-fluid mb-2 mx-auto" src="https://scannables.scdn.co/uri/plain/jpeg/FFCF00/black/400/spotify:artist:' . $this->meta->spotify . '" loading="lazy" alt="">';
            $title = esc_html__('Ouça no Spotify', 'dbands');

            break;

         case 'itunes':
            if (empty($this->meta->itunes)) {
               break;
            }
            $link = 'https://music.apple.com/br/artist/' . $this->meta->itunes . '?mt=1&app=music&at=10lSby';
            $icon = 'fa-brands fa-itunes-note';
            $text = esc_html__('Ouça no Apple Music', 'dbands');

            break;

         case 'deezer':
            if (empty($this->meta->deezer)) {
               break;
            }
            $link = $this->meta->deezer;
            $icon = 'fa-brands fa-deezer';
            $text = esc_html__('Ouça no Deezer', 'dbands');

            break;

         case 'youtube':
            if (empty($this->meta->youtube)) {
               break;
            }

            $icon              = 'fab fa-youtube';
            list($link, $text) = Utils::get_youtube_info($this->meta->youtube);

            break;

         case 'facebook':
            if (empty($this->meta->facebook)) {
               break;
            }
            $link = 'https://fb.com/' . $this->meta->facebook;
            $icon = 'fab fa-facebook';
            $text = esc_html__('Página no Facebook', 'dbands');

            break;

         case 'instagram':
            if (empty($this->meta->instagram)) {
               break;
            }
            $link  = 'https://www.instagram.com/' . $this->meta->instagram;
            $icon  = 'fab fa-instagram';
            $text  = '@' . $this->meta->instagram;
            $title = esc_html__('Perfil no Instagram', 'dbands');

            break;

         case 'twitter':
            if (empty($this->meta->twitter)) {
               break;
            }
            $link  = 'https://x.com/' . $this->meta->twitter;
            $icon  = 'fab fa-x-twitter';
            $text  = '@' . $this->meta->twitter;
            $title = esc_html__('Perfil no Twitter', 'dbands');

            break;

         case 'lastfm':
            if (empty($this->meta->lastfm)) {
               break;
            }
            $link = 'https://www.last.fm/music/' . $this->meta->lastfm;
            $icon = 'fab fa-lastfm-square';
            $text = esc_html__('Página no Last.fm', 'dbands');

            break;

         case 'musixmatch':
            if (empty($this->meta->musixmatch)) {
               break;
            }
            $icon = 'fab fa-pied-piper-pp';
            $link = 'https://www.musixmatch.com/' . esc_html__('pt-br', 'dbands') . '/artist/' . $this->meta->musixmatch;
            $text = esc_html__('Letras de músicas no MusixMatch', 'dbands');

            break;

         case 'posts':
            $link  = get_tag_link($this->data->term_id);
            $icon  = 'fas fa-th-large';
            $text  = sprintf(_n('Ver a publicação', 'Ver as %s publicações', $this->data->count, 'dbands'), $this->data->count);
            $title = sprintf(esc_html__('Publicações sobre %s', 'dbands'), $this->data->name);

            break;

         case 'lyrics':
            $lyrics_count = count(get_posts([
               'fields'         => 'ids',
               'post_type'      => 'lyric',
               'tag_id'         => $this->data->term_id,
               'posts_per_page' => -1,
            ]));

            if (empty($lyrics_count)) {
               break;
            }
            $link  = home_url('traducoes') . '#' . esc_attr(sanitize_title($this->data->name));
            $icon  = 'fa-solid fa-align-left';
            $text  = sprintf(_n('Ver a tradução', 'Ver as %s traduções', $lyrics_count, 'dbands'), $lyrics_count);
            $title = sprintf(esc_html__('Letras traduzidas de %s', 'dbands'), $this->data->name);

            break;

         default:
            return '';
            break;
      }

      if (empty($text)) {
         return '';
      }

      $html = '';

      if (!empty($link)) {
         $html .= '<a href="' . esc_url($link) . '"';
      }

      if (!empty($link) && !empty($title)) {
         $html .= ' title="' . $title . '"';
      }

      if (!empty($link) && !in_array($meta, ['lyrics', 'posts', 'tag'])) {
         $html .= ' target="_blank"';
      }

      if (!empty($link) && !empty($attrs)) {
         foreach ($attrs as $name => $value) {
            $html .= " {$name}=\"{$value}\"";
         }
      }

      if (!empty($link)) {
         $html .= ' rel="external">';
      }

      if (!empty($icon)) {
         $html .= '<i class="' . $icon . ' text-center fa-fw"></i> ';
      }

      if ($withText && !empty($text)) {
         $html .= $text;
      }

      if (!empty($link)) {
         $html .= '</a>';
      }

      return $start . $html . $end;
   }

   public function has_file($type)
   {
      return (bool) $this->get_file($type);
   }

   private function get_file($type)
   {
      if (!isset($this->data->slug)) {
         return false;
      }

      $extension     = 'cover' === $type ? '.jpg' : '.png';
      $filename      = '/' . $type . 's/' . $this->data->slug . $extension;
      $location_file = wp_upload_dir()['basedir'] . $filename;

      if (!file_exists($location_file)) {
         return false;
      }

      $url = WP_CONTENT_URL . '/uploads' . $filename;

      if (class_exists('\Automattic\Jetpack\Image_CDN\Image_CDN_Core')) {
         $url = Image_CDN_Core::cdn_url($url);
      }

      if ('cover' === $type) {
         return $url;
      }

      list($width, $height) = getimagesize($location_file);

      return [
         'url'    => $url,
         'width'  => $width,
         'height' => $height,
      ];
   }
}
