<?php
/**
 * Retrieve the theme's user settings and default settings. Individual files can access
 * these setting via a global variable call, so database query is only
 * done once.
*/
function franz_get_settings(){
	global $franz_defaults;
	$franz_settings = array_merge( $franz_defaults, (array) get_option( 'franz_settings', array() ) );
	return apply_filters( 'franz_settings', $franz_settings );
}


/**
 * Initialise settings
 */
function franz_init_settings(){
	
	require( FRANZ_ROOTDIR . '/admin/options-defaults.php' );
	global $franz_defaults, $franz_settings;
	$franz_defaults = apply_filters( 'franz_defaults', $franz_defaults );
	$franz_settings = franz_get_settings();
}
add_action( 'franz_setup', 'franz_init_settings' );


/**
 * Include files required for the theme's options 
 */
require( FRANZ_ROOTDIR . '/admin/user.php' );
require( FRANZ_ROOTDIR . '/admin/wpml-helper.php' );
require( FRANZ_ROOTDIR . '/admin/settings.php' );
require( FRANZ_ROOTDIR . '/admin/customizer/controls.php' );
require( FRANZ_ROOTDIR . '/admin/customizer/customizer.php' );
require( FRANZ_ROOTDIR . '/admin/settings-validator.php');
require( FRANZ_ROOTDIR . '/admin/ajax-handler.php');
require( FRANZ_ROOTDIR . '/admin/thank-you.php');


/** 
 * Add the theme options page
*/
function franz_options_init() {
	global $franz_settings;
	
	$franz_settings['hook_suffix'] = add_theme_page( __( 'Franz Josef Options', 'franz-josef' ), __( 'Franz Josef Options', 'franz-josef' ), 'edit_theme_options', 'franz_options', 'franz_options' );
	
	add_action( 'admin_print_styles-' . $franz_settings['hook_suffix'], 'franz_admin_options_style' );
	add_action( 'admin_print_scripts-' . $franz_settings['hook_suffix'], 'franz_admin_scripts' );
	// add_action( 'admin_head-' . $franz_settings['hook_suffix'], 'franz_custom_style' );
	add_action( 'admin_head-' . $franz_settings['hook_suffix'], 'franz_register_t_options' );
	add_action( 'admin_head-' . $franz_settings['hook_suffix'], 'franz_wpml_register_strings', 20 );
	
	do_action( 'franz_options_init' );
}
add_action( 'admin_menu', 'franz_options_init', 8 );


/**
 * Allow users with 'edit_theme_options' capability to be able to modify the theme's options
 */
function franz_options_page_capability( $cap ){
	return apply_filters( 'franz_options_page_capability', 'edit_theme_options' );
}
add_filter( 'option_page_capability_franz_options', 'franz_options_page_capability' );


/**
 * Add JavaScript for the theme's options page
*/
function franz_options_js(){ 
    global $franz_settings;
	
	$tab = 'addons';
	if ( isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $franz_settings['options_tabs'] ) ){ $tab = $_GET['tab']; }
	?>
	<script type="text/javascript">
	//<![CDATA[
		var franz_tab = '<?php echo $tab; ?>';
		var franz_settings = <?php echo json_encode( $franz_settings ); ?>;
		var franz_uri = '<?php echo esc_url( FRANZ_ROOTURI ); ?>';
	//]]>
	</script>
	<?php
}


/**
 * Admin footer
 */
function franz_admin_footer(){
	global $franz_settings;
	add_action( 'admin_footer-' . $franz_settings['hook_suffix'], 'franz_options_js' );
}
add_action( 'admin_menu', 'franz_admin_footer' );


if ( ! function_exists( 'franz_admin_options_style' ) ) :
/**
 * Enqueue style for admin page
*/
function franz_admin_options_style() {

	$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : '';
	
	wp_enqueue_style( 'franz-admin-style', FRANZ_ROOTURI . '/admin/admin.css' );
	if ( is_rtl() ) wp_enqueue_style( 'franz-admin-style-rtl', FRANZ_ROOTURI . '/admin/admin-rtl.css' );
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_style( 'franz-codemirror', FRANZ_ROOTURI . '/js/codemirror/codemirror.css', array(), '', 'all' );
	wp_enqueue_style( 'font-awesome', FRANZ_ROOTURI . '/fonts/font-awesome/css/font-awesome.min.css' );
	wp_deregister_style( 'chosen' ); wp_enqueue_style( 'chosen', FRANZ_ROOTURI . '/js/chosen/chosen.min.css', array(), '', 'all' );
}
endif;


