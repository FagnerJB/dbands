<?php

namespace dbp\Author;

class Register
{
   public function __construct()
   {
      add_action('show_user_profile', [$this, 'edit_fields']);
      add_action('edit_user_profile', [$this, 'edit_fields']);
      add_action('personal_options_update', [$this, 'save_fields']);
      add_action('edit_user_profile_update', [$this, 'save_fields']);
   }

   public function edit_fields($user): void
   {
      ?>
<h2>
   <?php esc_html_e('Campos extras', 'dbands'); ?>
   (<?php esc_html_e('opcional', 'dbands'); ?>)
</h2>
<table class="form-table">
   <tbody>
      <?php

      $key     = 'user_fav_bands';
      $details = [
         'name'        => 'Bandas recomendadas',
         'placeholder' => 'Rammstein, Scorpions, Alphaville',
         'description' => 'Nome das bandas favoritas ou recomendadas do site. Separe com <strong>, (vírgula) </strong> para vários',
      ];

      $value = get_user_meta($user->ID, $key, true);

      ?>
      <tr>
         <th>
            <label for="<?php echo $key; ?>">
               <?php echo $details['name']; ?>
            </label>
         </th>
         <td>
            <input id="<?php echo $key; ?>"
                   name="<?php echo $key; ?>" type="text"
                   value="<?php echo $value; ?>" size="40"
                   placeholder="<?php echo $details['placeholder']; ?>" />
            <span class="description">
               <?php echo $details['description']; ?>.
            </span>
         </td>
      </tr>
   </tbody>
</table>
<?php

   }
   public function save_fields($user_id)
   {
      if (!current_user_can('edit_user', $user_id)) {
         return false;
      }

      $key = 'user_fav_bands';

      if (!empty($_POST[$key])) {
         update_user_meta($user_id, $key, sanitize_text_field($_POST[$key]));
      } else {
         delete_user_meta($user_id, $key);
      }
   }
}
?>
