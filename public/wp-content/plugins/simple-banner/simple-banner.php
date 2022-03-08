<?php
/**
 * Plugin Name: Simple Banner
 * Plugin URI: https://github.com/rpetersen29/simple-banner
 * Description: Display a simple banner at the top of your website.
 * Version: 2.11.0
 * Author: Ryan Petersen
 * Author URI: http://rpetersen29.github.io/
 * License: GPL2
 *
 * @package Simple Banner
 * @version 2.11.0
 * @author Ryan Petersen <rpetersen.dev@gmail.com>
 */
define ('VERSION', '2.11.0');

register_activation_hook( __FILE__, 'simple_banner_activate' );
function simple_banner_activate() {
	add_action('admin_menu', 'simple_banner_menu');
}

// Disabled Pages/Posts functionns
function get_disabled_pages_array() {
	return array_filter(explode(',', get_option('disabled_pages_array')));
}
function get_post_object() {
	return get_posts(array('include' => array(get_the_ID())));
}
function get_is_current_page_a_post() {
	return !empty(get_post_object());
}
function get_disabled_on_posts() {
	return get_option('disabled_on_posts');
}
function get_disabled_on_current_page() {
	$disabled_on_current_page = (!empty(get_disabled_pages_array()) && in_array(get_the_ID(), get_disabled_pages_array()))
								|| (get_disabled_on_posts() && get_is_current_page_a_post());
	return $disabled_on_current_page;
}


add_action( 'wp_enqueue_scripts', 'simple_banner' );
function simple_banner() {
    // Enqueue the style
	wp_register_style('simple-banner-style',  plugin_dir_url( __FILE__ ) .'simple-banner.css', '', VERSION);
    wp_enqueue_style('simple-banner-style');
	// Set Script parameters
	$disabled_on_current_page = get_disabled_on_current_page();
	$script_params = array(
		// script specific parameters
		'version' => VERSION,
		'hide_simple_banner' => get_option('hide_simple_banner'),
		'simple_banner_position' => get_option('simple_banner_position'),
		'header_margin' => get_option('header_margin'),
		'header_padding' => get_option('header_padding'),
		'simple_banner_text' => get_option('simple_banner_text'),
		'pro_version_enabled' => get_option('pro_version_enabled'),
		'disabled_on_current_page' => $disabled_on_current_page,
		// debug specific parameters
		'debug_mode' => get_option('debug_mode'),
		'id' => get_the_ID(),
		'disabled_pages_array' => get_disabled_pages_array(),
		// 'post_object' => get_post_object(),
		'is_current_page_a_post' => get_is_current_page_a_post(),
		'disabled_on_posts' => get_disabled_on_posts(),
		'simple_banner_font_size' => get_option('simple_banner_font_size'),
		'simple_banner_color' => get_option('simple_banner_color'),
		'simple_banner_text_color' => get_option('simple_banner_text_color'),
		'simple_banner_link_color' => get_option('simple_banner_link_color'),
		'simple_banner_close_color' => get_option('simple_banner_close_color'),
		'simple_banner_text' => $disabled_on_current_page ? '' : get_option('simple_banner_text'),
		'simple_banner_custom_css' => get_option('simple_banner_custom_css'),
		'simple_banner_scrolling_custom_css' => get_option('simple_banner_scrolling_custom_css'),
		'simple_banner_text_custom_css' => get_option('simple_banner_text_custom_css'),
		'simple_banner_button_css' => get_option('simple_banner_button_css'),
		'site_custom_css' => get_option('site_custom_css'),
		'keep_site_custom_css' => get_option('keep_site_custom_css'),
		'site_custom_js' => get_option('site_custom_js'),
		'keep_site_custom_js' => get_option('keep_site_custom_js'),
		'wp_body_open_enabled' => get_option('wp_body_open_enabled'),
		'wp_body_open' => function_exists('wp_body_open'),
		'close_button_enabled' => get_option('close_button_enabled'),
		'close_button_expiration' => get_option('close_button_expiration'),
		'close_button_cookie_set' => isset($_COOKIE['simplebannerclosed']),
	);
	// Enqueue the script
    wp_register_script('simple-banner-script', plugin_dir_url( __FILE__ ) . 'simple-banner.js', array( 'jquery' ), VERSION);
	wp_localize_script('simple-banner-script', 'simpleBannerScriptParams', $script_params);
    wp_enqueue_script('simple-banner-script');
}

// Use `wp_body_open` action
if ( function_exists( 'wp_body_open' ) && get_option('wp_body_open_enabled') ) {
	add_action( 'wp_body_open', 'simple_banner_body_open' );
}
function simple_banner_body_open() {
	// if not disabled use wp_body_open
	$disabled_on_current_page = get_disabled_on_current_page();
	$close_button_enabled = get_option('close_button_enabled');
	$closed_cookie = $close_button_enabled && isset($_COOKIE['simplebannerclosed']);
	$closed_button = get_option('close_button_enabled') ? '<button id="simple-banner-close-button" class="simple-banner-button">&#x2715;</button>' : '';

	if (!$disabled_on_current_page && !$closed_cookie) {
		echo '<div id="simple-banner" class="simple-banner"><div class="simple-banner-text"><span>' 
		. get_option('simple_banner_text') 
		. '</span></div>' 
		. $closed_button 
		. '</div>';
	}
}

// Prevent CSS removal from optimizer plugins by putting a dummy item in the
add_action( 'wp_footer', 'prevent_css_removal');
function prevent_css_removal()
{
	echo '<div class="simple-banner simple-banner-text" style="display:none !important"></div>';
}

