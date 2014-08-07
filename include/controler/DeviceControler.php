<?php
/**
 * Project : wp-gcm
 * User: thuytien
 * Date: 8/6/2014
 * Time: 9:16 PM
 */
class DeviceControler extends BaseControler{


    /**
     * @param $regID
     * @return bool|deviceid
     */
    public static function isExist($regID)
    {
        global $wpdb;
        $query = "SELECT ID
                    FROM $wpdb->posts, $wpdb->postmeta
                    WHERE
                          ID = post_id
                      AND post_type = 'device'
                      AND post_status = 'publish'
                      AND meta_key = 'device_RegistrationId'
                      AND meta_value = '{$regID}'";
        $result = $wpdb->get_row($query, OBJECT);
        if ($result != null)
        {
            return $result->ID;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param $regID
     * @return int|WP_Error
     */
    public static function add($regID)
    {
        $post = array(
                    'post_status'    => 'publish',
                    'post_type'      => 'device',
                    'post_author'    => '1'
                );

        $post_id = wp_insert_post( $post );

        if( is_wp_error($post_id) )
        {
            return $post_id;
        }
        else
        {
            if ( add_post_meta($post_id, 'device_RegistrationId', $regID) )
            {

                return $post_id;
            }
            else
            {
                wp_delete_post($post_id);
                return new WP_Error( 'broke', "Khong the add device nay" );;
            }
        }
    }
}