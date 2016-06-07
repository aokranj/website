<?php
function fb_plugin_shortcode($atts) {
    global $app_id, $select_lng;
    $atts = shortcode_atts(array('title' => 'Like Us On Facebook', 'app_id' => '503595753002055', 'fb_url' => 'http://facebook.com/WordPress', 'width' => '400', 'height' => '500', 'data_small_header' => 'false', 'select_lng' => 'en_US', 'data_small_header' => 'false', 'data_adapt_container_width' => 'false', 'data_hide_cover' => 'false', 'data_show_facepile' => 'true', 'data_show_posts' => 'true', 'custom_css' => ''), $atts);
    if ($atts['title'])
        $result .= "<h4 class='customtitle'>".$atts['title']."</h2>";
    wp_register_script('myownscript', FB_WIDGET_PLUGIN_URL . 'fb.js', array('jquery'));
    wp_enqueue_script('myownscript');
    $local_variables = array('app_id' => $atts['app_id'], 'select_lng' => $atts['select_lng']);
    wp_localize_script('myownscript', 'milapfbwidgetvars', $local_variables);
    echo '<center><div class="loader"><img src="' . plugins_url() . '/facebook-pagelike-widget/loader.gif" /></div></center>';
    $result .= '<div id="fb-root"></div>
        <div class="fb-page" data-href="' . $atts['fb_url'] . '" data-width="' . $atts['width'] . '" data-height="' . $atts['height'] . '" data-small-header="' . $atts['data_small_header'] . '" data-adapt-container-width="' . $atts['data_adapt_container_width'] . '" data-hide-cover="' . $atts['data_hide_cover'] . '" data-show-facepile="' . $atts['data_show_facepile'] . '" data-show-posts="' . $atts['data_show_posts'] . '" style="' . $atts['custom_css'] . '"></div>';
    return $result;
}
add_shortcode('fb_widget', 'fb_plugin_shortcode');
?>