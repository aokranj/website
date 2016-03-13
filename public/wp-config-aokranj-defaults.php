<?php
/**
 * Default AO Kranj website configuration settings
 *
 * Common to all environments.
 */

/*
 * Database
 */
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix  = 'wp_';



/*
 * URI
 */
if (!defined('WP_HOME')) {
    define('WP_HOME',    'http://www.aokranj.com');
}
if (!defined('WP_SITEURL')) {
    define('WP_SITEURL', 'http://www.aokranj.com');
}



define('WPLANG', '');



if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', false);
}
