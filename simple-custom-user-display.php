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
 */
function scud_add_user_role() {
    $role_slug = 'simple_user';

    $role_display_name = 'Simple User';

    $capabilities = [
        'read'         => true,
        'edit_posts'   => true,
        'delete_posts' => true,
    ];

    add_role( $role_slug, $role_display_name, $capabilities );
}
