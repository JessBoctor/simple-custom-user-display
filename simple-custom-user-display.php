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

use SCUD\Scud_User;
use SCUD\Scud_Groups;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'SCUD_PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once SCUD_PLUGIN_DIR . 'class.scud-user-role.php';
require_once SCUD_PLUGIN_DIR . 'class.scud-user-group.php';

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
    $scud_user = new Scud_User();
    $scud_user->add_user_role();
}

// Initialize custom user profile fields and meta
add_action( 'current_screen', 'scud_load_user' );

/**
 * Conditionally load user meta fields based on the screen
 *
 * @param none;
 * @return void;
 */
function scud_load_user(): void {

    $scud_user = new Scud_User();
    $screen = get_current_screen();

    if ( ! $screen ) {
        return;
    }

    switch ( $screen->id ) {
        case 'users':
        case 'user':
        case 'profile':
        case 'user-edit':
            $scud_user->user_profile_hooks();
            break;
        default:
            break;
        }
}

// Register & Load the new user group taxonomies
add_action( 'init', 'scud_load_user_groups' );

// Load the taxonomies in the admin menu
function scud_load_user_groups() {
    $scud_groups = new Scud_Groups();

    // Register taxonomies
    $scud_groups->register_taxonomies();

    // Load admin screens
    $scud_groups->add_user_groups_to_menu();
}
