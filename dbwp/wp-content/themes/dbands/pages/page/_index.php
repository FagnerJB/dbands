<?php

get_component('header');

?>
<div id="main">
   <div id="content">
      <?php

      if (have_posts()) {
         get_page_component('page', 'content');
      }

?>
   </div>
</div>
<?php

get_component('footer');
