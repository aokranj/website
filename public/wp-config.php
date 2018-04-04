<?php
/**
 *
 * AO Kranj WP configuration
 *
 */



/*
 * LOCAL configuration
 */
require realpath(__DIR__) .'/../config/wp-config.php';



/*
 * COMMON configuration
 */
require realpath(__DIR__) .'/wp-config-aokranj-defaults.php';



/*
 * The rest of WP configuration
 */



/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
