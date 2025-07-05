<?php

use dbp\News\News;

$News = new News();

?>
<div id="aside" class="sticky top-3 flex flex-col gap-5">
<?php

get_component('aside-edit-this', [
   'type' => 'post',
   'text' => esc_html__('Editar esta publicação', 'dbands'),
]);

get_component('aside-bands');

get_component('aside-share');

get_component('aside-ad');

?>
</div>
