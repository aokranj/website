<?php
/**
 * This file that defines the core plugin class
 *
 * This class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      2.0.0
 *
 * @package    Duplicate_Page_And_Post
 * @subpackage Duplicate_Page_And_Post/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Duplicate_Page_And_Post
 * @subpackage Duplicate_Page_And_Post/includes
 * @author     pluginsforwp
 */
class Duplicate_Page_And_Post {

	/**
	 * This loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Duplicate_Page_And_Post_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * This unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * This is the current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    This is the current version of the plugin.
	 */
	protected $version;

	/**
	 * This is the function to load the hooks and assign variables.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'duplicate-page-and-post';
		$this->version = '2.1.1';

		$this->load_dependencies();
        $this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * - Duplicate_Page_And_Post_Loader. Orchestrates the hooks of this plugin.
     * - Duplicate_Page_And_Post_i18n. Defines internationalization for this plugin.
     * - Duplicate_Page_And_Post_Admin. Defines all hooks for the admin area.
	 *
	 * Created an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/*
		 * This class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/i18n.php';

		/*
		 * This class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/duplicate-page-and-post.php';

		$this->loader = new Duplicate_Page_And_Post_Loader();

	}

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    2.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Duplicate_Page_And_Post_i18n( $this->get_plugin_name() );

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Registered all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    2.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $post_types = array(
            'forum',
            'page',
            'post',
            'portfolio',
            'product',
            'project',
            'reply',
            'topic',
            'shop_coupon'
        );

        $plugin_admin = new Duplicate_Page_And_Post_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

        $this->loader->add_action( 'admin_action_duplicate', $plugin_admin, 'duplicate_action' );
        $this->loader->add_action( 'post_row_actions', $plugin_admin, 'admin_tables_duplicate_link', 10, 2 );
        $this->loader->add_action( 'page_row_actions', $plugin_admin, 'admin_tables_duplicate_link', 10, 2 );

        $this->loader->add_action( 'post_submitbox_start', $plugin_admin, 'submitbox_duplicate_link' );
        $this->loader->add_action( 'wp_before_admin_bar_render', $plugin_admin, 'admin_bar_duplicate_link' );

        foreach ( $post_types as $post_type ) {
            $this->loader->add_action( 'bulk_actions-edit-' . $post_type, $plugin_admin, 'bulk_actions_duplicate_link' );
            $this->loader->add_action( 'handle_bulk_actions-edit-' . $post_type, $plugin_admin, 'duplicate_bulk_action', 10, 3 );
        }

        $this->loader->add_filter( 'the_content', $plugin_admin, 'confirm_post', 99 );
        $this->loader->add_filter( 'wp', $plugin_admin, 'confirm_page', 99 );

    }

	/**
	 * This should be run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {

		$this->loader->run();

	}

	/**
	 * Here we assigned plugin name
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;

	}

	/**
	 * This reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Duplicate_Page_And_Post_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;

	}

	/**
	 * Here we assigned the version
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;

	}

}
