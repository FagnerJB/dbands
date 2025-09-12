<?php

namespace dbp\Services;

if (!defined('YT_CHANNEL_ID')) {
   return;
}

class Youtube
{
   private $baseurl    = 'https://youtube.googleapis.com/youtube/v3';
   private $first_page = 10;
   private $id         = YT_CHANNEL_ID ?? '';
   private $per_page   = 16;
   private $search_key = YT_SEARCH_APIKEY ?? '';
   private $tv_key     = YT_TV_APIKEY     ?? '';

   public function __construct($id = null)
   {
      if (!empty($id)) {
         $this->id = $id;
      }
   }

   public function get_feed($page = 1, $video_ID = '')
   {
      $videos_next = get_transient('dbtv-videos');

      if (empty($videos_next)) {
         $channels = $this->get_subscriptions($this->id);

         if (empty($channels)) {
            return [];
         }

         $videos = [];

         foreach ($channels as $channel) {
            $channel_ID = $channel['snippet']['resourceId']['channelId'];

            $channel_uploads = $this->get_feed_videos($channel_ID);

            if (empty($channel_uploads)) {
               continue;
            }

            $videos = array_merge($videos, $channel_uploads);
         }

         uasort($videos, fn($a, $b) => strcmp($b['date'], $a['date']));

         $videos_next = [];

         foreach ($videos as $key => $value) {
            $videos_next[$key] = [
               ...$value,
               'next' => next($videos)['id'] ?? false,
            ];
         }

         set_transient('dbtv-videos', $videos_next, is_environment('production') ? DAY_IN_SECONDS : 0);
      }

      if (!empty($video_ID)) {
         $found = [];

         foreach ($videos_next as $key => $video) {
            if ($key === $video_ID) {
               $found = $video;

               break;
            }
         }

         return $found;
      }

      $offset = 1 === $page ? 0 : $this->first_page + (($page - 2) * $this->per_page);
      $length = 1 === $page ? $this->first_page : $this->per_page;

      return array_slice($videos_next, $offset, $length);
   }

   public function get_playlist_videos($playlist_ID = null, $page = '')
   {
      if (is_null($playlist_ID)) {
         $playlist_ID = $this->id;
      }

      $playlist_videos_fetched = \wp_remote_get("{$this->baseurl}/playlistItems/?" . http_build_query([
         'part'       => 'snippet',
         'maxResults' => 50,
         'key'        => $this->tv_key,
         'playlistId' => $playlist_ID,
         'pageToken'  => $page,
      ]), [
         'cache_key'      => 'yt-playlistItems-' . $playlist_ID . '-' . $page,
         'cache_duration' => '1 day',
         'cache_type'     => 'disk',
      ]);

      if (false === $playlist_videos_fetched) {
         return [];
      }

      $playlist_videos_content = json_decode(\wp_remote_retrieve_body($playlist_videos_fetched), true);

      if (empty($playlist_videos_content['items'])) {
         return [];
      }

      $videos_playlist_return = [];

      if (isset($playlist_videos_content['nextPageToken'])) {
         $videos_playlist_return['pages']['next'] = $playlist_videos_content['nextPageToken'];
      }

      if (isset($playlist_videos_content['prevPageToken'])) {
         $videos_playlist_return['pages']['prev'] = $playlist_videos_content['prevPageToken'];
      }

      foreach ($playlist_videos_content['items'] as $video) {
         $video_ID = (string) $video['snippet']['resourceId']['videoId'];

         $videos_playlist_return['videos'][$video_ID] = [
            'id'     => $video_ID,
            'date'   => date_i18n('Y-m-d H:i:s', strtotime((string) $video['snippet']['publishedAt'])),
            'title'  => (string) $video['snippet']['title'],
            'author' => (string) $video['snippet']['channelTitle'],
         ];
      }

      return $videos_playlist_return;
   }

   public function get_subscriptions($channel, $pageToken = '')
   {
      $subscriptions_fetched = \wp_remote_get("{$this->baseurl}/subscriptions/?" . http_build_query([
         'part'       => 'snippet',
         'maxResults' => 50,
         'key'        => $this->tv_key,
         'channelId'  => $channel,
         'pageToken'  => $pageToken,
      ]), [
         'cache_key'      => 'yt-subs-' . $channel . '-' . $pageToken,
         'cache_duration' => '1 month',
         'cache_type'     => 'disk',
      ]);

      if (false === $subscriptions_fetched) {
         return false;
      }

      $subscriptions_content = json_decode(\wp_remote_retrieve_body($subscriptions_fetched), true);

      if (empty($subscriptions_content['items'])) {
         return false;
      }

      return $subscriptions_content['items'];
   }

   public function search(array $args)
   {
      $defaults = [
         'key'             => $this->search_key,
         'maxResults'      => 50,
         'order'           => 'relevance',
         'part'            => 'snippet',
         'safeSearch'      => 'strict',
         'type'            => 'video',
         'videoCategoryId' => 10,
         'videoDefinition' => 'high',
         'videoDimension'  => '2d',
         'videoEmbeddable' => 'true',
      ];

      $key = sanitize_file_name(http_build_query($args));

      $results_fetched = \wp_remote_get("{$this->baseurl}/search/?" . http_build_query(wp_parse_args($args, $defaults)), [
         'cache_key'      => 'yt-search-' . $key,
         'cache_duration' => '3 days',
         'cache_type'     => 'disk',
      ]);

      if (is_wp_error($results_fetched)) {
         return [];
      }

      $results_content = json_decode(\wp_remote_retrieve_body($results_fetched), true);

      if (empty($results_content['items'])) {
         return [];
      }

      $videos_return = [];

      foreach ($results_content['items'] as $video) {
         if (empty($video['id']['videoId'])) {
            continue;
         }

         $video_ID = $video['id']['videoId'];

         $videos_return[$video_ID] = [
            'id'     => $video_ID,
            'date'   => date_i18n('Y-m-d H:i:s', strtotime((string) $video['snippet']['publishedAt'])),
            'title'  => (string) $video['snippet']['title'],
            'author' => (string) $video['snippet']['channelTitle'],
         ];
      }

      return $videos_return;
   }

   private function get_feed_videos($channel_ID)
   {
      $playlist_ID = 'UULF' . ltrim($channel_ID, 'UC');
      $only_videos = $this->get_playlist_videos($playlist_ID);

      if (empty($only_videos['videos'])) {
         return [];
      }

      $only_upcoming = $this->search([
         'order'     => 'date',
         'channelId' => $channel_ID,
         'eventType' => 'upcoming',
      ]);

      $only_broadcasts = array_merge(
         array_keys($only_upcoming),
      );

      $videos_filtered = [];

      foreach ($only_videos['videos'] as $video_ID => $video) {
         if (in_array($video_ID, $only_broadcasts)) {
            continue;
         }

         $videos_filtered[$video_ID] = $video;
      }

      return $videos_filtered;
   }
}
