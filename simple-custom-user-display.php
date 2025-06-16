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

define( 'SCUD_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once SCUD_PLUGIN_DIR . 'class.scud-user-role.php';

// Register the new User Role
register_activation_hook( __FILE__, 'scud_activation' );

/**
 * Execute set up tasks on plugin activation
 *
 * @param none;
 * @return void;
 */
function scud_activation(): void {

    // Add the new user role
    $user_role = new Scud_User_Role();
    $user_role->scud_add_user_role();
}