// Add custom CSS/JS
add_action( 'wp_head', 'simple_banner_custom_options');
function simple_banner_custom_options()
{
	$closed_cookie = get_option('close_button_enabled') && isset($_COOKIE["simplebannerclosed"]);

	$disabled_on_current_page = get_disabled_on_current_page();
	$banner_is_disabled = $disabled_on_current_page || get_option('hide_simple_banner') == "yes";

	if ($banner_is_disabled || $closed_cookie){
		echo '<style type="text/css">.simple-banner{display:none;}</style>';
	}

	if (!$banner_is_disabled && !$closed_cookie && get_option('header_margin') != ""){
		echo '<style id="simple-banner-header-margin" type="text/css">header{margin-top:' . get_option('header_margin') . ';}</style>';
	}

	if (!$banner_is_disabled && !$closed_cookie && get_option('header_padding') != ""){
		echo '<style id="simple-banner-header-padding" type="text/css" >header{padding-top:' . get_option('header_padding') . ';}</style>';
	}

	if (get_option('simple_banner_position') != ""){
		if (get_option('simple_banner_position') == 'footer'){
			echo '<style type="text/css">.simple-banner{position:fixed;bottom:0;}</style>';
		} else {
			echo '<style type="text/css">.simple-banner{position:' . get_option('simple_banner_position') . ';}</style>';
		}
	}

	if (get_option('simple_banner_font_size') != ""){
		echo '<style type="text/css">.simple-banner .simple-banner-text{font-size:' . get_option('simple_banner_font_size') . ';}</style>';
	}

	if (get_option('simple_banner_color') != ""){
		echo '<style type="text/css">.simple-banner{background:' . get_option('simple_banner_color') . ';}</style>';
	} else {
		echo '<style type="text/css">.simple-banner{background: #024985;}</style>';
	}

	if (get_option('simple_banner_text_color') != ""){
		echo '<style type="text/css">.simple-banner .simple-banner-text{color:' . get_option('simple_banner_text_color') . ';}</style>';
	} else {
		echo '<style type="text/css">.simple-banner .simple-banner-text{color: #ffffff;}</style>';
	}

	if (get_option('simple_banner_link_color') != ""){
		echo '<style type="text/css">.simple-banner .simple-banner-text a{color:' . get_option('simple_banner_link_color') . ';}</style>';
	} else {
		echo '<style type="text/css">.simple-banner .simple-banner-text a{color:#f16521;}</style>';
	}

	if (get_option('simple_banner_close_color') != ""){
		echo '<style type="text/css">.simple-banner .simple-banner-button{color:' . get_option('simple_banner_close_color') . ';}</style>';
	}

	if (get_option('simple_banner_custom_css') != ""){
		echo '<style type="text/css">.simple-banner{'. get_option('simple_banner_custom_css') . '}</style>';
	}

	if (get_option('simple_banner_scrolling_custom_css') != ""){
		echo '<style type="text/css">.simple-banner.simple-banner-scrolling{'. get_option('simple_banner_scrolling_custom_css') . '}</style>';
	}

	if (get_option('simple_banner_text_custom_css') != ""){
		echo '<style type="text/css">.simple-banner .simple-banner-text{'. get_option('simple_banner_text_custom_css') . '}</style>';
	}

	if (get_option('simple_banner_button_css') != ""){
		echo '<style type="text/css">.simple-banner .simple-banner-button{'. get_option('simple_banner_button_css') . '}</style>';
	}

	$remove_site_custom_css = ($banner_is_disabled || $closed_cookie) && get_option('keep_site_custom_css') == "";
	if (!$remove_site_custom_css && get_option('site_custom_css') != "" && get_option('pro_version_enabled')) {
		echo '<style id="simple-banner-site-custom-css" type="text/css">'. get_option('site_custom_css') . '</style>';
	} else {
		// put a dummy element to see if css is being bundled
		echo '<style id="simple-banner-site-custom-css-dummy" type="text/css"></style>';
	}

	$remove_site_custom_js = ($banner_is_disabled || $closed_cookie) && get_option('keep_site_custom_js') == "";
	if (!$remove_site_custom_js && get_option('site_custom_js') != "" && get_option('pro_version_enabled')) {
		echo '<script id="simple-banner-site-custom-js" type="text/javascript">'. get_option('site_custom_js') . '</script>';
	} else {
		// put a dummy element to see if scripts are being bundled
		echo '<script id="simple-banner-site-custom-js-dummy" type="text/javascript"></script>';
	}
}

add_action('admin_menu', 'simple_banner_menu');
function simple_banner_menu() {
	$manage_simple_banner = 'manage_simple_banner';
	$manage_options = 'manage_options';
	// Add admin access
	$admin = get_role( 'administrator' );
	if ($admin) {
		$admin->add_cap( $manage_simple_banner );
	}

	$permissions_array = get_option('permissions_array');

	// Add permissions for other roles
	foreach (get_editable_roles() as $role_name => $role_info) {
		if ( $role_name !== 'administrator') {
			if (in_array($role_name, explode(",", $permissions_array))) {
				$add_role = get_role( $role_name );
				$add_role->add_cap( $manage_simple_banner );
				$add_role->add_cap( $manage_options );
			} else {
				$remove_role = get_role( $role_name );
				// only remove capabilities if they were previously added
				if ($remove_role->has_cap( $manage_simple_banner )){
					$remove_role->remove_cap( $manage_simple_banner );
					$remove_role->remove_cap( $manage_options );
				}
			}
		}
	}

	add_menu_page('Simple Banner Settings', 'Simple Banner', $manage_simple_banner, 'simple-banner-settings', 'simple_banner_settings_page', 'dashicons-admin-generic');
}


// TODO: Add this check after looking into `unfiltered_html` option
// function get_sanitized_simple_banner_text($string) {
// 	$string_value = $string;
// 	if ( current_user_can( 'unfiltered_html' ) ) {
// 		returnwp_kses_post( $string );
// 	} else {
// 		return wp_kses_data( $string );
// 	}
// }

//script input sanitization function
function theme_slug_sanitize_js_code($input){
    return base64_encode($input);
}
  
  
//output escape function    
function theme_slug_escape_js_output($input){
    return esc_textarea( base64_decode($input) );
}