/**
 * Script required for the theme options page
 */
function franz_admin_scripts() {
	global $franz_settings;
	$tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : '';
	
    wp_enqueue_media();
	wp_enqueue_script( 'custom-header' );
    wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'franz-codemirror', FRANZ_ROOTURI . '/js/codemirror/codemirror.min.js', array(), '', false );
	wp_enqueue_script( 'chosen', FRANZ_ROOTURI . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), '', false );
	wp_enqueue_script( 'jquery-ui-sortable' );
	
	wp_enqueue_script( 'franz-admin', FRANZ_ROOTURI . '/admin/js/admin.js', array( 'jquery', 'franz-codemirror', 'chosen', 'jquery-ui-sortable', 'media-upload', 'thickbox' ), '', false );
	
	$l10n_data = array(
		'chosen_no_search_result'	=> __( 'Oops, nothing found.', 'franz-josef' ),
		'is_rtl'					=> is_rtl(),
		'import_select_file'		=> __( 'Please select the exported Franz Josef options file to import.', 'franz-josef' ),
		'delete'					=> __( 'Delete', 'franz-josef' ),
		'optional'					=> __( '(optional)', 'franz-josef' ),
		'link'						=> __( 'Link', 'franz-josef' ),
		'franz_uri'					=> FRANZ_ROOTURI
	);
	wp_localize_script( 'franz-admin', 'franzAdminScript', $l10n_data );
}


if ( ! function_exists( 'franz_options_tabs' ) ) :
/**
 * Generates the tabs in the theme's options page
*/
function franz_options_tabs( $current = 'about', $tabs = array() ) {

	global $franz_settings;
	if ( ! $tabs ) $tabs = $franz_settings['options_tabs'];
	
	/* Place the About tab last */
	$about_tab = $tabs['about'];
	unset( $tabs['about'] );
	$tabs['about'] = $about_tab;
	
	$links = array();
	foreach( $tabs as $tab => $name ) {
		if ( $tab == $current ) $links[] = "<a class='nav-tab nav-tab-$tab nav-tab-active' href='?page=franz_options&amp;tab=$tab'>$name</a>";
		else $links[] = "<a class='nav-tab nav-tab-$tab' href='?page=franz_options&amp;tab=$tab'>$name</a>";
	}
	
	echo '<h3 class="options-tab clearfix">';
	foreach ( $links as $link )	echo $link;
	echo '<a class="toggle-all" href="#">' . __( 'Toggle all options boxes', 'franz-josef' ) . '</a>';
	echo '</h3>';
}
endif;


if ( ! function_exists( 'franz_options_tabs_content' ) ) :
/**
 * Output the options content
 */
function franz_options_tabs_content( $tab ){
	$options_file = FRANZ_ROOTDIR . '/admin/options-' . $tab . '.php';
	if ( file_exists( $options_file ) ) include( $options_file );
	else include( FRANZ_ROOTDIR . '/admin/options-generic.php' );
	
	if ( function_exists( 'franz_options_' . $tab ) ) call_user_func( 'franz_options_' . $tab );
	else franz_options_generic( $tab );
}
endif;


/**
 * Remove the General and Display tabs as they have been migrated to the Customizer
 */
function franz_remove_options_tabs( $franz_settings ){
	if ( ! is_admin() ) return $franz_settings;
	
	if ( isset( $franz_settings['options_tabs']['general'] ) ) unset( $franz_settings['options_tabs']['general'] );
	if ( isset( $franz_settings['options_tabs']['display'] ) ) unset( $franz_settings['options_tabs']['display'] );
	
	return $franz_settings;
}
add_filter( 'franz_settings', 'franz_remove_options_tabs' );


/**
 * Add a link to the theme's options page in the admin bar
*/
function franz_wp_admin_bar_theme_options(){
	if ( ! current_user_can( 'edit_theme_options' ) ) return;
	global $wp_admin_bar;
	$wp_admin_bar->add_menu( array( 
								'parent' 	=> 'appearance',
								'id' 		=> 'franz-options',
								'title' 	=> 'Franz Josef Options',
								'href' 		=> admin_url( 'themes.php?page=franz_options' ) ) );
}
add_action( 'admin_bar_menu', 'franz_wp_admin_bar_theme_options', 61 );


if ( ! function_exists( 'franz_docs_link' ) ) :
/**
 * Display a link to the documentation page
 */
function franz_docs_link( $suffix = '' ){
	return;
}
endif;