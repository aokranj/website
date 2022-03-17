<?php
/*
 * Hide the warning `Background updates are not working as expected` that pops
 * up due to health check finding the .git directory.
 */
add_filter(
    'wp_mail_from',
    function ($origValue) {
        if (defined('AOKRANJ_MAIL_FROM_ADDR')) {
            return AOKRANJ_MAIL_FROM_ADDR;
        } else {
            return $origValue;
        }
    }
);

add_filter(
    'wp_mail_from_name',
    function ($origValue) {
        if (defined('AOKRANJ_MAIL_FROM_NAME')) {
            return AOKRANJ_MAIL_FROM_NAME;
        } else {
            return $origValue;
        }
    }
);
