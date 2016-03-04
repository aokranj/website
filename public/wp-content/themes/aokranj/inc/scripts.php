<?php

function aokranj_enqueue_styles() {

    // remove franz josef styles
    wp_dequeue_style( 'bootstrap' );
    wp_dequeue_style( 'font-awesome' );
    wp_dequeue_style( 'franzjosef' );
    wp_dequeue_style( 'franzjosef-print' );
    wp_dequeue_style( 'franzjosef-responsive' );
    wp_dequeue_style( 'franzjosef-google-fonts' );

    // re-add franz josef styles but override handles
    global $franz_settings;
	if ( ! is_admin() ) {
        wp_enqueue_style( 'bootstrap-parent', FRANZ_ROOTURI . '/bootstrap/css/bootstrap.min.css' );
		if ( is_rtl() ) wp_enqueue_style( 'bootstrap-rtl-parent', FRANZ_ROOTURI . '/bootstrap-rtl/bootstrap-rtl.min.css', array( 'bootstrap-parent' ) );
		wp_enqueue_style( 'font-awesome-parent', FRANZ_ROOTURI . '/fonts/font-awesome/css/font-awesome.min.css' );
		wp_enqueue_style( 'franzjosef-parent', FRANZ_ROOTURI . '/style.css', array( 'bootstrap-parent', 'font-awesome-parent' ) );
		wp_enqueue_style( 'franzjosef-responsive-parent', FRANZ_ROOTURI . '/responsive.css', array( 'bootstrap-parent', 'font-awesome-parent', 'franzjosef-parent' ) );
		if ( is_rtl() ) wp_enqueue_style( 'franzjosef-responsive-rtl-parent', FRANZ_ROOTURI . '/responsive-rtl.css', array( 'franzjosef-parent' ) );
		wp_enqueue_style( 'franzjosef-google-fonts-parent', franz_google_fonts_uri(), array() );

		if ( ! $franz_settings['disable_print_css'] ) wp_enqueue_style( 'franzjosef-print-parent', FRANZ_ROOTURI . '/print.css', array( 'franzjosef-responsive-parent' ), false, 'print' );
    }

    // add aokranj styles after all the others
    wp_enqueue_style( 'aokranj', AOKRANJ_ROOTURI . '/style.css', array( 'franzjosef-parent', 'franzjosef-responsive-parent', 'franzjosef-google-fonts-parent' ) );
    wp_enqueue_script('aokranj', AOKRANJ_ROOTURI . '/aokranj.js', array('jquery', 'franzjosef', 'bootstrap'), AOKRANJ_PLUGIN_VERSION  );
}

add_action( 'wp_enqueue_scripts', 'aokranj_enqueue_styles', 20 );
