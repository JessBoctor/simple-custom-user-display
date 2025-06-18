<?php
namespace SCUD;

use Scud_User;
use WP_User;

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

        // Deep thanks to Justin Tadlock and his user-tags plugin for this next bit
        // https://wordpress.org/plugins/user-tags/
        // https://justintadlock.com/archives/2011/10/20/custom-user-taxonomies-in-wordpress

        /**
         * Update user taxonomies in the profile page
         */
        public function user_profile_hooks() {
            // Show on User Profiles.
            add_action( 'show_user_profile', array( $this, 'display_user_taxonomy_fields' ) );
            add_action( 'edit_user_profile', array( $this, 'display_user_taxonomy_fields' ) );
            add_action( 'user_new_form', array( $this, 'display_user_taxonomy_fields' ) );

            // Update on Save
            add_action( 'personal_options_update', array( $this, 'save_user_taxonomy_terms' ) );
            add_action( 'edit_user_profile_update', array( $this, 'save_user_taxonomy_terms' ) );
            add_action( 'user_register', array( $this, 'save_user_taxonomy_terms' ) );

            // Clear up related tags and taxonomies, when a user is deleted.
            add_action( 'deleted_user', array( $this, 'remove_user_from_terms_list' ) );
        }

        /**
         * Display the taxonomy fields on user profiles
         *
         * @param WP_User $user The user to show in the admin editor
         * @return void The HTML code to disply the user field gets passed to the profile
         */
        public function display_user_taxonomy_fields( WP_User $user ): void {
            // Return early if the current user does not have the abiility to edit other users
            if ( ! current_user_can( 'edit_users' ) ) {
                return;
            }

            // Avoid displaying the custom taxonomy fields for other user roles
            $scud_user = new Scud_User();
            if ( ! in_array( $scud_user->role_slug, $user->roles ) ) {
                return;
            }
			?>
            <h3><?php esc_html_e( 'User Groups' ); ?></h3>
            <div class="user-taxonomy-wrapper">
				<?php
				wp_nonce_field( 'user-groups', 'user-groups' );

				$user_taxonomies = get_object_taxonomies( 'user', 'objects' );
                if ( ! empty( $user_taxonomies ) ) :
				    foreach ( $user_taxonomies as $taxonomy ) {
                    ?>
                        <table class="form-table user-profile-taxonomy">
                            <tr>
                                <th>
                                    <label for="new-tag-user_tag_<?php echo esc_attr( $taxonomy->name ); ?>">
										<?php echo esc_html( $taxonomy->labels->singular_name ); ?>
                                    </label>
                                </th>
                                <td>
									<?php
									wp_terms_checklist(
										$user->ID,
										array(
											'taxonomy' => $taxonomy->name,
											'walker'   => 'Walker_Category_Checklist',
										)
									);
									?>
                                </td>
                            </tr>
                        </table>
					<?php
				    } // Taxonomies
                endif; // Taxonomies exist
				?>
            </div>
			<?php
        }

        /**
         * Update terms saved to a user on profile update
         *
         * @param string $user_id The ID of the user being saved
         * @return void;
         */
        public function save_user_taxonomy_terms( string $user_id ): void {
            // Check this save is being called from the correct admin screen and nonce
            check_admin_referer( 'update-user_' . $user_id );

			// Check if the current user can edit this user.
			if ( empty( $_POST['tax_input'] ) || ! current_user_can( 'edit_user', $user_id ) ) {
				return;
			}

			//phpcs:ignore
			$input_tags = wp_unslash( $_POST['tax_input'] );
            // The structure of the taxonomy data here is taxonomy_slug => array of terms
			foreach ( $input_tags as $taxonomy => $taxonomy_terms ) {

				$taxonomy       = sanitize_key( $taxonomy );
				$taxonomy_terms = array_map( 'absint', $taxonomy_terms );

				// Save the data.
				if ( ! empty( $taxonomy_terms ) ) :
					wp_set_object_terms( $user_id, $taxonomy_terms, $taxonomy, false );
				else :
					// No terms left, delete all terms.
					wp_set_object_terms( $user_id, array(), $taxonomy, false );
				endif;
			}
        }

        /**
         * Clear up related tags and taxonomies when a user is deleted
         *
         * @param string $user_id The id of the user being deleted
         * @return void
         */
        public function remove_user_from_terms_list( string $user_id ): void {
            $taxonomies    = get_object_taxonomies( 'user', 'object' );
			$taxonomy_list = array();
			foreach ( $taxonomies as $key => $taxonomy ) {
				$taxonomy_list[] = $key;
			}
			// Delete the relation for a user.
			if ( ! empty( $taxonomy_list ) && is_array( $taxonomy_list ) ) {
				wp_delete_object_term_relationships( $user_id, $taxonomy_list );
			}
        }
    }
}
