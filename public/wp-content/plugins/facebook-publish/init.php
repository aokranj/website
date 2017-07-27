<?php
/*
Plugin Name: Facebook Publish
Plugin URI: http://codeholic.in/
Description: Automatically posts new articles to Facebook. Simple one click Integration. 
Version: 1.4.5
Author: Pramod Jodhani
Author URI: http://codeholic.in/
*/

define('FP_SETTINGS_SLUG' , "facebook-publish");

define('FP_PLUGIN_NAME' , "Facebook Publish");

require_once("fp-constants.php");



$fp_settings = get_option("fp_settings");

$fp_facebook = "";

$fp_session_data = "";

require_once( "lib/class/cAuth/index.php" );

//$fp_hybridauth = new cAuth( );
$fp_cauth = new cAuth( );

$fp_session_data = $fp_settings['session_data']; 

if($fp_session_data) {

	$fp_session_data = unserialize($fp_session_data);

	if( count($fp_session_data) > 0 ) {

		//$fp_cauth->load_session_data( serialize($fp_session_data) );
	}

}



function fp_activate() {
	
    require "fp_activate_plugin.php";

}

register_activation_hook( __FILE__, 'fp_activate' );



require_once("lib/functions.php");

require_once("lib/admin/init.php");

//pre($_SESSION);

?>