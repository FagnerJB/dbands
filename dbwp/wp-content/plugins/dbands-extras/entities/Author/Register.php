<?php

namespace dbp\Author;

class Register
{
   public function __construct()
   {
      add_action('show_user_profile', [$this, 'edit_fields']);
      add_action('edit_user_profile', [$this, 'edit_fields']);
      add_action('personal_options_update',     [$this, 'save_fields']);
      add_action('edit_user_profile_update',    [$this, 'save_fields']);

      add_filter('user_contactmethods', [$this, 'remove_fields']);
   }



   function edit_fields($user)
   {

?>
      <h2>
         <?php esc_html_e('Redes sociais', 'dbands') ?>
         (<?php esc_html_e('opcional', 'dbands') ?>)
      </h2>
      <table class="form-table">
         <tbody>
            <?php

            foreach (Utils::get_metas() as $key => $details) {
               $value = get_user_meta($user->ID, $key, true);

            ?>
               <tr>
                  <th><label for="<?php echo $key ?>"><?php echo $details['name'] ?></label></th>
                  <td><input id="<?php echo $key ?>" name="<?php echo $key ?>" type="text" value="<?php echo $value ?>" size="40" placeholder="<?php echo $details['placeholder'] ?>"> <span class="description"><?php echo $details['description'] ?>.</span></td>
               </tr>
            <?php

            }

            ?>
         </tbody>
      </table>
<?php

   }


   function save_fields($user_id)
   {
      if (!current_user_can('edit_user', $user_id))
         return false;

      foreach (Utils::get_metas() as $key => $details) {
         if (!empty($_POST[$key]))
            update_user_meta($user_id, $key, sanitize_text_field($_POST[$key]));
         else
            delete_user_meta($user_id, $key);
      }
   }

   function remove_fields($methods)
   {
      unset($methods['yim']);
      unset($methods['aim']);
      unset($methods['jabber']);

      return $methods;
   }
}
