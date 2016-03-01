<?php

function aokranj_customize_register( $wp_customize ) {
	$wp_customize->add_control( 'franz_settings[disable_credit]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-footer',
        'priority'  => 0,
		'label' 	=> __( 'Do not show franz josef credit', 'franz-josef' ),
	) );
}

add_action( 'customize_register', 'aokranj_customize_register', 20 );
