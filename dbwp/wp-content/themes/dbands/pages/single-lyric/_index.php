<?php

get_component('header');

?>
<div id="main">
   <div id="content">
      <?php

      if (have_posts()) {
         get_page_component('single-lyric', 'content');
      }

?>
   </div>
</div>
<?php

get_component('footer');

?>
