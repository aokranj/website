<?php
/**
 * Settings Validator
 * 
 * This file defines the function that validates the theme's options
 * upon submission.
*/
function franz_settings_validator( $input ){
	
	global $franz_options_validated;
	if ( $franz_options_validated == true ) return $input;
	
	if ( !isset( $_POST['franz_uninstall'] ) ) {
		global $franz_defaults, $allowedposttags;
		
		// Add <script> and <ins> tags to the allowed tags in code
		$allowedposttags = array_merge( $allowedposttags, array( 'script' => array( 'type' => array(), 'src' => array() ), 'ins' => array( 'class'=>array(), 'id'=>array(), 'style'=>array(), 'title'=>array(), 'data-ad-client'=>array(), 'data-ad-slot'=>array(), 'alt'=>array() ) ) );
		
		if ( isset( $_POST['franz_generic'] ) ) {
			$tab = $_POST['franz_tab'];
			$input = apply_filters( 'franz_validate_options_' . $tab, $input );
		}
		
		$franz_options_validated = true;
		
		// Merge the new settings with the previous one (if exists) before saving
		$last_settings = get_option( 'franz_settings', array() );
		if( $last_settings ) $input = array_merge( $last_settings, (array) $input );
		
		/* Only save options that have different values than the default values */
		foreach ( $input as $key => $value ){
			if ( array_key_exists( $key, $franz_defaults ) && ( $franz_defaults[$key] === $value || $value === '' ) )
				unset( $input[$key] );
		}

		if ( $input ) {
			$input = array_merge( array( 'db_version' => $franz_defaults['db_version'] ), $input );
		} else {
			delete_option( 'franz_settings' );
			return false;
		}

	} // Closes the uninstall conditional
	
	return $input;
}


/**
 * Define the data validation functions
*/
function franz_validate_digits( $input, $option_name, $error_message ){
	global $franz_defaults;
	if ( ! isset( $input[$option_name] ) ) return $input;
	if ( ! empty( $input[$option_name] ) || '0' === $input[$option_name] ){
		if ( !ctype_digit( $input[$option_name] ) ) {
			$input[$option_name] = $franz_defaults[$option_name];
			add_settings_error( 'franz_options', 2, $error_message);
		}
	} else {
		$input[$option_name] = $franz_defaults[$option_name];
	}
	return $input;
}

function franz_validate_dropdown( $input, $option_name, $possible_values, $error_message ){
	if ( ! isset( $input[$option_name] ) ) return $input;
	if ( ! in_array( $input[$option_name], $possible_values ) ){
		unset( $input[$option_name] );
		add_settings_error( 'franz_options', 2, $error_message );
	}
	return $input;
}

function franz_validate_url( $input, $option_name, $error_message ) {
	global $franz_defaults;
	if ( ! empty( $input[$option_name] ) ){
		$input[$option_name] = esc_url_raw( $input[$option_name] );
		if ( $input[$option_name] == '' ) {
			$input[$option_name] = $franz_defaults[$option_name];
			add_settings_error( 'franz_options', 2, $error_message);
		}	
	}	
	return $input;
}

function franz_validate_colours( $options ) {
	global $franz_defaults;
	foreach ( $options as $key => $option ){
		if ( in_array( $key, array( 'colour_preset', 'colour_presets' ) ) ) continue;
		if ( ! empty( $option ) ){
			if ( stripos( $option, '#' ) !== 0 ) {
				$options[$key] = '#' . $option;
			}
			$options[$key] = franz_convert_shortform_colour( $options[$key] );
		} else {
			$option = $franz_defaults[$key];
		}
	}
	return $options;
}

function franz_convert_shortform_colour( $colour ){
	if ( strlen( $colour ) == 4 ) {
		$colour = preg_replace( '/\#([0-9a-fA-F] )([0-9a-fA-F] )([0-9a-fA-F] )/', '#$1$1$2$2$3$3$4', $colour );
	}
	
	return $colour;
}
