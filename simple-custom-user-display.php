<?php
/*
Plugin Name: Simple Custom User Display
Plugin URI:  https://github.com/JessBoctor/simple-custom-user-display
Description: Create a custom user and display in a DataView block
Version:     0.1
Author:      Jess Boctor
Author URI:  http://jessboctor.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'SCUD_PLUGIN_PATH', plugin_dir_path( __FILE__ ));

// Register the new User Role
register_activation_hook( __FILE__, 'scud_add_user_role' );

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

    add_role( $role_slug, $role_display_name, $capabilities );
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
