<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'database');

/** MySQL database username */
define('DB_USER', 'user');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '1[ke#P#j;+PtAzo.<_pXUxk(*q?@|N2)>CCUhXj5gT<iieo*e|oM?& kTAm)~W9?');
define('SECURE_AUTH_KEY',  'lesF-O[(o8&oK7eTH4zVysOo??{>9W$/y+Z(3<n-9HjDYB_;!yWMmv_AOKf#AoU:');
define('LOGGED_IN_KEY',    'z+%0TnY9oLm.8Z7PNYbdqAF]wWH;=bIWZ|6VR)/xd3EHk:rB`7/,-J+7}H*x!? .');
define('NONCE_KEY',        '^vFa Ys+UoH3<Cc}mim3H=cwW7=ysRL{`-^Se^x&@.ZQq`L+0Vuy#l(]Vh,lwsX6');
define('AUTH_SALT',        'qgqh{6gn5L3.`-l>q=t`>yC?TxnC.NJMR&~DiTmJ`<:$`3{Ffcz.fzosa@!)C(nw');
define('SECURE_AUTH_SALT', 'a8.P2z/6DKn0x8E*Ha9H,[z4?.<_jUC7|+-@&)GHsf1AM[mGCvd[rV8wEV:eCw_:');
define('LOGGED_IN_SALT',   '8Vtxi7{J/cE(-&R@w7qqm9i<mI3$-0q7vnu!+Z>Q~eI+0c] [Eb|$auUZ}LR@2q#');
define('NONCE_SALT',       'ZI_Dao}ljcZG-xN@ys8!!:{4-x%/eh:^T{?f[}^X>(JFj(u8Vu^-%]3umH=8)C&]');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

