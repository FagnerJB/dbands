<?php

use cavWP\Utils as CavUtils;
use dbp\Common\Utils;
use dbp\News\News;

$social_posts = [];
$News         = new News();
$social_IDs   = $News->get_social_posts();
$title_raw    = $News->get('title', apply_filter: false);
$link_raw     = $News->get('link', apply_filter: false);
$excerpt      = str_replace(["\r\n", "\n"], ', ', $News->get('excerpt', apply_filter: false));
$title        = urlencode($title_raw);
$link         = urlencode($link_raw);
$tags         = $News->get('tags');

$tag = '';

if (!empty($tags)) {
   $tag = CavUtils::clean_hashtag($tags[0]->name);
}

$social_posts['default'] = [
   'mini'   => true,
   'name'   => 'Compartilhar',
   'icon'   => 'fa-solid fa-share-nodes',
   'bg'     => 'bg-zinc-700',
   'action' => "x-on:click=\"navigator.share({title: '{$title_raw}',text: '{$excerpt}',url: '{$link_raw}'})\"",
];

$social_posts['tw'] = [
   'hidden'   => true,
   'name'     => 'X',
   'icon'     => 'fa-brands fa-x-twitter',
   'bg'       => 'bg-black',
   'username' => 'dbands_com_br',
   'profile'  => 'https://twitter.com/intent/follow?screen_name=dbands_com_br',
   'share'    => "https://x.com/intent/tweet?text={$title}&url={$link}&via=dbands_com_br&hashtags={$tag}",
];

if (!empty($social_IDs['twitter'])) {
   $social_posts['tw']['link']   = get_post_meta(11651, '_menu_item_url', true) . '/status/' . $social_IDs['twitter'];
   $social_posts['tw']['repost'] = "https://x.com/intent/retweet?tweet_id={$social_IDs['twitter']}";
   $social_posts['tw']['like']   = "https://x.com/intent/like?tweet_id={$social_IDs['twitter']}";
   $social_posts['tw']['reply']  = "https://x.com/intent/tweet?in_reply_to={$social_IDs['twitter']}";
}

$social_posts['fb'] = [
   'hidden'   => true,
   'name'     => 'Facebook',
   'icon'     => 'fa-brands fa-facebook',
   'bg'       => 'bg-[#0865fe]',
   'username' => 'dbands',
   'profile'  => get_post_meta(11648, '_menu_item_url', true),
   'share'    => "https://www.facebook.com/sharer/sharer.php?u={$link}&display=popup",
];

if (!empty($social_IDs['facebook'])) {
   $social_posts['fb']['link']   = $social_posts['fb']['profile'] . '/posts/' . $social_IDs['facebook'];
   $repost_link                  = urlencode($social_posts['fb']['link']);
   $social_posts['fb']['repost'] = "https://www.facebook.com/sharer/sharer.php?u={$repost_link}&display=popup";
}

$social_posts['wa'] = [
   'hidden' => true,
   'mini'   => true,
   'name'   => 'WhatsApp',
   'icon'   => 'fa-brands fa-whatsapp',
   'bg'     => 'bg-[#25d366]',
   'share'  => "https://wa.me/?text={$title}%20-%20{$link}",
];

$social_posts['td'] = [
   'hidden' => true,
   'mini'   => true,
   'name'   => 'Threads',
   'icon'   => 'fa-brands fa-threads',
   'bg'     => 'bg-black',
   'share'  => "https://www.threads.com/intent/post?text={$title}&url={$link}",
   // "profile" => https://www.threads.net/intent/follow?username=dbands_com_br
];

$social_posts['rd'] = [
   'hidden' => true,
   'mini'   => true,
   'name'   => 'Reddit',
   'icon'   => 'fa-brands fa-reddit',
   'bg'     => 'bg-[#ff4500]',
   'share'  => "https://www.reddit.com/submit?url={$link}l&title={$title}&type=LINK",
];

$social_posts[] = [
   'hidden'   => true,
   'mini'     => true,
   'name'     => 'Instagram',
   'icon'     => 'fa-brands fa-instagram',
   'bg'       => 'bg-[#ff0069]',
   'username' => 'dbands_com_br',
   'profile'  => get_post_meta(11650, '_menu_item_url', true),
];

$social_posts['copy'] = [
   'mini'   => true,
   'name'   => 'Copiar link',
   'icon'   => 'fa-regular fa-copy',
   'bg'     => 'bg-zinc-700',
   'action' => 'x-on:click.throttle.2000ms="shareCopy(\'' . $News->get('link') . '\')"',
];

$social_posts['show'] = [
   'mini'   => true,
   'name'   => 'Mostrar todos',
   'icon'   => 'fa-solid fa-square-plus',
   'bg'     => 'bg-zinc-700',
   'action' => 'x-on:click="showShare"',
];

?>
<section id="share" class="share-list flex flex-col gap-2">
   <h2 class="text-xl font-medium">
      <?php esc_html_e('Nas redes sociais', 'dbands'); ?>
   </h2>
   <ul class="flex flex-col">
      <?php

      foreach ($social_posts as $key => $social) {
         $social_links = Utils::get_social_links($social['name'], $social['username'] ?? '');

         $container_class = "share-{$key}";
         $container_class .= isset($social['mini']) ? ' items-center' : ' items-start';
         $container_class .= isset($social['hidden']) ? ' share-hidden' : ' wjs';

         ?>
      <li
          class="<?php echo $social['bg']; ?> transition-all text-white flex my-2 py-1.5 px-2 font-medium text-lg <?php echo $container_class; ?>">
         <?php

         if (isset($social['mini'])) {
            foreach ($social_links as $key => $social_link) {
               if (!isset($social[$key])) {
                  continue;
               }

               echo '<a class="py-1.5 px-2" href="' . esc_url($social[$key]) . '" target="_blank" rel="external" aria-hidden="true" tabindex="-1">';
            }

            if (isset($social['action'])) {
               echo '<button class="py-1.5 px-2 text-center" ' . $social['action'] . ' aria-hidden="true" tabindex="-1">';
            }
         } else {
            echo '<div class="py-1.5 px-2 text-center">';
         }

         echo "<i class=\"{$social['icon']} text-xl\"></i>";

         if (isset($social['mini'])) {
            if (isset($social['action'])) {
               echo '</button>';
            } else {
               echo '</a>';
            }
         } else {
            echo '</div>';
         }

         ?>
         <div class="grow flex flex-col text-base">
            <?php

            foreach ($social_links as $key => $social_link) {
               if (!isset($social[$key])) {
                  continue;
               }

               ?>
            <a class="py-1.5 px-2"
               href="<?php echo esc_url($social[$key]); ?>"
               target="_blank" rel="external">
               <i
                  class="<?php echo $social_link['icon']; ?> fa-fw"></i>
               <?php echo $social_link['label']; ?>
            </a>
            <?php

            }

         if (isset($social['action'])) {
            ?>
            <button class="py-1.5 px-2 text-left"
                    <?php echo $social['action']; ?>>
               <?php echo $social['name']; ?>
            </button>
            <?php

         }

         ?>
         </div>
      </li>
      <li>
         <?php

      }

?>
      <li>
         <div class="py-1.5 px-2 bg-zinc-700 truncate text-zinc-200 w-full select-all nojs">
            <?php echo $title_raw; ?> -
            <?php echo $link_raw; ?>
         </div>
      </li>
   </ul>
</section>
