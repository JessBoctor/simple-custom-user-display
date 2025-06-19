<?php
/**
 * Query for users which have the scud user role
 */
namespace SCUD;

use WP_User_Query;
use SCUD\Scud_User;

if ( ! class_exists( 'Scud_User_Query' ) ) {
    class Scud_User_Query {

        /**
         * An array of user meta fields to return
         */
        static array $meta_fields_for_display = [
            'nickname',
            'first_name',
            'last_name',
            'description'
        ];

        /**
         * Pull SCUD users from the database
         *
         * @param none
         * @return array Contains WP_User objects
         */
        public static function get_users(): array {
            // Save the userdata into a single array to pass to the dataviews
            $scud_userdata = [];

            // Fetch all SCUD User IDs
            $query_args = [
                'role' => Scud_User::$role_slug,
                'fields' => 'ID',
            ];

            $user_query = new WP_User_Query( $query_args );
            $scud_user_ids = $user_query->get_results();

            // We want to fetch all the meta in one call to get_user_meta
            // But we don't need all of the meta data returned
            // Create a filter for the returned data
            $meta_keys_for_display = array_merge( self::$meta_fields_for_display, array_keys( Scud_User::$meta_fields ) );

            // Go through each user ID and fetch the data we need
            foreach ( $scud_user_ids as $user_id ) {

                // Fetch the metadata and ilter to what we can use
                $user_meta = array_intersect_key(
                    get_user_meta( $user_id ),
                    array_flip( $meta_keys_for_display )
                );

                // Fetch the user taxonomy terms
                $user_taxonomies = [];

                $scud_userdata[ $user_id ] = array_merge( $user_meta, $user_taxonomies );
            }

            write_log( $scud_userdata );

            return $scud_userdata;
        }
    }
}