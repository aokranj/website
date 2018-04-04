<?php
/**
 * Process the AJAX call to save the theme's settings
 */
function franz_ajax_update_handler() {
	global $wpdb;
	check_ajax_referer( 'franz_options-options', '_wpnonce' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		echo '<div class="error fade"><p>' . __( "Sorry, but you don't have the necessary permission to modify theme options.", 'franz-josef' ) . '</p></div>';
		die();
	}
	
	$data = $_POST['franz_settings'];
	$data = franz_settings_validator( $data );
	
	if ( get_settings_errors( 'franz_options' ) ){
		settings_errors( 'franz_options' );
	} else {
		if ( $data ) update_option( 'franz_settings', stripslashes_deep( $data ) );
		echo '<div class="updated fade"><p>' . __( 'Options saved.', 'franz-josef' ) . '</p></div>';
	}
		
	die();
}
add_action('wp_ajax_franz_ajax_update', 'franz_ajax_update_handler');


/**
 * Mark the user as having seen the thank you note
 */
function franz_dismiss_thanks() {
	
	$current_user = wp_get_current_user();
	update_user_meta( $current_user->ID, '_franz_thanks_shown', true );
	die();
}
add_action( 'wp_ajax_franz_dismiss_thanks', 'franz_dismiss_thanks');