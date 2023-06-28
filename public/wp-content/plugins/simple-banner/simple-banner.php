<?php
/**
 * Plugin Name: Simple Banner
 * Plugin URI: https://github.com/rpetersen29/simple-banner
 * Description: Display a simple banner at the top or bottom of your website.
 * Version: 2.15.2
 * Author: Ryan Petersen
 * Author URI: http://rpetersen29.github.io/
 * License: GPL2
 *
 * @package Simple Banner
 * @version 2.15.2
 * @author Ryan Petersen <rpetersen.dev@gmail.com>
 */
define ('SB_VERSION', '2.15.2');

register_activation_hook( __FILE__, 'simple_banner_activate' );
function simple_banner_activate() {
	add_action('admin_menu', 'simple_banner_menu');
}

// Disabled Pages/Posts functions
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
function get_is_removed_after_date() {
	$remove_after_date = get_option('simple_banner_remove_after_date');
	
	if (!$remove_after_date) return false;

	$curr_date = new DateTime('now');
	$cancelled_date = new DateTime($remove_after_date);

	// Compare the dates
	if ($curr_date > $cancelled_date) {
		return true;
	}
	return false;
}
function get_disabled_on_current_page() {
	$disabled_on_current_page = (!empty(get_disabled_pages_array()) && in_array(get_the_ID(), get_disabled_pages_array()))
								|| (get_disabled_on_posts() && get_is_current_page_a_post()) || get_is_removed_after_date();
	return $disabled_on_current_page;
}


