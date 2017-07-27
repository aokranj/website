<?php
/**
 * Load the files where we define our options
 */
require( FRANZ_ROOTDIR . '/admin/customizer/validators.php' );
require( FRANZ_ROOTDIR . '/admin/customizer/active-callback.php' );
require( FRANZ_ROOTDIR . '/admin/customizer/options-general.php' );
require( FRANZ_ROOTDIR . '/admin/customizer/options-display.php' );


/**
 * Enqueue script for custom customize control.
 */
function franz_enqueue_customizer_scripts() {
	wp_enqueue_script( 'franz-codemirror', FRANZ_ROOTURI . '/js/codemirror/codemirror.min.js', array(), '', false );
	wp_enqueue_script( 'franz-customizer', FRANZ_ROOTURI . '/admin/customizer/customizer.js', array( 'jquery', 'customize-controls' ), false, true );
	wp_enqueue_style( 'franz-codemirror', FRANZ_ROOTURI . '/js/codemirror/codemirror.css', array(), '', 'all' );
	wp_enqueue_style( 'franz-customizer', FRANZ_ROOTURI . '/admin/customizer/customizer.css' );
	
	$l10n_data = array(
		'chosen_no_search_result'	=> __( 'Oops, nothing found.', 'franz-josef' ),
		'is_rtl'					=> is_rtl(),
		'import_select_file'		=> __( 'Please select the exported Franz Josef options file to import.', 'franz-josef' ),
		'delete'					=> __( 'Delete', 'franz-josef' ),
		'optional'					=> __( '(optional)', 'franz-josef' ),
		'link'						=> __( 'Link', 'franz-josef' ),
		'franz_uri'					=> FRANZ_ROOTURI
	);
	wp_localize_script( 'franz-customizer', 'franzCustomizer', $l10n_data );
}
add_action( 'customize_controls_enqueue_scripts', 'franz_enqueue_customizer_scripts' );


/**
 * Enqueue script to preview the changed settings
 */
function franz_enqueue_customizer_preview_scripts(){
	wp_enqueue_script( 'franz-customizer-preview', FRANZ_ROOTURI . '/admin/customizer/customizer-preview.js', array( 'jquery' ), false, true );
}
add_action( 'customize_preview_init', 'franz_enqueue_customizer_preview_scripts' );


/**
 * Add theme options to WordPress Customizer
 */
function franz_customize_register( $wp_customize ) {

	/* Register all settings */
	global $franz_defaults;
	$transport_settings = franz_get_customizer_transport_settings();
	$validator_settings = franz_get_customizer_validator_settings();
	foreach ( $franz_defaults as $setting => $default ) {
		$wp_customize->add_setting( 'franz_settings[' . $setting . ']', array(
			'type' 		=> 'option',
			'default'	=> $default,
			'transport' => $transport_settings[$setting],
			'sanitize_callback'	=> $validator_settings[$setting],
		) );
	}
	
	/* Register custom controls */
	franz_add_customizer_controls( $wp_customize );
	
	/* Options panel */	
	$wp_customize->add_panel( 'fj-general', array(
		'title'	=> __( 'Franz Josef: General', 'franz-josef' ),
	) );
	
	$wp_customize->add_panel( 'fj-display', array(
		'title'	=> __( 'Franz Josef: Display', 'franz-josef' ),
	) );
	
	/* Register the options controls */
	franz_customizer_general_options( $wp_customize );
	franz_customizer_display_options( $wp_customize );

}
add_action( 'customize_register', 'franz_customize_register' );


/**
 * Define the options for each setting
 */
function franz_get_customizer_transport_settings(){
	global $franz_defaults;
	
	/* By default set all settings to postMessage transport */
	$transport_settings = array();
	foreach ( $franz_defaults as $setting => $default ) {
		$transport_settings[$setting] = 'refresh';
	}
	
	/* Selectively set settings to postMessage transport */
	$settings = array(
		'slider_height',
		'copyright_text',
	);
	foreach ( $settings as $setting ) {
		$transport_settings[$setting] = 'postMessage';
	}
	
	return $transport_settings;
}


/**
 * Define the validator for each setting
 */
function franz_get_customizer_validator_settings(){
	global $franz_defaults;
	
	/* By default set all settings to no validator */
	$validator_settings = array();
	foreach ( $franz_defaults as $setting => $default ) {
		$validator_settings[$setting] = '';
	}
	
	/**
	 * Selectively set validator functions 
	 */

	/* Slider options */
	$validator_settings['slider_type']					= 'sanitize_text_field';
	$validator_settings['slider_specific_posts'] 		= 'sanitize_text_field';
    $validator_settings['slider_specific_categories']	= 'franz_validate_multiple_select';
	$validator_settings['slider_exclude_categories']	= 'sanitize_text_field';
	$validator_settings['slider_content']				= 'sanitize_text_field';
	$validator_settings['slider_postcount'] 			= 'absint';
	$validator_settings['slider_height'] 				= 'absint';
	$validator_settings['slider_interval']				= 'franz_validate_numeric';
	$validator_settings['slider_trans_duration'] 		= 'franz_validate_numeric';
	
	/* Front page options */
	$validator_settings['frontpage_posts_cats'] 	= 'franz_validate_multiple_select';
	$validator_settings['front_page_blog_columns']	= 'absint';
	
	/* Footer options */
	$validator_settings['copyright_text'] 	= 'wp_kses_post';
	
	/* Excerpt options */
	$validator_settings['excerpt_html_tags'] = 'trim';
	
	/* Footer widget options */
	$validator_settings['footerwidget_column']	= 'absint';
		
	/* Miscellaneous options */
	$validator_settings['favicon_url']	= 'esc_url_raw';
	$validator_settings['custom_css']	= 'franz_validate_css';
	
	return $validator_settings;
}


/**
 * Filter the returned settings from the database for live preview in customizer
 */
function franz_customizer_filter_settings( $franz_settings ){
		
	if ( isset( $_POST['customized'] ) ) {
		$customized_settings = json_decode( wp_unslash( $_POST['customized'] ), true );
		foreach ( $customized_settings as $setting => $value ) {
			if ( stripos( $setting, 'franz_settings' ) === 0 ) {
				$setting = str_replace( 'franz_settings[', '', str_replace( ']', '', $setting ) );
				$franz_settings[$setting] = $value;
			}
		}
	}
	
	return $franz_settings;
}
add_filter( 'franz_settings', 'franz_customizer_filter_settings' );