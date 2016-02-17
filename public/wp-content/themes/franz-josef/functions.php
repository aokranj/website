<?php
/**
 * Load the various theme files
*/
define( 'FRANZ_ROOTDIR', get_template_directory() );
define( 'FRANZ_ROOTURI', get_template_directory_uri() );
define( 'FJ_CHILDDIR', get_stylesheet_directory() );
define( 'FJ_CHILDURI', get_stylesheet_directory_uri() );

require( FRANZ_ROOTDIR . '/inc/setup.php' );
require( FRANZ_ROOTDIR . '/admin/settings-init.php' );
require( FRANZ_ROOTDIR . '/inc/utils.php' );
require( FRANZ_ROOTDIR . '/inc/scripts.php' );
require( FRANZ_ROOTDIR . '/inc/head.php' );
require( FRANZ_ROOTDIR . '/inc/header.php' );
require( FRANZ_ROOTDIR . '/inc/menus.php' );
require( FRANZ_ROOTDIR . '/inc/slider.php' );
require( FRANZ_ROOTDIR . '/inc/stacks.php' );
require( FRANZ_ROOTDIR . '/inc/comments.php' );
require( FRANZ_ROOTDIR . '/inc/user.php' );
require( FRANZ_ROOTDIR . '/inc/widgets.php' );
require( FRANZ_ROOTDIR . '/inc/loop.php' );
require( FRANZ_ROOTDIR . '/inc/parts.php' );
require( FRANZ_ROOTDIR . '/vendors/menu-item-custom-fields/menu-item-custom-fields.php' );

/* Natively-supported plugins */
require( FRANZ_ROOTDIR . '/inc/plugins/yarpp.php' );
