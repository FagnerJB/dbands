<?php

$type = $args['type'];
$ID   = $args['ID']   ?? null;
$text = $args['text'] ?? null;

$function = "edit_{$type}_link";

if (function_exists($function) && is_user_logged_in()) {
   ?>
   <div class="text-center">
      <?php

         $function($text, '[ ', ' ]', $ID);

   ?>
   </div>
<?php
}
