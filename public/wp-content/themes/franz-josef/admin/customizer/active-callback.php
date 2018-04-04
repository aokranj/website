<?php
/**
 * Check if the site has static front page
 */
function franz_has_static_front_page(){
	if ( 'page' == get_option( 'show_on_front' ) ) return true;
	return false;
}


/**
 * Check if the site has static posts page
 */
function franz_has_static_posts_page(){
	if ( get_option( 'page_for_posts' ) ) return true;
	return false;
}