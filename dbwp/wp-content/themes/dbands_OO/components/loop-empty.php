<?php

use dbp\Common\Utils;

?>
<p class="font-medium">
   <?php esc_html_e('Não há publicações aqui. Tente fazer nova uma busca.', 'dbands'); ?>
</p>
<div class="my-2">
   <?php

   $search_options = Utils::get_search_options();

foreach ($search_options as $key => $option) {
   ?>
      <button class="btn-alt" type="button"
         x-on:click="search.type='<?php echo $key; ?>';document.getElementById('search-input').focus()">
         <?php

         echo $option['selected'];

   if (!empty($option['service'])) {
      ?>
            <i class="<?php echo $option['service']; ?>"></i>
         <?php

   }

   ?>
      </button>
   <?php

}

?>
</div>
