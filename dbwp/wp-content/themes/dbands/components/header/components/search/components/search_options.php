<?php

use dbp\Common\Utils;

$type    = get_query_var('search_type', 'site');
$options = Utils::get_search_options();

?>
<div class="flex grow gap-2 items-center min-w-60 wjs" x-on:click.outside="search.select = false">
   <div class="h-full grow relative flex items-center">
      <button class="btn-alt flex gap-2 justify-between items-center w-full py-1 px-2 " type="button"
         x-on:click="search.select = !search.select">
         <div class="flex gap-2 items-center">
            <strong
               x-text="search.selected"><?php echo $options[$type]['selected']; ?></strong>
         </div>
         <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="absolute top-full right-0 z-10 left-0 flex flex-col rounded-b bg-zinc-900 overflow-hidden"
         x-show="search.select" x-transition x-cloak>
         <?php

         foreach ($options as $key => $option) {
            ?>
         <label class="btn-item cursor-pointer py-1 px-2" tabindex="0"
            x-on:keyup.enter="search.type='<?php echo $key; ?>';document.getElementById('search-input').focus()">
            <input class="hidden" name="search_type" type="radio"
               value="<?php echo $key; ?>"
               x-model.fill="search.type"
               <?php checked($type, $key); ?> required>
            <input name="<?php echo $key; ?>-placeholder"
               type="hidden"
               value="<?php echo $option['placeholder']; ?>"
               disabled>
            <input name="<?php echo $key; ?>-selected"
               type="hidden"
               value="<?php echo $option['selected']; ?>"
               disabled>
            <div class="flex items-center gap-2">
               <i class="far"
                  x-bind:class="{'fa-circle': search.type !== '<?php echo $key; ?>', 'fa-dot-circle': search.type === '<?php echo $key; ?>'}"></i>
               <strong><?php echo $option['title']; ?></strong>
               <?php

                     if (!empty($option['service'])) {
                        ?>
               <i class="<?php echo $option['service']; ?>"></i>
               <?php

                     }

            ?>
            </div>
            <small><?php echo $option['subtitle']; ?></small>
         </label>
         <?php

         }

?>
      </div>
   </div>
</div>
<select name="search_type" class="grow btn-alt nojs">
<?php

foreach ($options as $key => $option) {
   $selected = selected($key, $type);

   echo "<optgroup label=\"{$option['subtitle']}\">";
   echo "<option value=\"{$key}\" {$selected}>{$option['title']}</option>";
   echo '</optgroup>';
}

?>
</select>
