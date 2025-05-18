<?php

$News       = $args['post'] ?? false;
$with_links = $args['with_links'] ?? false;

if ($News) {
   $categories = $News->get('categories');
} elseif (isset($args['categories'])) {
   $categories = $args['categories'];
}

if (!is_single() && $News) {
   $bands = $News->get('tags');
} elseif (isset($args['bands'])) {
   $bands = $args['bands'];
}

$tags = array_merge($bands ?? [], $categories ?? []);

if (is_tag() || is_category()) {
   $current = get_queried_object();

   $found = array_find_key($tags, fn($term) => $term->term_id === $current->term_id);
   if (!is_null($found)) {
      unset($tags[$found]);
   }
}

if (!empty($tags)) {

?>
   <ul class="flex flex-wrap font-semibold <?php echo $with_links ? 'gap-2' : 'gap-1 lg:gap-2' ?>">
      <?php

      foreach ($tags as $tag) {
         if ($tag->taxonomy === 'category') {
            $li_classes = 'bg-red-800 text-zinc-200';
            if ($with_links) {
               $li_classes .= ' hover:bg-red-600 focus:bg-red-600';
            }
         } else {
            $li_classes = 'bg-amber-400 text-zinc-800';
            if ($with_links) {
               $li_classes .= ' hover:bg-yellow-300 focus:bg-yellow-300';
            }
         }

         $li_classes .= $with_links ? ' text-base' : ' text-xxs md:text-base';

         $name = $tag->name;
         if ($tag->taxonomy === 'category') {
            $singular = get_term_meta($tag->term_id, 'singular_name', true);

            if (!empty($singular) && !$with_links) {
               $name = $singular;
            }
         }

      ?>
         <li class="<?php echo $li_classes ?>" href="<?php echo get_term_link($tag) ?>">
            <?php

            $item_classes = 'block truncate max-w-50';

            if ($with_links) {
               $rel = is_singular() ? 'rel="tag"' : '';

               echo '<a class="' . $item_classes . ' py-1.5 px-3" href="' . get_term_link($tag) . '" ' . $rel . '>';
            } else {
               echo '<div class="' . $item_classes . ' py-0.75 px-1.5">';
            }

            $prefix = $tag->taxonomy === 'category' ? '<i class="fas fa-hashtag"></i>' : '';
            echo $prefix . $name;

            if ($with_links) {
               echo '</a>';
            } else {
               echo '</div>';
            }

            ?>
         </li>
      <?php

      }

      ?>
   </ul>
<?php

}
