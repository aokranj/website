<?php

function aokranj_theme_locale() {
    load_child_theme_textdomain( 'franz-josef', AOKRANJ_ROOTDIR . '/languages' );
}

add_action( 'after_setup_theme', 'aokranj_theme_locale' );
