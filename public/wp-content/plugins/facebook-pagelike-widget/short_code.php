<?php
function fb_plugin_shortcode($atts) {
    $defaults = shortcode_atts(array('title' => 'Like Us On Facebook', 'app_id' => '503595753002055', 'fb_url' => 'http://facebook.com/WordPress', 'width' => '400', 'height' => '500', 'data_small_header' => 'false', 'select_lng' => 'en_US', 'data_small_header' => 'false', 'data_adapt_container_width' => 'false', 'data_hide_cover' => 'false', 'data_show_facepile' => 'true', 'data_show_posts' => 'true', 'custom_css' => ''), $atts);
    wp_register_script('milapfbwidgetscript', FB_WIDGET_PLUGIN_URL . 'fb.js', array('jquery'));
    wp_enqueue_script('milapfbwidgetscript');
    $local_variables = array('app_id' => $defaults['app_id'], 'select_lng' => $defaults['select_lng']);
    wp_localize_script('milapfbwidgetscript', 'milapfbwidgetvars', $local_variables);
    echo '<div class="fb_loader" style="text-align: center !important;"><img src="' . plugins_url() . '/facebook-pagelike-widget/loader.gif" /></div>';
	return '<div id="fb-root"></div>
        <div class="fb-page" data-href="' . $defaults['fb_url'] . '" data-width="' . $defaults['width'] . '" data-height="' . $defaults['height'] . '" data-small-header="' . $defaults['data_small_header'] . '" data-adapt-container-width="' . $defaults['data_adapt_container_width'] . '" data-hide-cover="' . $defaults['data_hide_cover'] . '" data-show-facepile="' . $defaults['data_show_facepile'] . '" data-show-posts="' . $defaults['data_show_posts'] . '" style="' . $defaults['custom_css'] . '"></div>';
}
add_shortcode('fb_widget', 'fb_plugin_shortcode');
?>