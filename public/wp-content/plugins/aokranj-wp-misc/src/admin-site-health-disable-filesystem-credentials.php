<?php
/*
 * This one is pretty nasty - it was hidden but it made the `Background
 * updates are not working as expected` appear as a critical error.
 * Let's disable FTP updates altogether (who does that anyway?!? :)).
 */
add_filter(
    'request_filesystem_credentials',
    function ($output) {
        // Apparently (bool) true means no credentials are needed.
        // This may backfire in the future, when WP will try to overwrite own files, for whatever reason.
        return true;
    }
);