add_action( 'admin_init', 'simple_banner_settings' );
function simple_banner_settings() {
	register_setting( 'simple-banner-settings-group', 'hide_simple_banner',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_font_size',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_color',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_text_color',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_link_color',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_close_color',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_text',
		array(
	    	'sanitize_callback' => 'wp_kses_post'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_custom_css',
		array(
	    	'sanitize_callback' => 'wp_strip_all_tags'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_scrolling_custom_css',
		array(
	    	'sanitize_callback' => 'wp_strip_all_tags'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_text_custom_css',
		array(
	    	'sanitize_callback' => 'wp_strip_all_tags'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_button_css',
		array(
	    	'sanitize_callback' => 'wp_strip_all_tags'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_position',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'header_margin',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'header_padding',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'pro_version_activation_code',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'pro_version_enabled',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'disabled_on_posts',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'disabled_pages_array',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'permissions_array',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'site_custom_css',
		array(
	    	'sanitize_callback' => 'wp_strip_all_tags'
		)
    );
	register_setting( 'simple-banner-settings-group', 'keep_site_custom_css',
		array(
	    	'sanitize_callback' => 'wp_strip_all_tags'
		)
    );
	register_setting( 'simple-banner-settings-group', 'site_custom_js');
	register_setting( 'simple-banner-settings-group', 'keep_site_custom_js',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'debug_mode',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'wp_body_open_enabled',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'close_button_enabled',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'close_button_expiration',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
}

function simple_banner_settings_page() {
	?>
	<?php
		if (esc_attr( get_option('pro_version_activation_code') ) == "SBPROv1-14315") {
			update_option('pro_version_enabled', true);
		} else {
			update_option('pro_version_enabled', false);
		}
	?>

	<style type="text/css" id="settings_stylesheet">
		.simple-banner-settings-form th {width: 30%;}
	</style>

	<div class="wrap">
		<div style="display: flex;justify-content: space-between;">
			<h2>Simple Banner Settings</h2>
			<a class="button button-primary button-hero" style="font-weight: 700;" href="https://www.paypal.me/rpetersenDev" target="_blank">DONATE</a>
		</div>


		<p>Use Hex color values for the color fields.</p>
		<p>Links in the banner text must be typed in with HTML <code>&lt;a&gt;</code> tags.
		<br />e.g. <code>This is a &lt;a href=&#34;http:&#47;&#47;www.wordpress.com&#34;&gt;Link to Wordpress&lt;&#47;a&gt;</code>.</p>

		<!-- Preview Banner -->
		<div id="preview_banner_outer_container" style="min-height: 40px;">
			<div id="preview_banner_inner_container">
				<div id="preview_banner" class="simple-banner" style="width: 100%;text-align: center;">
					<div id="preview_banner_text" class="simple-banner-text" style="font-weight: 700;padding: 10px;">
						<span>This is what your banner will look like with a <a href="/">link</a>.</span>
					</div>
				</div>
			</div>
		</div>
		<br>
		<span><b>*Note: Font and text styles subject to change based on chosen theme CSS.</b></span>

		<!-- Settings Form -->
		<form class="simple-banner-settings-form" method="post" action="options.php">
			<?php settings_fields( 'simple-banner-settings-group' ); ?>
			<?php do_settings_sections( 'simple-banner-settings-group' ); ?>

			<table class="form-table">
				<!-- Hide -->
				<tr valign="top">
					<th scope="row">
						Hide Simple Banner
						<br><span style="font-weight:400;">This can hide the simple banner, essentially applies <code>display: none;</code> to the banner</span>
					</th>
					<td style="vertical-align:top;">
						<!-- -->
						<input type="radio" id="yes" name="hide_simple_banner" value="yes" <?php echo ((get_option('hide_simple_banner') == 'yes') ? 'checked' : '' ); ?>>
						<label for="yes">yes</label>
						<!-- -->
						<input type="radio" id="no" name="hide_simple_banner" value="no" <?php echo ((get_option('hide_simple_banner') == 'yes') ? '' : 'checked' ); ?>>
						<label for="no">no</label>
						<!-- -->
					</td>
				</tr>
				<!-- Close Button -->
				<tr valign="top">
					<th scope="row">
						Close button enabled
						<br>
						<span style="font-weight:400;">
							This feature uses strictly necessary cookies which do not require consent from users per <a href="https://gdpr.eu/cookies/">GDPR</a> guidelines
						</span>
					</th>
					<td>
						<?php
							$checked = get_option('close_button_enabled') ? 'checked ' : '';
							echo '<input type="checkbox" id="close_button_enabled" '. $checked . ' name="close_button_enabled" />';
						?>
					</td>
				</tr>
				<!-- Close Button -->
				<tr valign="top">
					<th scope="row">
						Close button expiration
						<br>
						<span style="font-weight:400;">
							The amount of time until the close button action will expire. Enter the amount of days until the close button action will expire, e.g. <code>14</code> or the exact day and time, e.g. <code>Mon, 21 Feb 2022 15:53:22 GMT</code>. Default is 0.
						</span>
					</th>
					<td>
						<input id="close_button_expiration" name="close_button_expiration"
										value="<?php echo esc_attr( get_option('close_button_expiration') ); ?>" />
					</td>
				</tr>
				<!-- Font Size -->
				<tr valign="top">
					<th scope="row">
						Simple Banner Font Size
						<br><span style="font-weight:400;">Leaving this blank sets the default to your theme CSS value</span>
					</th>
					<td style="vertical-align:top;">
						<input type="text" id="simple_banner_font_size" name="simple_banner_font_size" placeholder="font-size"
										value="<?php echo esc_attr( get_option('simple_banner_font_size') ); ?>" />
						<span>e.g. 16px</span>
					</td>
				</tr>
				<!-- Background Color -->
				<tr valign="top">
					<th scope="row">
						Simple Banner Background Color
						<br><span style="font-weight:400;">Leaving this blank sets the color to the default value #024985</span>
					</th>
					<td style="vertical-align:top;">
						<input type="text" id="simple_banner_color" name="simple_banner_color" placeholder="Hex value"
										value="<?php echo esc_attr( get_option('simple_banner_color') ); ?>" />
						<input style="height: 30px;width: 100px;" type="color" id="simple_banner_color_show"
										value="<?php echo ((get_option('simple_banner_color') == '') ? '#024985' : esc_attr( get_option('simple_banner_color') )); ?>">
					</td>
				</tr>
				<!-- Text Color -->
				<tr valign="top">
					<th scope="row">
						Simple Banner Text Color
						<br><span style="font-weight:400;">Leaving this blank sets the color to the default value white</span>
					</th>
					<td style="vertical-align:top;">
						<input type="text" id="simple_banner_text_color" name="simple_banner_text_color" placeholder="Hex value"
										value="<?php echo esc_attr( get_option('simple_banner_text_color') ); ?>" />
						<input style="height: 30px;width: 100px;" type="color" id="simple_banner_text_color_show"
										value="<?php echo ((get_option('simple_banner_text_color') == '') ? '#ffffff' : esc_attr( get_option('simple_banner_text_color') )); ?>">
					</td>
				</tr>
				<!-- Link Color-->
				<tr valign="top">
					<th scope="row">
						Simple Banner Link Color
						<br><span style="font-weight:400;">Leaving this blank sets the color to the default value #f16521</span>
					</th>
					<td style="vertical-align:top;">
						<input type="text" id="simple_banner_link_color" name="simple_banner_link_color" placeholder="Hex value"
										value="<?php echo esc_attr( get_option('simple_banner_link_color') ); ?>" />
						<input style="height: 30px;width: 100px;" type="color" id="simple_banner_link_color_show"
										value="<?php echo ((get_option('simple_banner_link_color') == '') ? '#f16521' : esc_attr( get_option('simple_banner_link_color') )); ?>">
					</td>
				</tr>
				<!-- Close Color-->
				<tr valign="top">
					<th scope="row">
						Simple Banner Close Button Color
						<br><span style="font-weight:400;">Leaving this blank sets the color to the default value black</span>
					</th>
					<td style="vertical-align:top;">
						<input type="text" id="simple_banner_close_color" name="simple_banner_close_color" placeholder="Hex value"
										value="<?php echo esc_attr( get_option('simple_banner_close_color') ); ?>" />
						<input style="height: 30px;width: 100px;" type="color" id="simple_banner_close_color_show"
										value="<?php echo ((get_option('simple_banner_close_color') == '') ? 'black' : esc_attr( get_option('simple_banner_close_color') )); ?>">
					</td>
				</tr>
				<!-- Text Contents -->
				<tr valign="top">
					<th scope="row">
						Simple Banner Text
						<br><span style="font-weight:400;">Leaving this blank removes the banner</span>
					</th>
						<td>
							<textarea id="simple_banner_text" class="large-text code" style="height: 150px;width: 97%;" name="simple_banner_text"><?php echo get_option('simple_banner_text'); ?></textarea>
						</td>
				</tr>
				<!-- Custom CSS -->
				<tr valign="top">
					<th scope="row">
						Simple Banner Custom CSS
					</th>
					<td>
						<span style="font-weight:400;">CSS will be applied directly to the <code>simple-banner</code> class, the <code>simple-banner-scrolling</code> class for scrolling styles, the <code>simple-banner-text</code> class for text specific styles, and the <code>simple-banner-button</code> class for close button specific styles.</span>
						<span style="font-weight:400;color:red;">Be very careful, bad CSS can break the banner.</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="font-weight:400;">
						<div>.simple-banner {</div>
						<textarea id="simple_banner_custom_css" class="code" style="height: 150px;width: 90%;" name="simple_banner_custom_css"><?php echo get_option('simple_banner_custom_css'); ?></textarea>
						<div>}</div>
					</th>
					<td>
						<div style="display:flex">
							<div style="flex-grow:1;">
								<div>.simple-banner-scrolling {</div>
								<textarea id="simple_banner_scrolling_custom_css" class="code" style="height: 150px;width: 90%;" name="simple_banner_scrolling_custom_css"><?php echo get_option('simple_banner_scrolling_custom_css'); ?></textarea>
								<div>}</div>
							</div>
							<div style="flex-grow:1;">
								<div>.simple-banner-text {</div>
								<textarea id="simple_banner_text_custom_css" class="code" style="height: 150px;width: 90%;" name="simple_banner_text_custom_css"><?php echo get_option('simple_banner_text_custom_css'); ?></textarea>
								<div>}</div>
							</div>
							<div style="flex-grow:1;">
								<div>.simple-banner-button {</div>
								<textarea id="simple_banner_button_css" class="code" style="height: 150px;width: 90%;" name="simple_banner_button_css"><?php echo get_option('simple_banner_button_css'); ?></textarea>
								<div>}</div>
							</div>
						</div>
					</td>
				</tr>
				<!-- Position -->
				<tr valign="top">
					<th scope="row">
						Simple Banner Position
						<br><span style="font-weight:400;">Change the <code>position</code> value of your banner. More information <a target="_blank" href="https://www.w3schools.com/cssref/pr_class_position.asp">here</a></span>
					</th>
					<td style="vertical-align:top;">
						<!-- -->
						<input type="radio" id="footer" name="simple_banner_position" value="footer" <?php echo ((get_option('simple_banner_position') == 'footer') ? 'checked' : '' ); ?>>
						<label for="footer"><strong>footer:</strong> <span>The banner is fixed on the bottom of the window. Updates the banner position with the following css attributes <code>position: fixed;bottom: 0;</code></span></label><br>
						<!-- -->
						<input type="radio" id="static" name="simple_banner_position" value="static" <?php echo ((get_option('simple_banner_position') == 'static') ? 'checked' : '' ); ?>>
						<label for="static"><strong>static:</strong> <span>Default value. Elements render in order, as they appear in the document flow</span></label><br>
						<!-- -->
						<input type="radio" id="absolute" name="simple_banner_position" value="absolute" <?php echo ((get_option('simple_banner_position') == 'absolute') ? 'checked' : '' ); ?>>
						<label for="absolute"><strong>absolute:</strong> <span>The element is positioned relative to its first positioned (not static) ancestor element</span></label><br>
						<!-- -->
						<input type="radio" id="fixed" name="simple_banner_position" value="fixed" <?php echo ((get_option('simple_banner_position') == 'fixed') ? 'checked' : '' ); ?>>
						<label for="fixed"><strong>fixed:</strong> <span>The element is positioned relative to the browser window</span></label><br>
						<!-- -->
						<input type="radio" id="relative" name="simple_banner_position" value="relative" <?php echo ((get_option('simple_banner_position') == 'relative') ? 'checked' : '' ); ?>>
						<label for="relative"><strong>relative:</strong> <span>The element is positioned relative to its normal position, so <code>left:20px</code> adds 20 pixels to the element's LEFT position</span></label><br>
						<!-- -->
						<input type="radio" id="sticky" name="simple_banner_position" value="sticky" <?php echo ((get_option('simple_banner_position') == 'sticky') ? 'checked' : '' ); ?>>
						<label for="sticky"><strong>sticky:</strong> <span>The element is positioned based on the user's scroll position</span></label><br>
						<div style="padding-left: 10px;">
							A sticky element toggles between relative and fixed, depending on the scroll position.
							It is positioned relative until a given offset position is met in the viewport - then it "sticks" in place (like position:fixed).<br>
							<strong>Note:</strong> Not supported in IE/Edge 15 or earlier. Supported in Safari from version 6.1 with a -webkit- prefix.</div>
						<!-- -->
						<input type="radio" id="initial" name="simple_banner_position" value="initial" <?php echo ((get_option('simple_banner_position') == 'initial') ? 'checked' : '' ); ?>>
						<label for="initial"><strong>initial:</strong> <span>Sets this property to its default value.</span></label><br>
						<!-- -->
						<input type="radio" id="inherit" name="simple_banner_position" value="inherit" <?php echo ((get_option('simple_banner_position') == 'inherit') ? 'checked' : '' ); ?>>
						<label for="inherit"><strong>inherit:</strong> <span>Inherits this property from its parent element.</span></label><br>
					</td>
				</tr>
				<!-- Header Margin -->
				<tr valign="top">
					<th scope="row">
						Header Top Margin
						<br><span style="font-weight:400;">Apply margin to the top of your theme header</span>
						<br><span style="font-weight:400;color:red;">Will be disabled if banner is hidden, disabled, or closed</span>
					</th>
					<td style="vertical-align:top;">
						<input type="text" id="header_margin" name="header_margin" placeholder="margin-top"
										value="<?php echo esc_attr( get_option('header_margin') ); ?>" />
						<span>e.g. 40px</span>
					</td>
				</tr>
				<!-- Header Padding -->
				<tr valign="top">
					<th scope="row">
						Header Top Padding
						<br><span style="font-weight:400;">Apply padding to the top of your theme header</span>
						<br><span style="font-weight:400;color:red;">Will be disabled if banner is hidden, disabled, or closed</span>
					</th>
					<td style="vertical-align:top;">
						<input type="text" id="header_padding" name="header_padding" placeholder="padding-top"
										value="<?php echo esc_attr( get_option('header_padding') ); ?>" />
						<span>e.g. 40px</span>
					</td>
				</tr>
				<!-- wp_body_open -->
				<?php if ( function_exists( 'wp_body_open' ) ): ?>
					<tr valign="top">
						<th scope="row">
							wp_body_open enabled
							<br>
								<span style="font-weight:400;">
									If enabled, will use the <a href="https://developer.wordpress.org/reference/functions/wp_body_open/">wp_body_open</a> action
									to insert the banner to your site. This will allow the banner to be translated into other languages if you are using another 
									translation plugin.
								</span>
						</th>
						<td>
							<?php
								$checked = get_option('wp_body_open_enabled') ? 'checked ' : '';
								echo '<input type="checkbox" id="wp_body_open_enabled" '. $checked . ' name="wp_body_open_enabled" />';
							?>
						</td>
					</tr>
				<?php endif; ?>
			</table>

			<div style="padding: 10px;
						margin: 10px 0;
						border: 2px solid red;
						border-radius: 10px;
						background-color: white;
						color: red;
						font-size: medium;
						font-weight: bold;
						text-align: center;">
				Always make sure you test your banner in mobile views, theme headers often change up their css for mobile.
			</div>

			<!-- Pro Features -->
			<div style="padding: 0 10px;border: 1px solid #24282e;border-radius: 10px;background-color: #fafafa;">

				<h2>Pro Features
					<?php
						if (!get_option('pro_version_enabled')) {
							echo '<a class="button-primary" href="https://simple-banner.square.site/" target="_blank">Purchase Pro Version</a>';
						}
					?>
				</h2>

				<table class="form-table">
					<!-- Activation Code -->
					<tr valign="top" style="<?php if (get_option('pro_version_enabled')) { echo 'display: none;'; } ?>">
						<th scope="row">
							Activation Code
						</th>
						<td>
							<input type="text" style="border: 2px solid gold;border-radius: 5px;" id="pro_version_activation_code" name="pro_version_activation_code" value="<?php echo get_option('pro_version_activation_code'); ?>" />
						</td>
					</tr>
					<!-- Permissions -->
					<?php if ( in_array( 'administrator', (array) wp_get_current_user()->roles ) ): ?>
						<tr valign="top">
							<th scope="row">
								Permissions
								<br><span style="font-weight:400;">Allow roles to edit Simple Banner.</span>
							</th>
							<td>
								<div id="simple_banner_pro_permissions">
									<?php
										$roles = get_editable_roles();
										$disabled = !get_option('pro_version_enabled');
										$permissions_array = get_option('permissions_array');
										foreach (get_editable_roles() as $role_name => $role_info) {
											if ($role_name == 'administrator') {
												continue;
											}
											$allowed = current_user_can( 'manage_simple_banners' );
											$checkbox = '<input type="checkbox"';
											$checkbox .= $disabled ? 'disabled ' : '';
											$checkbox .= (!$disabled && in_array($role_name, explode(",", $permissions_array))) ? 'checked ' : '';
											$checkbox .= 'value="' . $role_name . '">';
											$checkbox .= $role_name;
											$checkbox .= '</input><br>';
											echo $checkbox;
										}
									?>
									</dl>
								</div>
							</td>
						</tr>
					<?php endif; ?>
					<?php
						if (get_option('pro_version_enabled')) {
							echo '<input type="text" hidden id="permissions_array" name="permissions_array" value="'. get_option('permissions_array') . '" />';
						}
					?>
					<!-- Disabled on Psts -->
					<tr valign="top">
						<th scope="row">
							Disabled on Posts
							<br>
							<span style="font-weight:400;">
								<span style="color:red;">*EXPERIMENTAL FEATURE*</span><br>
								Disable Simple Banner on all posts.
							</span>
						</th>
						<td style="padding-top:0;">
							<?php
								if (get_option('pro_version_enabled')) {
									$checked = get_option('disabled_on_posts') ? 'checked ' : '';
									echo '<input type="checkbox" id="disabled_on_posts" '. $checked . ' name="disabled_on_posts" />';
								} else {
									echo '<input type="checkbox" disabled />';
								}
							?>
						</td>
					</tr>
					<!-- Disabled Pages -->
					<tr valign="top">
						<th scope="row">
							Disabled Pages
							<br><span style="font-weight:400;">Disable Simple Banner on the following pages.</span>
						</th>
						<td>
							<div id="simple_banner_pro_disabled_pages">
								<?php
									$disabled = !get_option('pro_version_enabled');
									$disabled_pages_array = array_filter(explode(',', get_option('disabled_pages_array')));
									$frontpage_id = get_option( 'page_on_front' ); // page_on_front returns 0 if value hasn't been set
									if ($frontpage_id == 0) {
										$frontpage_id = 1;
									}
									$parent_checkbox = '<input type="checkbox" ';
									$parent_checkbox .= $disabled ? 'disabled ' : '';
									$parent_checkbox .= (!$disabled && in_array($frontpage_id, $disabled_pages_array)) ? 'checked ' : '';
									$parent_checkbox .= 'value="' . $frontpage_id . '">';
									$parent_checkbox .= get_option( 'blogname' ) . ' | ' . get_site_url() . ' ';
									$parent_checkbox .= '</input><br>';
									echo $parent_checkbox;

									$pages = get_pages(array(
										'exclude' => array($frontpage_id) // exclude frontpage_id
									));
									foreach ( $pages as $page ) {
										$checkbox = '<input type="checkbox"';
										$checkbox .= $disabled ? 'disabled ' : '';
										$checkbox .= (!$disabled && in_array($page->ID, $disabled_pages_array)) ? 'checked ' : '';
										$checkbox .= 'value="' . $page->ID . '">';
										$checkbox .= $page->post_title . ' | ' . get_page_link( $page->ID ) . ' ';
										$checkbox .= '</input><br>';
										echo $checkbox;
									}
								?>
							</div>
							<?php
								if (get_option('pro_version_enabled')) {
									echo '<input type="text" hidden id="disabled_pages_array" name="disabled_pages_array" value="'. get_option('disabled_pages_array') . '" />';
								}
							?>
						</td>
					</tr>
					<!-- Website Custom CSS -->
					<tr valign="top">
						<th scope="row">
							Website Custom CSS
							<br><span style="font-weight:400;">CSS will be applied to the entire website</span>
						</th>
						<td>
							<?php
								if (get_option('pro_version_enabled')) {
									echo '<textarea id="site_custom_css" style="height: 150px;width: 75%;" name="site_custom_css">'. get_option('site_custom_css') . '</textarea>';
								} else {
									echo '<textarea style="height: 150px;width: 75%;" disabled></textarea>';
								}
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="padding-top:0;">
							Keep CSS if banner is hidden, disabled, or closed?
						</th>
						<td style="padding-top:0;">
							<?php
								if (get_option('pro_version_enabled')) {
									$checked = get_option('keep_site_custom_css') ? 'checked ' : '';
									echo '<input type="checkbox" id="keep_site_custom_css" '. $checked . ' name="keep_site_custom_css" />';
								} else {
									echo '<input type="checkbox" disabled />';
								}
							?>
						</td>
					</tr>
					<!-- Website Custom JS -->
					<tr valign="top">
						<th scope="row">
							Website Custom JS
							<br><span style="font-weight:400;">JavaScript will be applied to the entire website</span>
						</th>
						<td>
							<?php
								if (get_option('pro_version_enabled')) {
									echo '<textarea id="site_custom_js" style="height: 150px;width: 75%;" name="site_custom_js">'. get_option('site_custom_js') . '</textarea>';
								} else {
									echo '<textarea style="height: 150px;width: 75%;" disabled></textarea>';
								}
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="padding-top:0;">
							Keep JS if banner is hidden, disabled, or closed?
						</th>
						<td style="padding-top:0;">
							<?php
								if (get_option('pro_version_enabled')) {
									$checked = get_option('keep_site_custom_js') ? 'checked ' : '';
									echo '<input type="checkbox" id="keep_site_custom_js" '. $checked . ' name="keep_site_custom_js" />';
								} else {
									echo '<input type="checkbox" disabled />';
								}
							?>
						</td>
					</tr>
					<!-- Debug Mode -->
					<tr valign="top">
						<th scope="row">
							Debug Mode
							<br><span style="font-weight:400;">If enabled, will log all variables in the console of your browser</span>
						</th>
						<td>
							<?php
								if (get_option('pro_version_enabled')) {
									$checked = get_option('debug_mode') ? 'checked ' : '';
									echo '<input type="checkbox" id="debug_mode" '. $checked . ' name="debug_mode" />';
								} else {
									echo '<input type="checkbox" disabled />';
								}
							?>
						</td>
					</tr>
				</table>
			</div>

			<!-- Save Changes Button -->
			<?php submit_button(); ?>
		</form>
	</div>

	<!-- Script to apply styles to Preview Banner -->
	<script type="text/javascript">
		// Simple Banner Default Stylesheet
		var simple_banner_css = document.createElement('link');
		simple_banner_css.id = 'simple-banner-stylesheet';
		simple_banner_css.rel = 'stylesheet';
		simple_banner_css.href = "<?php echo plugin_dir_url( __FILE__ ) .'simple-banner.css' ?>";
		document.getElementsByTagName('head')[0].appendChild(simple_banner_css);

		// Fixed Preview Banner on scroll
		window.onscroll = function() {fixedBanner()};
        function fixedBanner() {			
			var elementContainer = document.getElementById('preview_banner_outer_container');
			var elementTarget = document.getElementById('preview_banner_inner_container');
			if (window.scrollY > (elementContainer.offsetTop)) {
				elementTarget.style.position = 'fixed';
				elementTarget.style.width = '83.671%';
				elementTarget.style.top = '40px';
			} else {
				elementTarget.style.position = 'relative';
				elementTarget.style.width = '100%';
				elementTarget.style.top = '0';
			}
        }

		var style_font_size = document.createElement('style');
		var style_background_color = document.createElement('style');
		var style_link_color = document.createElement('style');
		var style_text_color = document.createElement('style');
		var style_close_color = document.createElement('style');
		var style_custom_css = document.createElement('style');
		var style_custom_text_css = document.createElement('style');
		var style_custom_button_css = document.createElement('style');

		// Banner Text
		var hrefRegex = /href\=[\'\"](?!http|https)(.*?)[\'\"]/gsi;
		var scriptStyleRegex = /<(script|style)[^>]*?>.*?<\/(script|style)>/gsi;
		function stripBannerText(string) {
			let strippedString = string;
			while (strippedString.match(scriptStyleRegex)) { 
			    strippedString = strippedString.replace(scriptStyleRegex, '')
			};
			return strippedString.replace(hrefRegex, "href=\"https://$1\"");
		}
		document.getElementById('preview_banner_text').innerHTML = document.getElementById('simple_banner_text').value != "" ? 
						'<span>'+stripBannerText(document.getElementById('simple_banner_text').value)+'</span>' : 
						'<span>This is what your banner will look like with a <a href="/">link</a>.</span>';
		document.getElementById('simple_banner_text').onchange=function(e){
			document.getElementById('preview_banner_text').innerHTML = e.target.value != "" ? '<span>'+stripBannerText(e.target.value)+'</span>' : '<span>This is what your banner will look like with a <a href="/">link</a>.</span>';
		};

		// Close Button
		var closeButton = '<button id="simple-banner-close-button" class="simple-banner-button">✕</button>';
		var closeButtonChecked = document.getElementById('close_button_enabled').checked;
		var closeButtonInitialValue = closeButtonChecked ? closeButton : '';
		document.getElementById('preview_banner').innerHTML = document.getElementById('preview_banner').innerHTML + closeButtonInitialValue;
		document.getElementById('close_button_enabled').onchange=function(e){
			var str = document.getElementById('preview_banner').innerHTML; 
			if (e.target.checked) {
				document.getElementById('preview_banner').innerHTML = str + closeButton;
			} else {
				var res = str.replace(closeButton, '');
				document.getElementById('preview_banner').innerHTML = res;
			}
		};

		// Font Size
		style_font_size.type = 'text/css';
		style_font_size.id = 'preview_banner_font_size'
		style_font_size.appendChild(document.createTextNode('.simple-banner .simple-banner-text{font-size:' + (document.getElementById('simple_banner_font_size').value || '1em') + '}'));
		document.getElementsByTagName('head')[0].appendChild(style_font_size);

		document.getElementById('simple_banner_font_size').onchange=function(e){
			var child = document.getElementById('preview_banner_font_size');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_font_size';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner .simple-banner-text{font-size:' + (document.getElementById('simple_banner_font_size').value || '1em') + '}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};

		// Background Color
		style_background_color.type = 'text/css';
		style_background_color.id = 'preview_banner_background_color'
		style_background_color.appendChild(document.createTextNode('.simple-banner{background:' + (document.getElementById('simple_banner_color').value || '#024985') + '}'));
		document.getElementsByTagName('head')[0].appendChild(style_background_color);

		document.getElementById('simple_banner_color').onchange=function(e){
			document.getElementById('simple_banner_color_show').value = e.target.value || '#024985';
			var child = document.getElementById('preview_banner_background_color');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_background_color';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner{background:' + (document.getElementById('simple_banner_color').value || '#024985') + '}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};
		document.getElementById('simple_banner_color_show').onchange=function(e){
			document.getElementById('simple_banner_color').value = e.target.value;
			document.getElementById('simple_banner_color').dispatchEvent(new Event('change'));
		};

		// Text Color
		style_text_color.type = 'text/css';
		style_text_color.id = 'preview_banner_text_color'
		style_text_color.appendChild(document.createTextNode('.simple-banner .simple-banner-text{color:' + (document.getElementById('simple_banner_text_color').value || '#ffffff') + '}'));
		document.getElementsByTagName('head')[0].appendChild(style_text_color);

		document.getElementById('simple_banner_text_color').onchange=function(e){
			document.getElementById('simple_banner_text_color_show').value = e.target.value || '#ffffff';
			var child = document.getElementById('preview_banner_text_color');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_text_color';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner .simple-banner-text{color:' + (document.getElementById('simple_banner_text_color').value || '#ffffff') + '}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};
		document.getElementById('simple_banner_text_color_show').onchange=function(e){
			document.getElementById('simple_banner_text_color').value = e.target.value;
			document.getElementById('simple_banner_text_color').dispatchEvent(new Event('change'));
		};

		// Link Color
		style_link_color.type = 'text/css';
		style_link_color.id = 'preview_banner_link_color'
		style_link_color.appendChild(document.createTextNode('.simple-banner .simple-banner-text a{color:' + (document.getElementById('simple_banner_link_color').value || '#f16521') + '}'));
		document.getElementsByTagName('head')[0].appendChild(style_link_color);

		document.getElementById('simple_banner_link_color').onchange=function(e){
			document.getElementById('simple_banner_link_color_show').value = e.target.value || '#f16521';
			var child = document.getElementById('preview_banner_link_color');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_link_color';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner .simple-banner-text a{color:' + (document.getElementById('simple_banner_link_color').value || '#f16521') + '}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};
		document.getElementById('simple_banner_link_color_show').onchange=function(e){
			document.getElementById('simple_banner_link_color').value = e.target.value;
			document.getElementById('simple_banner_link_color').dispatchEvent(new Event('change'));
		};

		// Close Color
		style_close_color.type = 'text/css';
		style_close_color.id = 'preview_banner_close_color'
		style_close_color.appendChild(document.createTextNode('.simple-banner .simple-banner-button{color:' + (document.getElementById('simple_banner_close_color').value || 'black') + '}'));
		document.getElementsByTagName('head')[0].appendChild(style_close_color);

		document.getElementById('simple_banner_close_color').onchange=function(e){
			document.getElementById('simple_banner_close_color_show').value = e.target.value || 'black';
			var child = document.getElementById('preview_banner_close_color');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_close_color';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner .simple-banner-button{color:' + (document.getElementById('simple_banner_close_color').value || 'black') + '}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};
		document.getElementById('simple_banner_close_color_show').onchange=function(e){
			document.getElementById('simple_banner_close_color').value = e.target.value;
			document.getElementById('simple_banner_close_color').dispatchEvent(new Event('change'));
		};

		// Custom CSS
		style_custom_css.type = 'text/css';
		style_custom_css.id = 'preview_banner_custom_stylesheet'
		style_custom_css.appendChild(document.createTextNode('.simple-banner{'+document.getElementById('simple_banner_custom_css').value+'}'));
		document.getElementsByTagName('head')[0].appendChild(style_custom_css);

		document.getElementById('simple_banner_custom_css').onchange=function(){
			var child = document.getElementById('preview_banner_custom_stylesheet');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_custom_stylesheet';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner{'+document.getElementById('simple_banner_custom_css').value+'}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};

		// Custom Text CSS
		style_custom_text_css.type = 'text/css';
		style_custom_text_css.id = 'preview_banner_custom_text_stylesheet'
		style_custom_text_css.appendChild(document.createTextNode('.simple-banner .simple-banner-text{'+document.getElementById('simple_banner_text_custom_css').value+'}'));
		document.getElementsByTagName('head')[0].appendChild(style_custom_text_css);

		document.getElementById('simple_banner_text_custom_css').onchange=function(){
			var child = document.getElementById('preview_banner_custom_text_stylesheet');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_custom_text_stylesheet';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner .simple-banner-text{'+document.getElementById('simple_banner_text_custom_css').value+'}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};

		// Custom Button CSS
		style_custom_button_css.type = 'text/css';
		style_custom_button_css.id = 'preview_banner_custom_button_stylesheet'
		style_custom_button_css.appendChild(document.createTextNode('.simple-banner .simple-banner-button{'+document.getElementById('simple_banner_button_css').value+'}'));
		document.getElementsByTagName('head')[0].appendChild(style_custom_button_css);

		document.getElementById('simple_banner_button_css').onchange=function(){
			var child = document.getElementById('preview_banner_custom_button_stylesheet');
			if (child){child.innerText = "";child.id='';}

			var style_dynamic = document.createElement('style');
			style_dynamic.type = 'text/css';
			style_dynamic.id = 'preview_banner_custom_button_stylesheet';
			style_dynamic.appendChild(
				document.createTextNode(
					'.simple-banner .simple-banner-button{'+document.getElementById('simple_banner_button_css').value+'}'
				)
			);
			document.getElementsByTagName('head')[0].appendChild(style_dynamic);
		};

		// Permissions
		document.getElementById('simple_banner_pro_permissions').onclick=function(e){
			let permissionsArray = [];
			Array.from(document.getElementById('simple_banner_pro_permissions').getElementsByTagName('input')).forEach(function(e) {
				if (e.checked) {
					permissionsArray.push(e.value);
				}
			});
			document.getElementById('permissions_array').value = permissionsArray;
		};

		// Disabled Pages
		document.getElementById('simple_banner_pro_disabled_pages').onclick=function(e){
			let disabledPagesArray = [];
			Array.from(document.getElementById('simple_banner_pro_disabled_pages').getElementsByTagName('input')).forEach(function(e) {
				if (e.checked) {
					disabledPagesArray.push(e.value);
				}
			});
			document.getElementById('disabled_pages_array').value = disabledPagesArray;
		};

		// remove banner text newlines on submit
		document.getElementById('submit').onclick=function(e){
			document.getElementById('simple_banner_text').value = document.getElementById('simple_banner_text').value.replace(/\n/g, "");
		};
	</script>
	<?php
}
?>
