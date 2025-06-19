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
            // Store the user data together (e.g. WP_User object, metadata, and taxonomies)
            $scud_userdata = [];

            // Fetch all SCUD User IDs
            $query_args = [
                'role' => Scud_User::$role_slug,
                'fields' => 'ID',
            ];

            $user_query = new WP_User_Query( $query_args );
            $user_objects = $user_query->get_results();

            $meta_keys_for_display = array_merge( self::$meta_fields_for_display, array_keys( Scud_User::$meta_fields ) );
            write_log( $meta_keys_for_display );

            foreach ( $user_objects as $user_id ) {

                write_log( get_user_meta( $user_id ) );
                // Fetch the meta data
                $scud_userdata[ $user_id ] = array_intersect_key(
                    get_user_meta( $user_id ),
                    array_flip( $meta_keys_for_display )
                );
            }

            write_log( $scud_userdata );

            return $scud_userdata;
        }
    }
}