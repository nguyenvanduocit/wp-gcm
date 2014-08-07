<?php
require_once "APIResponse.php";
/**
 *
 */
class GCMAPI
{
    /**
     * @var null
     */
    public static $instance = null;

    /**
     * @return null
     */
    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new GCMAPI();
        }
        return self::$instance;
    }

    /**
     *
     */
    function register()
    {
        add_action('wp_ajax_device_register', array(__CLASS__, 'device_register'));
    }

    /**
     *
     */
    function device_register()
    {
        $response = new APIResponse();
        if ( ( isset($_REQUEST['regid']) && $_REQUEST['regid'] != "") && ( isset($_REQUEST['appid']) && $_REQUEST['appid'] != "") ) {

            $registrationId = $_REQUEST['regid'];
            $appid = $_REQUEST['appid'];

            if(AppControler::isExist($appid))
            {
                $deviceid = DeviceControler::isExist($registrationId);
                if( $deviceid )
                {
                    $p2p_id = p2p_type( 'app_to_device' )->get_p2p_id( $appid, $deviceid );
                    if( $p2p_id )
                    {
                        $response->setMessage("This device is ready registed");
                    }
                    else{
                        $p2p_id = p2p_type( 'app_to_device' )->connect( $appid, $deviceid);
                        $response->setMessage( "success" );
                    }
                }
                else{
                    $deviceid = DeviceControler::add($registrationId);
                    if(is_wp_error($deviceid))
                    {
                        $response->setMessage( $deviceid->get_error_message() );
                    }
                    else{

                        $p2p_id = p2p_type( 'app_to_device' )->connect( $appid, $deviceid);
                        $response->setMessage( "success" );
                    }
                }
            }
            else
            {
                $response->setMessage("Application is not exist");
            }
        }
        else
        {
            $response->setMessage("Missing argument");
        }
        echo $response->toJson();
        die();
    }

}