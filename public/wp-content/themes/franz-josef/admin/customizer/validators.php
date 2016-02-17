<?php
/**
 * Validate multiple select field
 */
function franz_validate_multiple_select( $input ){
	if ( ! is_array( $input ) ) $input = array();
	foreach ( $input as $key => $value ) {
		$input[$key] = sanitize_text_field( $value );
	}
	
	return $input;
}


/**
 * Validate custom css
 */
function franz_validate_css( $input ){
	$input = wp_filter_nohtml_kses( wp_strip_all_tags( $input ) );
	return $input;
}


/**
 * Validate floating numbers
 */
function franz_validate_numeric( $input ){
	if ( ! is_numeric( $input ) ) $input = '';
	return $input;
}