<?php
/**
 * This file describe the starting point of all actions
 *
 * @since             2.0.0
 * @package           Duplicate_Page_And_Post
 *
 * Plugin Name:       Duplicate Page And Post
 * Plugin URI:        https://wordpress.org/plugins/duplicate-page-and-post/
 * Description:       Duplicate a Post or Page with just one click.
 * Version:           2.1.1
 * Author:            pluginsforwp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       duplicate-page-and-post
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! define( 'DUPLICATE-PAGE-AND-POST', plugin_dir_path( __FILE__ ) ) ) {
	die;
}

/*
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/duplicate-page-and-post.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_duplicate_page_and_post() {

	$plugin = new Duplicate_Page_And_Post();
	$plugin->run();

}
run_duplicate_page_and_post();
