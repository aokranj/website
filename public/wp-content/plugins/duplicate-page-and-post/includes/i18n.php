<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 *
 * @package    Duplicate_Page_And_Post
 * @subpackage Duplicate_Page_And_Post/includes
 */

class Duplicate_Page_And_Post_i18n {

    /**
     * The ID of this plugin.
     *
     * @since    2.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * Initialize the class and set its properties.
     *
     * @since    2.0.0
     * @param      string    $plugin_name       The name of the plugin.
     */
    public function __construct( $plugin_name ) {

        $this->plugin_name = $plugin_name;

    }

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_name, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );

	}

}
