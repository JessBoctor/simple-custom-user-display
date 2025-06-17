<?php
/**
* Create a new user role with custom meta and taxonomy
*/
class Scud_User {

    /**
     * A string with no spaces to act as a slug or internal reference to the role tupe
     */
    private string $role_slug = 'simple_user';

    /**
     * A string which is displayed to admin screens
     */
    private string $role_display_name = 'Simple User';

    /**
     * The capabilities that the user should have e.g. the ability to create posts or make theme changes
     * WordPress Capabilities are listed here: https://wordpress.org/documentation/article/roles-and-capabilities/#capabilities
     */
    private array $capabilities = [
        'read'         => true,
        'edit_posts'   => true,
        'delete_posts' => true,
    ];

    /**
     * A string describing the group of meta fields assigned to this role
     */
    private string $meta_fields_heading = 'Additional Information';

    /**
     * An array containing the slug and label for meta-data fields which should be saved to these types of user
     * The slug and label should be saved as key => value pairs:
     * [
     *    'meta_slug' => 'meta_label',
     * ]
     */
    private array $meta_fields = [
        'slug_1' => 'Slug 1',
        'slug_2' => 'Slug 2',
    ];

    /**
     * Register the custom user role
     * Fires only on the activation hook, so the role is only registered once
     *
     * To read through the documentation on adding a custom user role, see https://developer.wordpress.org/reference/functions/add_role/
     * @param none
     * @return void
     */
    public function add_user_role(): void {
        add_role( $this->role_slug, $this->role_display_name, $this->capabilities );
    }

    /**
     * Run the action hooks when a user profile is loaded
     *
     * @param none;
     * @return void;
     */
    public function user_profile_hooks(): void {

        // Add custom meta fields to the "edit user" admin screen
        add_action( 'show_user_profile', array( $this, 'display_user_profile_fields' ) );
        add_action( 'edit_user_profile', array( $this, 'display_user_profile_fields' ) );

        // Save custom meta field values on profile update
        add_action( 'personal_options_update', array( $this, 'save_user_profile_fields' ) );
        add_action( 'edit_user_profile_update', array( $this, 'save_user_profile_fields' ) );

    }

    /**
     * Retrieve and display the user title field
     *
     * @param WP_User $user The user to show in the admin editor
     * @return void The HTML code to disply the user field
     */
    public function display_user_profile_fields( WP_User $user ): void {

        // Avoid displaying the custom meta fields for other user roles
        if ( ! in_array( $this->role_slug, $user->roles ) ) {
            return;
        }

        // For some reason, the action hooks expect the HTML code directly and won't render a string of HTML code
        // It would be cleaner to return the HTML string, but this type of templating works too
        ?>
        <h3><?php echo $this->meta_fields_heading; ?></h3>

        <table class="form-table">
            <?php foreach ( $this->meta_fields as $field_slug => $field_label ):
                 // Before we display the profile fields, retrieve the fields from the WP_User object
                 $saved_meta = $user->get( 'scud_user_' . $field_slug );
                ?>
                    <tr>
                        <th><label for="scud_user_<?php echo $field_slug; ?>"><?php echo $field_label; ?></label></th>
                        <td>
                            <input type="text" name="scud_user_<?php echo $field_slug; ?>" id="scud_user_<?php echo $field_slug; ?>" value="<?php echo esc_attr( $saved_meta ); ?>" />
                        </td>
                    </tr>
                <?php endforeach; ?>
        </table>
    <?php
    }

    /**
     * Save the user profile fields
     *
     * @param string $user_id The ID of the user being saved
     * @return void;
     */
    public function save_user_profile_fields( string $user_id ): void {
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
            return;
        }

        if ( !current_user_can( 'edit_user', $user_id ) ) {
            return;
        }

        $fields_not_in_post = [];
        foreach ( $this->meta_fields as $field_slug => $field_label ) {
            if ( array_key_exists( 'scud_user_' . $field_slug, $_POST ) ) {
                update_user_meta( $user_id, 'scud_user_' . $field_slug, $_POST['scud_user_' . $field_slug ] );
            } else {
                $fields_not_in_post[] = $field_slug;
            }
        }
    
    }

}   
