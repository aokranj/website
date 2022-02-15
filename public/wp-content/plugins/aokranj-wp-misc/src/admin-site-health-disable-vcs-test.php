<?php
/*
 * Hide the warning `Background updates are not working as expected` that pops
 * up due to health check finding the .git directory.
 */
add_filter(
    'automatic_updates_is_vcs_checkout',
    function ($output) {
        return false;
    }
);
