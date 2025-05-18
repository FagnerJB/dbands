<?php

get_component('header');

?>
<main id="main">
   <div id="content">
      <?php

      if (have_posts()) {
         get_page_component('page', 'content');
      }

      ?>
   </div>
</main>
<?php

get_component('footer');
