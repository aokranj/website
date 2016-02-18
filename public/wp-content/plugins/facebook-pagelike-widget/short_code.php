<?php
function fb_plugin_shortcode() {
    global $app_id,$select_lng;
    $widget_facebook_widget = get_option('widget_fbw_id');
    if (is_active_widget(false, '', 'fbw_id')) {
        foreach ($widget_facebook_widget as $facebook_widget) {
            $result     = '';
            $title                          =   $facebook_widget['title'];
            $app_id                         =   $facebook_widget['app_id'];
            $fb_url                         =   $facebook_widget['fb_url'];
            $width                          =   $facebook_widget['width'];
            $height                         =   $facebook_widget['height'];
            $data_small_header              =   isset($facebook_widget['data_small_header']) && $facebook_widget['data_small_header'] != '' ? 'true' : 'false';
            $data_adapt_container_width     =   isset($facebook_widget['data_adapt_container_width']) && $facebook_widget['data_adapt_container_width'] != '' ? 'true' : 'false';
            $data_hide_cover                =   isset($facebook_widget['data_hide_cover']) && $facebook_widget['data_hide_cover'] != '' ? 'true' : 'false';
            $data_show_facepile             =   isset($facebook_widget['data_show_facepile']) && $facebook_widget['data_show_facepile'] != '' ? 'true' : 'false';
            $data_show_posts                =   isset($facebook_widget['data_show_posts']) && $facebook_widget['data_show_posts'] != '' ? 'true' : 'false';
            $custom_css                     =	$facebook_widget['custom_css'];
            $select_lng                     =	$facebook_widget['select_lng'];
            $result = $before_widget;
            if ($title)
                $result .= $before_title . $title . $after_title;
            wp_register_script('myownscript', FB_WIDGET_PLUGIN_URL . 'fb.js', array('jquery'));
            wp_enqueue_script('myownscript');
            $local_variables = array('app_id' => $app_id,'select_lng'=>$select_lng);
            wp_localize_script('myownscript', 'vars', $local_variables);
            $result .= '<div id="fb-root"></div>
            <div class="fb-page" data-href="'.$fb_url.'" data-width="'.$width.'" data-height="'.$height.'" data-small-header="'.$data_small_header.'" data-adapt-container-width="'.$data_adapt_container_width.'" data-hide-cover="'.$data_hide_cover.'" data-show-facepile="'.$data_show_facepile.'" data-show-posts="'.$data_show_posts.'" style="'.$custom_css.'"></div>';
            $result .= $after_widget;
            return $result;
        }
    }
    else {
        return "Please configure Widget first..!!";
    }
}
add_shortcode('fb_widget','fb_plugin_shortcode');
?>