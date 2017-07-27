<?php
/**
 * @package Facebook Widget
 * @version 4.2
 */
/*
Plugin Name: Facebook Widget
Plugin URI: http://patelmilap.wordpress.com/
Description: This widget adds a Simple Facebook Page Like Widget into your wordpress website sidebar within few minutes.
Author: Milap Patel
Version: 4.2
Author URI: http://patelmilap.wordpress.com/
Text Domain: facebook-pagelike-widget
*/
function fbwidget_activate() {}
register_activation_hook( __FILE__, 'fbwidget_activate' );

function fbwidget_deactivate() {
        delete_option( 'widget_fbw_id' );
        unregister_sidebar( 'facebook_widget' );
        global $current_user;
        $user_id = $current_user->ID;
        delete_user_meta($user_id,'fb_ignore_notice');
}
register_deactivation_hook( __FILE__, 'fbwidget_deactivate' );

function LoadFbtextDomain() {
    load_plugin_textdomain( 'facebook-pagelike-widget',false, basename( dirname( __FILE__ ) ) );
}
add_action( 'init', 'LoadFbtextDomain' );

$widget_facebook_widget = get_option('widget_fbw_id');

if(empty($widget_facebook_widget)) {
	add_action('admin_notices', 'fb_admin_notice');
}

function fb_admin_notice() {
	global $current_user ;
    $user_id 	=	$current_user->ID;
	if ( ! get_user_meta($user_id, 'fb_ignore_notice') ) {
        echo '<div class="updated"><p>'; 
            printf(__( 'Please configure awesome widget <a href="widgets.php">here</a> | <a href="%1$s">Hide Notice</a>'), '?fb_nag_ignore=0', 'facebook-pagelike-widget' );
        echo "</p></div>";
	}
}

add_action('admin_init', 'fb_nag_ignore');
function fb_nag_ignore() {
	global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['fb_nag_ignore']) && '0' == $_GET['fb_nag_ignore'] ) {
             add_user_meta($user_id, 'fb_ignore_notice', 'true', true);
	}
}

if(!defined('FB_WIDGET_PLUGIN_URL'))
	define('FB_WIDGET_PLUGIN_URL', plugin_dir_url( __FILE__ ));

if(!defined('FB_WIDGET_PLUGIN_BASE_URL'))
	define('FB_WIDGET_PLUGIN_BASE_URL',dirname( __FILE__ ));

require_once(FB_WIDGET_PLUGIN_BASE_URL.'/fb_class.php');
require_once(FB_WIDGET_PLUGIN_BASE_URL.'/short_code.php');
?>