<?php
/**
 * Register translatable strings from the theme
 */
function franz_wpml_register_strings( $string = NULL ){
	if ( ! function_exists( 'icl_register_string' ) ) return;
	
	if ( is_array( $string ) ) {
		if ( ! array_key_exists( 'context', $string ) ) $string['context'] = 'Franz Josef theme';
		$franz_t_strings[] = $string;
	} else {
		global $franz_t_strings;
	}
	if ( ! is_array( $franz_t_strings ) ) return;
	
	foreach ( $franz_t_strings as $string ) {
		icl_register_string( $string['context'], $string['name'], $string['value'] );
	}
}


/**
 * Add translatable strings to the $franz_t_strings array
 *
 * @param 	array|string $strings can be an array of strings (when adding multiple strings at once), or a single string value.
 *			If it is an array, it is expected to be in the following structure: 
 *			array( 'context' => '', 'name' => '', 'value' => '' )
 * @param	string $name The name of the string to help identify the string, e.g. copyright text
 * @param	string $context The context of the string to help identify the string's origin, e.g. Franz Josef theme
 *
 */
function franz_add_t_string( $strings, $name = '', $context = 'Franz Josef theme' ){
	global $franz_t_strings;
	
	if ( is_array( $strings ) ) {
		foreach ( $strings as $string ) {
			if ( ! ( $string['value'] && $string['name'] ) ) continue;
			if ( ! $string['context'] ) $string['context'] = $context;
			$franz_t_strings[] =  $string;
		}
	} else {
		$string = array(
					'value' 	=> $strings,
					'name'		=> $name,
					'context'	=> $context
				);
		$franz_t_strings[] =  $string;
	}
}


/**
 * Get the translated string
 *
 * @param string $value 	The default value that will be returned if string hasn't been translated
 * @param string $name		The name of the string
 * @param string $context	The context of the string
 *
 */
function franz_icl_t( $name, $value = '', $context = 'Franz Josef theme' ){
	if ( ! function_exists( 'icl_t' ) ) return $value;
	else return icl_t( $context, $name, $value );
}


/**
 * Registers the translatable options
 */
function franz_register_t_options(){
	if ( ! function_exists( 'icl_t' ) ) return;
	global $franz_settings;
	
	$options = array( 
					array( 'name' => 'Copyright text', 'value' => $franz_settings['copy_text'], 'context' => '' ) ,
					array( 'name' => 'Home nav menu description', 'value' => $franz_settings['navmenu_home_desc'], 'context' => '' ) 
				);
	foreach ( $franz_settings['social_profiles'] as $social_profile ) {
		$options[] = array( 'name' => 'Social icon - ' . $social_profile['name'], 'value' => wp_kses_decode_entities( $social_profile['title'] ), 'context' => '' );
	}
	
	franz_add_t_string( $options );
}


/**
 * Replace the strings in the theme's settings with the translated strings
 */
function franz_translate_settings(){
	if ( ! function_exists( 'icl_t' ) ) return;
	if ( is_admin() ) return;
	
	global $franz_settings;
	$franz_settings['copy_text'] = franz_icl_t( 'Copyright text', $franz_settings['copy_text'] );
	$franz_settings['navmenu_home_desc'] = franz_icl_t( 'Home nav menu description', $franz_settings['navmenu_home_desc'] );
	
	foreach ( $franz_settings['social_profiles'] as $key => $social_profile ) {
		$franz_settings['social_profiles'][$key]['title'] = franz_icl_t( 'Social icon - ' . $social_profile['name'], wp_kses_decode_entities( $social_profile['title'] ) );
	}
}
add_action( 'template_redirect', 'franz_translate_settings' );


/**
 * Adjusts object IDs for multilingual functionality
 *
 */
function franz_object_id( $ids, $type = '', $return_original_if_missing = false, $language_code = NULL ){
	$is_array = true;
	if ( function_exists( 'icl_object_id' ) ) {
		if ( ! is_array( $ids ) ) {
			$ids = (array) $ids; $is_array = false;
		}
		foreach ( $ids as $key => $id ) {
			$current_type = ( ! $type ) ? get_post_type( $id ) : $type;
			$ids[$key] = icl_object_id( $id, $current_type, $return_original_if_missing, $language_code );
		}
	}
	
	if ( ! $is_array ) $ids = array_pop( $ids );
	return $ids;
}