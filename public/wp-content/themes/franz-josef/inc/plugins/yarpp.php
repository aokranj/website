<?php
/**
 * Dequeue YARPP's CSS
 */
function franz_dequeue_yarpp_css(){
	if ( function_exists( 'yarpp_related' ) ) wp_dequeue_style( 'yarppRelatedCss' );
}
add_action( 'wp_footer', 'franz_dequeue_yarpp_css', 5 );


/**
 * Remove YARPP's auto-display related content from post content
 */
function franz_remove_yarpp_auto_content(){
	if ( function_exists( 'yarpp_related' ) ) franz_remove_anonymous_object_filter( 'the_content', 'YARPP', 'the_content' ); 
}
add_action( 'template_redirect', 'franz_remove_yarpp_auto_content' );


if ( ! function_exists( 'franz_related_posts' ) ) :
/**
 * Manages the display of related posts
 */
function franz_related_posts(){
	global $franz_settings;
	if ( ! function_exists( 'yarpp_related' ) && ! franz_has_custom_layout() ) return;
	
	/* Display a notice if YARPP has not been installed */
	if ( ! function_exists( 'yarpp_related' ) && franz_has_custom_layout() ) {
		get_template_part( 'yarpp-template-single' );
		return;
	}
	
	$display_settings = get_option( 'yarpp' );
	$display_settings = $display_settings['auto_display_post_types'];
	if ( ! is_singular() || ! in_array( get_post_type(), $display_settings ) ) return;
	
	$args = array(
		'template'	=> 'yarpp-template-single.php',
		'limit'		=> 3,
	);
	yarpp_related( apply_filters( 'franz_yarpp_args', $args ) );
}
endif;