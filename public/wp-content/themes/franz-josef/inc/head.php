<?php
/**
 * Check if the user settings are different than the default settings
 *
 * @param array $settings
 * @global array $franz_settings
 * @global array $franz_defaults
 * @return bool
 */
function franz_is_settings_custom( $settings ){
	global $franz_defaults, $franz_settings;
	$settings = (array) $settings;
	
	$diff = false;
	foreach ( $settings as $key ) {
		if ( $franz_defaults[$key] !== $franz_settings[$key] ) {
			$diff = true;
			break;
		}
	}
	
	return $diff;
}


/**
 * Basic CSS minifier, based on the codes by Kit McAllister (http://kitmacallister.com/2011/minify-css-with-php/)
 *
 * @param string Regular CSS string to be minified
 * @return string Minified CSS string
 *
 * @package Franz Josef
 * @since Franz Josef 1.8
 */
function franz_minify_css( $css ){

	/* Strip comments */
	$css = preg_replace('!/\*.*?\*/!s','', $css);
	$css = preg_replace('/\n\s*\n/',"\n", $css);

	/* Minify */
	$css = preg_replace('/[\n\r \t]/',' ', $css);
	$css = preg_replace('/ +/',' ', $css);
	$css = preg_replace('/ ?([,:;{}] ) ?/','$1',$css);

	/* Kill trailing semicolon */
	$css = preg_replace('/;}/','}',$css);

	return $css;
}


/**
 * This function prints out the title for the website.
 * If present, the theme will display customised site title structure.
*/
function franz_title( $title, $sep = '-', $seplocation = '' ){
	global $franz_settings;
	$default_title = $title;
	
	if ( is_feed() ){
		$title = $default_title;
	} elseif ( is_front_page() ) { 
		$title = get_bloginfo( 'name' );
		$title .= ( $desc = get_bloginfo( 'description' ) ) ? " - " . $desc : '';
	} else {
		$title = $default_title . " - " . get_bloginfo( 'name' );
	}
	
	return ent2ncr( apply_filters( 'franz_title', trim( $title ) ) );
}


if ( ! function_exists( '_wp_render_title_tag' ) ) :
/**
 * Backward compatibility for site title for WP < 4.1
 */
function franz_title_tag(){
	?>
    <title><?php wp_title(); ?></title>
    <?php
}
add_action( 'wp_head', 'franz_title_tag' );
add_filter( 'wp_title', 'franz_title', 10, 3 );	
endif;


/**
 * Prints out custom <head> tags
 *
 * @package Franz Josef
 * @since Franz Josef 1.8
 */
function franz_custom_head_tags(){
	global $franz_settings;
	echo $franz_settings['head_tags'];
}
add_action( 'wp_head', 'franz_custom_head_tags', 100 );


/**
 * Sets the various customised styling according to the options set for the theme.
 *
 * @param bool $out Whether to echo the styles or not
 * @param bool $minify Whether to minify the styles or not
 * @param bool $force_all If set to true, it returns the full generated CSS as it will be in the front end
*/
function franz_custom_style( $echo = true, $minify = true, $force_all = false ){
	global $franz_settings;

	if ( ! is_bool( $echo ) ) $echo = true;
	$style = '';
	
	// only get the custom css styles when we're not in the admin mode
	if ( ! is_admin() || $force_all ) {
		if ( $franz_settings['custom_css'] ) { $style .= $franz_settings['custom_css']; }
	}
	
	$style = apply_filters( 'franz_custom_style', $style, $echo, $force_all );
	if ( $minify ) $style = franz_minify_css( $style );
	
    if ( $style && $echo ) echo '<style type="text/css">' . "\n" . $style . "\n" . '</style>' . "\n";
	else return $style;
}
add_action( 'wp_head', 'franz_custom_style' );