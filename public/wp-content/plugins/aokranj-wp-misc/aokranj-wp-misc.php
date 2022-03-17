<?php
/*
Plugin Name: aokranj-wp-misc
Plugin URI: https://github.com/aokranj/website-aokranj.si
Description: WordPress tweaks for aokranj.com
Version: 1.0.0
Author: Bostjan Skufca Jese
Author URI: https://github.com/bostjan
*/

require_once __DIR__ . '/src/admin-site-health-disable-filesystem-credentials.php';
require_once __DIR__ . '/src/admin-site-health-disable-vcs-test.php';
require_once __DIR__ . '/src/mail-from.php';
require_once __DIR__ . '/src/robotstxt.php';
