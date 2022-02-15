<?php
/*
 * Manage what content gets served at /robots.txt
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
