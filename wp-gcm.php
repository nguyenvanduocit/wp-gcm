<?php
/*
Plugin Name: wp-gcm
Version: 5.1.0
Description: plugin hỗ trợ flance
Author: Vô Minh
Plugin URI: http://muatocroi.com
Text Domain: wp-flance
Domain Path: /lang
*/

include dirname( __FILE__ ) . '/scb/load.php';

function _gcm_init() {
	require_once dirname( __FILE__ ) . '/core.php';
    GCM_Core::init();
}
scb_init( '_gcm_init' );

