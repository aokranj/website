<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    franz_enqueue_scripts();
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
