<?php

get_component('header');

?>
<main id="main">
   <div id="content">
      <h1 class="sr-only"><?php bloginfo('name'); ?></h1>
      <?php

      get_page_component('home', 'content');

?>
   </div>
</main>
<?php

get_component('footer');
