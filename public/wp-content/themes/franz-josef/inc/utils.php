<?php
if ( ! function_exists( 'franz_disect_it' ) ) :
/**
 * Prints out the content of a variable wrapped in <pre> elements.
 * For development and debugging use
*/
function franz_disect_it( $var = NULL, $exit = true, $comment = false ){
	
	if ( $comment ) {echo '<!--';}
	
	if ( $var !== NULL ){
		echo '<pre>';
		var_dump( $var );
		echo '</pre>';
	} else {
		echo '<strong>ERROR:</strong> You must pass a variable as argument to the <code>disect_it()</code> function.';
	}
	
	if ( $comment ) {echo '-->';}
	if ( $exit ) {exit();}
}
endif;


if ( ! function_exists( 'franz_substr' ) ) :
/**
 * Truncate a string by specified length
*/
function franz_substr( $string, $start = 0, $length = '', $suffix = '' ){
	
	if ( $length == '' ) return $string;
	
	if ( strlen( $string ) > $length ) {
		$trunc_string = substr( $string, $start, $length ) . $suffix;
	} else {
		$trunc_string = $string;	
	}
	return apply_filters( 'franz_substr', $trunc_string, $string, $start, $length, $suffix );
}
endif;


if ( ! function_exists( 'franz_truncate_words' ) ) :
/**
 * Truncate a string by specified word count
 *
 * @param string $string The string to be truncated
 * @param int $word_count The number of words to keep
 * @param string $suffix Optional, string to be appended to truncated string
 * @return string $trunc_string The truncated string
 *
 * @package Franz Josef
 * @since 1.6
*/
function franz_truncate_words( $string, $word_count, $suffix = '...' ){
   $string_array = explode( ' ', $string );
   $trunc_string = $string;
   if ( count ( $string_array ) > $word_count && $word_count > 0 )
      $trunc_string = implode( ' ', array_slice( $string_array, 0, $word_count ) ) . $suffix;
	  
   return apply_filters( 'franz_truncate_words', $trunc_string, $string, $word_count, $suffix );
}
endif;


if ( ! function_exists( 'franz_remove_anonymous_object_filter' ) ) :
/**
 * Remove an anonymous object filter.
 *
 * @param  string $tag    Hook name.
 * @param  string $class  Class name
 * @param  string $method Method name
 * @return void
 */
function franz_remove_anonymous_object_filter( $tag, $class, $method ){
	$filters = $GLOBALS['wp_filter'][ $tag ];

	if ( empty ( $filters ) ) return;

	foreach ( $filters as $priority => $filter ) {
		foreach ( $filter as $identifier => $function )	{
			if ( is_array( $function) && is_a( $function['function'][0], $class ) && $method === $function['function'][1] ) {
				remove_filter( $tag, array ( $function['function'][0], $method ), $priority );
			}
		}
	}
}
endif;


/**
 * Get the registered image sizes dimensions
 */
function franz_get_image_sizes( $size = '' ) {

	global $_wp_additional_image_sizes;

	$sizes = array();
	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	// Create the full array with sizes and crop info
	foreach( $get_intermediate_image_sizes as $_size ) {

			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

					$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
					$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
					$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );

			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

					$sizes[ $_size ] = array( 
							'width' => $_wp_additional_image_sizes[ $_size ]['width'],
							'height' => $_wp_additional_image_sizes[ $_size ]['height'],
							'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
					);

			}

	}

	// Get only 1 size if found
	if ( $size ) {

			if( isset( $sizes[ $size ] ) ) {
					return $sizes[ $size ];
			} else {
					return false;
			}

	}

	return $sizes;
}


/**
 * Sort array based on key named "score"
 */
function franz_sort_array_key_score( $a, $b ){
	if ( $b['score'] > $a['score'] ) return 1;
	return 0;
}