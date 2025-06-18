<?php
namespace SCUD;

/**
 * Create new user taxonomies
 */
if ( ! class_exists( 'Scud_Groups' ) ) {
    class Scud_Groups {
        /**
         * There's no clean way to store taxonomy information without just registering it.
         * Let's avoid complexity and just hard code the taxonomy features during registration,
         *   rather than trying to pull the information from somewhere else.
         *
         * All available taxonomy parameters can be found here: https://developer.wordpress.org/reference/functions/register_taxonomy/
         */
        public function register_taxonomies() {

            // Group 1
            register_taxonomy(
                'group_1',
                'user',
                array(
                    'public' => true,
                    'show_ui' => true,
                    'labels' => array(
                        'name' => 'First User Groups',
                        'singular_name' => 'User Group 1'
                    ),
                    'capabilities' => array(
                        'manage_terms' => 'edit_users',
                        'edit_terms'  => 'edit_users',
                        'delete_terms' => 'edit_users',
                        'assign_terms' => 'edit_users',
                    ),
                )
            );

            // Group 2
            register_taxonomy(
                'group_2',
                'user',
                array(
                    'public' => true,
                    'show_ui' => true,
                    'labels' => array(
                        'name' => 'Second User Groups',
                        'singular_name' => 'User Group 2'
                    ),
                    'capabilities' => array(
                        'manage_terms' => 'edit_users',
                        'edit_terms'  => 'edit_users',
                        'delete_terms' => 'edit_users',
                        'assign_terms' => 'edit_users',
                    ),
                )
            );
        }

        /**
         * Add a menu screen for each taxonomy
         * This allows you to manage terms
         * You need to create on new menu (e.g. call add_submenu_page) for each taxonomy which is registered
         *
         * @param none
         * @return void
         */
        public function add_user_groups_to_menu(): void {
            add_submenu_page( 'users.php', 'User Group One', 'First User Groups', 'edit_users', 'edit-tags.php?taxonomy=group_1' );
            add_submenu_page( 'users.php', 'User Group Two', 'Second User Groups', 'edit_users', 'edit-tags.php?taxonomy=group_2' );
        }
    }
}
