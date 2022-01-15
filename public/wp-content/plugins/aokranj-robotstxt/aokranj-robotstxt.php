<?php
/*
Plugin Name: aokranj-robotstxt
Plugin URI: https://github.com/aokranj/website-aokranj.si
Description: Per-environment /robots.txt content
Version: 1.0.0
Author: Bostjan Skufca Jese
Author URI: https://github.com/bostjan
*/
add_filter(
    'robots_txt',
    function($output) {

        /**
         * Disable indexing of all but "prod" deployments
         */
        if (WP_ENV != "prod") {
            $output =
                "User-agent: *\n" .
                "Disallow: /\n" .
                "Disallow: /*\n" .
                "Disallow: /*?\n";
        }
        return $output;
    }
);
