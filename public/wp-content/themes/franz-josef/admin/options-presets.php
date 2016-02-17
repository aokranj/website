<?php
/**
 * Set the settings for options presets
*/
$authorised = true;
if ( isset($_POST['franz-preset'] ) ){ 
	if ( ! wp_verify_nonce( $_POST['franz-preset'], 'franz-preset' ) ) $authorised = false;
	if ( ! current_user_can( 'edit_theme_options' ) ) $authorised = false;
} else {
	return;
}

if ( $authorised ) {
	global $franz_settings, $franz_defaults;
			
	if ( $_POST['franz_options_preset'] == 'reset' ) {
		delete_option( 'franz_settings' );
		add_settings_error( 'franz_options', 2, __( 'Settings have been reset.', 'franz-josef' ), 'updated' );
	}
	
	// Update the global settings variable
	$franz_settings = array_merge( $franz_defaults, get_option( 'franz_settings', array() ) );

} else {
	wp_die( __( 'ERROR: You are not authorised to perform that operation', 'franz-josef' ) );
}