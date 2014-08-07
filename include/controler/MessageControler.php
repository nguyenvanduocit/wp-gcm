<?php
/**
 * Project : wp-gcm
 * User: thuytien
 * Date: 8/7/2014
 * Time: 9:51 AM
 */
class MessageControler extends BaseControler{

    public static function send($message)
    {
        //$apps = p2p_type( 'app_to_message' )->get_related( $message );
        $apps = get_posts(array(
            'connected_type' => 'app_to_message',
            'connected_items' => $message
        ));
        foreach($apps as $app)
        {
            $result = AppControler::push($app, $message);
            if(is_wp_error($result))
            {
                return $result->get_error_message();
            }
        };
    }
}