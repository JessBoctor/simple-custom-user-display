<?php
/**
* Create a new user role with custom meta and taxonomy
*/
class Scud_User_Role {

    // A string with no spaces to act as a slug or internal reference to the role tupe
    private string $role_slug = 'simple_user';

    // A string which is displayed to admin screens
    private string $role_display_name = 'Simple User';

    /**
     * Register the custom user role
     * Fires only on the activation hook, so the role is only registered once
     *
     * To read through the documentation on adding a custom user role, see https://developer.wordpress.org/reference/functions/add_role/
     * @param none
     * @return void
     */
    function scud_add_user_role(): void {
        // A string with no spaces to act as a slug or internal reference to the role tupe
        $role_slug = 'simple_user';

        // A string which is displayed to admin screens
        $role_display_name = 'Simple User';

        // WordPress Capabilities are listed here: https://wordpress.org/documentation/article/roles-and-capabilities/#capabilities
        $capabilities = [
            'read'         => true,
            'edit_posts'   => true,
            'delete_posts' => true,
        ];

        add_role( $this->role_slug, $this->role_display_name, $capabilities );
    }

    // Add custom meta fields to the "edit user" admin screen
    add_action( 'show_user_profile', 'scud_display_user_profile_fields' );
    add_action( 'edit_user_profile', 'scud_display_user_profile_fields' );

    /**
     * Retrieve and display the user title field
     *
     * @param WP_User $user The user to show in the admin editor
     * @return void The HTML code to disply the user field
     */
    function scud_display_user_profile_fields( WP_User $user ): void {
        // Before we display the profile fields, retrieve the fields from the WP_User object
        $saved_title = $user->get( 'scud_user_title' );

        // For some reason, the action hooks expect the HTML code directly and won't render a string of HTML code
        // It would be cleaner to return the HTML string, but this type of templating works too
        ?>
        <h3><?php _e('Additional information'); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="scud_user_title"><?php _e( 'Title' ); ?></label></th>
                <td>
                    <input type="text" name="scud_user_title" id="scud_user_title" value="<?php echo esc_attr( $saved_title ); ?>" />
                </td>
            </tr>
        </table>
    <?php
    }

    // Save custom meta field values on profile update
    add_action( 'personal_options_update', 'scud_save_user_profile_fields' );
    add_action( 'edit_user_profile_update', 'scud_save_user_profile_fields' );

    /**
     * Save the user profile fields
     *
     * @param string $user_id The ID of the user being saved
     * @return void;
     */
    function scud_save_user_profile_fields( $user_id ) {
    if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
        return;
    }

    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return;
    }

    update_user_meta( $user_id, 'title', $_POST['title'] );
    }
}
