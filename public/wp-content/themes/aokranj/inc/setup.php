<?php

function aokranj_theme_setup() {
    load_child_theme_textdomain( 'franz-josef', AOKRANJ_THEME_ROOTDIR . '/languages' );
    add_editor_style( array( 'editor.css' ) );
}

add_action( 'after_setup_theme', 'aokranj_theme_setup' );