add_action( 'wp_enqueue_scripts', 'simple_banner' );
function simple_banner() {
    // Enqueue the style
	wp_register_style('simple-banner-style',  plugin_dir_url( __FILE__ ) .'simple-banner.css', '', SB_VERSION);
    wp_enqueue_style('simple-banner-style');
	// Set Script parameters
	$disabled_on_current_page = get_disabled_on_current_page();
	$script_params = array(
		// script specific parameters
		'version' => SB_VERSION,
		'hide_simple_banner' => get_option('hide_simple_banner'),
		'simple_banner_position' => get_option('simple_banner_position'),
		'header_margin' => get_option('header_margin'),
		'header_padding' => get_option('header_padding'),
		'simple_banner_z_index' => get_option('simple_banner_z_index'),
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
		'simple_banner_remove_after_date' => get_option('simple_banner_remove_after_date'),
		'simple_banner_insert_inside_element' => get_option('simple_banner_insert_inside_element'),
	);
	// Enqueue the script
    wp_register_script('simple-banner-script', plugin_dir_url( __FILE__ ) . 'simple-banner.js', array( 'jquery' ), SB_VERSION);
    wp_add_inline_script('simple-banner-script', 'const simpleBannerScriptParams = ' . wp_json_encode($script_params), 'before');
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

// Prevent CSS removal from optimizer plugins by putting a dummy item in the DOM
add_action( 'wp_footer', 'prevent_css_removal');
function prevent_css_removal(){
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

	if (get_option('simple_banner_z_index') != ""){
		echo '<style type="text/css">.simple-banner{z-index:' . get_option('simple_banner_z_index') . ';}</style>';
	} else {
		echo '<style type="text/css">.simple-banner{z-index: 99999;}</style>';
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
	register_setting( 'simple-banner-settings-group', 'simple_banner_z_index',
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
	register_setting( 'simple-banner-settings-group', 'simple_banner_pro_license_key',
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
	register_setting( 'simple-banner-settings-group', 'simple_banner_remove_after_date',
		array(
	    	'sanitize_callback' => 'wp_filter_nohtml_kses'
		)
    );
	register_setting( 'simple-banner-settings-group', 'simple_banner_insert_inside_element',
		array(
	    	'sanitize_callback' => 'wp_strip_all_tags'
		)
    );
}

function is_license_verified(){
	$license_key = esc_attr( get_option('simple_banner_pro_license_key') );
	// null check for license
	if (!$license_key){
		return false;
	}

	$is_pro_currently_enabled = esc_attr( get_option('pro_version_enabled') );

    $url = 'https://api.gumroad.com/v2/licenses/verify';

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	$data = array(
	    'product_id' => 'vg6aCpxipQHuI5yvYzSVEA==',
	    'license_key' => $license_key,
	    'increment_uses_count' => 'false',
	);

	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	
	// execute request and get response
	$result = curl_exec($ch);
	// Keeping for backwards compatibility
	// This function has no effect. Prior to PHP 8.0.0, this function was used to close the resource.
	curl_close($ch);

	// TODO: Figure out what to do with these
	// also get the error and response code
	// $errors = curl_error($ch);
	// $json_errors = json_decode($errors);
	// gumroad returns a 404 on invalid license code
	// e.g. {"success":false,"message":"That license does not exist for the provided product."}
	// $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	$json = json_decode($result);
	$success = $json->{'success'};

	// check if license is active
	// if error or unknown response return current value
	if ($success === true) {
		// now check if cancelled
		$subscription_cancelled_at = $json->{'purchase'}->{'subscription_cancelled_at'};
		if ($subscription_cancelled_at) {
			$curr_date = new DateTime('now');
			$cancelled_date = new DateTime($subscription_cancelled_at);

			// Compare the dates
			if ($curr_date > $cancelled_date) {
			    return false;
			}
		}
		return true;
	} else if ($success === false) {
		return false;
	}
	return $is_pro_currently_enabled;
}

function simple_banner_settings_page() {
	?>
	<?php
		if (esc_attr( get_option('pro_version_activation_code') ) == "SBPROv1-14315") {
			update_option('pro_version_enabled', true);
		} else {
			$is_verified = is_license_verified();
			update_option('pro_version_enabled', $is_verified);
		}
	?>

	<style type="text/css" id="settings_stylesheet">
		.simple-banner-settings-form th {width: 30%;}
		.simple-banner-settings-form th div {font-size: 13px;font-weight: 400;}
		.simple-banner-settings-form th div code {font-size: 12px;}
		#mobile-alert {
			padding: 10px;
			margin: 10px 0;
			border: 2px solid red;
			border-radius: 10px;
			background-color: white;
			color: red;
			font-size: medium;
			font-weight: bold;
			text-align: center;
		}
	</style>

	<div class="wrap">
		<div style="display: flex;justify-content: space-between;">
			<h1 style="font-weight: 700;">Simple Banner Settings</h1>
			<a class="button button-primary button-hero" style="font-weight: 700;" href="https://www.paypal.me/rpetersenDev" target="_blank">DONATE</a>
		</div>

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
		<span><b><i>Note: Font and text styles subject to change based on chosen theme CSS.</i></b></span>

		<!-- Settings Form -->
		<form class="simple-banner-settings-form" method="post" action="options.php">
			<?php settings_fields( 'simple-banner-settings-group' ); ?>
			<?php do_settings_sections( 'simple-banner-settings-group' ); ?>

			<?php include 'free_features.php';?>

			<div id="mobile-alert" style="">
				Always make sure you test your banner in mobile views, theme headers often change their css for mobile.
			</div>

			<?php include 'pro_features.php';?>

			<?php
				if (get_option('pro_version_enabled')) {
					echo '<input type="text" hidden id="disabled_pages_array" name="disabled_pages_array" value="'. get_option('disabled_pages_array') . '" />';
				}
				// Need to set these hidden values in the form so they are not set to null on save
				echo '<input type="text" hidden id="pro_version_enabled" name="pro_version_enabled" value="'. get_option('pro_version_enabled') . '" />';
				echo '<input type="text" hidden id="pro_version_activation_code" name="pro_version_activation_code" value="'. get_option('pro_version_activation_code') . '" />';
			?>

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
