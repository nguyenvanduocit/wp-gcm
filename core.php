<?php
require_once 'include/posttype/Message.posttype.php';
require_once 'include/posttype/Device.posttype.php';
require_once 'include/posttype/App.posttype.php';
require_once 'include/CustomDashboard.php';
require_once 'include/API.class.php';

require_once 'include/controler/BaseControler.php';
require_once 'include/controler/DeviceControler.php';
require_once 'include/controler/AppControler.php';
require_once 'include/controler/MessageControler.php';

class GCM_Core {

	static function init() {

        MessagePostType::instance()->register();
        AppPostType::instance()->register();
        DevicePostType::instance()->register();
        GCMAPI::instance()->register();

		add_action( 'p2p_init', array( __CLASS__, 'connection_posttype' ));
        add_action('publish_message', array( __CLASS__, 'onPostStatusChange'), 10, 2);

        add_action('admin_notices', array( __CLASS__,'wpSub_admin_notices'));
        add_action('admin_init', array( __CLASS__,'fix_session_bs') );

        if(is_admin())
		{
            GCMCustomDashboard::instance()->register();
			add_filter('admin_footer_text', array( __CLASS__, 'modify_footer_admin') );
			add_action('admin_enqueue_scripts', array( __CLASS__, 'post_screen_scripts' ));
		}
	}

    public static function fix_session_bs() {

        if(!session_id()) {
            session_start();
        }
    }

    function onPostStatusChange($ID, $post)
    {
        $result = MessageControler::send($post);
        $_SESSION['gcm_admin_notices'] = $result;
    }
    function wpSub_admin_notices()
    {
        if ( isset($_SESSION['gcm_admin_notices']) && ( $_SESSION['gcm_admin_notices'] != "" ))
        {
            $adminMessage = $_SESSION['gcm_admin_notices'];
            unset($_SESSION['gcm_admin_notices']);
            echo '<div id="notice" class="message"><p>' . $adminMessage . '</p></div>';
        }
    }

	function connection_posttype() {
		p2p_register_connection_type( array(
			'name' => 'app_to_message',
			'from' => 'app',
			'to' => 'message',
			'prevent_duplicates' => true,
            'reciprocal' => true,
			'cardinality' => 'many-to-many',
			'admin_column' => 'to',
			'admin_dropdown' => 'any',
			'admin_box' => array(
			    'show' => 'any',
			    'context' => 'advanced'
			),
            'title' => array( 'from' => 'Message', 'to' => 'Application' ),
            'from_labels' => array(
                'singular_name' => __( 'application', 'wp-gcm' ),
                'search_items' => __( 'Search application', 'wp-gcm' ),
                'not_found' => __( 'No application found.', 'wp-gcm' ),
                'create' => __( 'Create application', 'wp-gcm' ),
			  ),
			'to_labels' => array(
                'singular_name' => __( 'message', 'wp-gcm' ),
                'search_items' => __( 'Search message', 'wp-gcm' ),
                'not_found' => __( 'No message found.', 'wp-gcm' ),
                'create' => __( 'Create message', 'wp-gcm' ),
			  ),
            'fields' => array(
                'isSent' => array(
                    'title' => 'is Sent',
                    'type' => 'checkbox',
                ))
		) );

        p2p_register_connection_type( array(
            'name' => 'app_to_device',
            'from' => 'app',
            'to' => 'device',
            'prevent_duplicates' => true,
            'reciprocal' => true,
            'cardinality' => 'many-to-many',
            'admin_column' => 'to',
            'admin_dropdown' => 'any',
            'admin_box' => array(
                'show' => 'any',
                'context' => 'advanced'
            ),
            'title' => array( 'from' => 'Devices', 'to' => 'Application' ),
            'from_labels' => array(
                'singular_name' => __( 'application', 'wp-gcm' ),
                'search_items' => __( 'Search application', 'wp-gcm' ),
                'not_found' => __( 'No application found.', 'wp-gcm' ),
                'create' => __( 'Create application', 'wp-gcm' ),
            ),
            'to_labels' => array(
                'singular_name' => __( 'device', 'wp-gcm' ),
                'search_items' => __( 'Search device', 'wp-gcm' ),
                'not_found' => __( 'No device found.', 'wp-gcm' ),
                'create' => __( 'Create device', 'wp-gcm' ),
            )
        ) );

	}
	
	function post_screen_scripts() {
		$screen = get_current_screen(); 
		if (is_object($screen) && $screen->id=="message") {
			wp_enqueue_script( 'netcart-admin-script', plugins_url( 'script/MessagePostScript.js', __FILE__ ),array( 'jquery'), "1.0.0", false );
		}
	}

	function modify_footer_admin () {  
		echo 'Email hỗ trợ : <a href="mailto:nguyenvanduocit@gmail.com">nguyenvanduocit@gmail.com</a> | Điện thoại : 0167 297 1234';  
	} 
}
?>