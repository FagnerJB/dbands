<?php

namespace dbp\Category;

class Register
{
   public function __construct()
   {
      add_action('category_add_form_fields', [$this, 'add_fields']);
      add_action('category_edit_form_fields', [$this, 'edit_fields']);
      add_action('created_category', [$this, 'save_fields']);
      add_action('edit_category', [$this, 'save_fields']);
   }

   public function add_fields()
   {
      echo <<<FIELD
      <div class="form-field">
         <label for="singular">Nome singular</label>
         <input id="singular" name="singular" type="text" value size="40">
      </div>
      FIELD;
   }

   public function edit_fields($term)
   {
      $value = get_term_meta($term->term_id, 'singular_name', true);
?>
      <tr class="form-field">
         <th scope="row"><label for="singular">Nome singular</label></th>
         <td>
            <input id="singular" name="singular" value="<?php echo $value ?>" type="text">
         </td>
      </tr>
<?php

   }

   public function save_fields($term_ID)
   {
      if (!empty($_POST['singular'])) {
         $value = sanitize_text_field($_POST['singular']);
         update_term_meta($term_ID, 'singular_name', $value);
      } else {
         delete_term_meta($term_ID, 'singular_name');
      }
   }
}
