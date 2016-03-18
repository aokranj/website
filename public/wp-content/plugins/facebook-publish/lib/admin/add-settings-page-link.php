<?php

add_filter('plugin_action_links', 'fp_plugin_action_links', 10, 2);

function fp_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = "facebook-publish/init.php";
    }
    
    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link  = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=facebook-publish">Settings</a>';
        array_unshift($links, $settings_link);

    }

    return $links;
}