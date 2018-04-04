<?php
/**
 * Franz Josef Display options
 */
function franz_customizer_display_options( $wp_customize ){
	
	/* =Posts
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-display-posts', array(
		'title' 		=> __( 'Posts', 'franz-josef' ),
		'panel'			=> 'fj-display',
	) );
	
	$wp_customize->add_control( 'franz_settings[tiled_posts]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Use tiled layout in posts listing pages', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_post_date]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Hide post date', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_post_cat]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Hide post categories', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_post_tags]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Hide post tags', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_post_author]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Hide post author', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_author_avatar]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( "Hide author's profile image", 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_featured_image]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Hide featured image', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_author_bio]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( "Hide author's bio", 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[adjacent_posts_same_term]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Next & previous posts links', 'franz-josef' ),
		'description'	=> __( 'Limit next & previous posts links to posts within the same category', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_responsive_tables]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Disable responsive tables', 'franz-josef' ),
		'description'	=> sprintf( __( 'You can also disable responsive tables individually by adding %s class to the tables.', 'franz-josef' ), '<code>non-responsive</code>' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_child_pages_nav]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Disable child pages navigation', 'franz-josef' ),
		'description'	=> __( 'Disables the "In this section" child pages navigation in the sidebar.', 'franz-josef' ),
	) );

	$wp_customize->add_control( 'franz_settings[disable_archive_video]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-posts',
		'label' 	=> __( 'Disable video in posts listing', 'franz-josef' ),
		'description'	=> __( 'Disables the feature where embedded video is used instead of post image in posts listing pages.', 'franz-josef' ),
	) );
	
	
	/* =Excerpts
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-display-excerpts', array(
		'title' 		=> __( 'Excerpts', 'franz-josef' ),
		'panel'			=> 'fj-display',
	) );
	
	$wp_customize->add_control( 'franz_settings[archive_full_content]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-excerpts',
		'label' 	=> __( 'Show full content in archive pages', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[excerpt_html_tags]', array(
		'type' 		=> 'text',
		'section' 	=> 'fj-display-excerpts',
		'label' 	=> __( 'Retain these HTML tags in excerpts', 'franz-josef' ),
		'description'	=> sprintf( __( 'Enter the HTML tags you\'d like to retain in excerpts. For example, enter %1$s to retain %2$s and %3$s HTML tags.', 'franz-josef' ), '<code>&lt;p&gt;&lt;ul&gt;&lt;li&gt;</code>', '<code>&lt;p&gt;</code>, <code>&lt;ul&gt;</code>,', '<code>&lt;li&gt;</code>' ),
		'input_attrs'	=> array(
			'class'	=> 'code'
		)
	) );
	
	
	/* =Miscellaneous
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-display-misc', array(
		'title' 		=> __( 'Miscellaneous', 'franz-josef' ),
		'panel'			=> 'fj-display',
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_search_widget]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-misc',
		'label' 	=> __( 'Disable search form in sidebar', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_editor_style]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-display-misc',
		'label' 	=> __( 'Disable Franz Josef styling in WordPress editor', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'franz_settings[favicon_url]', array(
		'label' 	=> __( 'Favicon', 'franz-josef' ),
		'section' 	=> 'fj-display-misc',
	) ) );
	
	
	/* =Custom CSS
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-display-css', array(
		'title' 		=> __( 'Custom CSS', 'franz-josef' ),
		'panel'			=> 'fj-display',
	) );
	
	$wp_customize->add_control( new Franz_Code_Control( $wp_customize, 'franz_settings[custom_css]', array(
		'type' 		=> 'textarea',
		'section' 	=> 'fj-display-css',
		'label' 	=> __( 'Custom CSS styles', 'franz-josef' ),
		'mode'		=> 'css',
		'input_attrs'	=> array(
			'rows'	=> 7,
			'cols'	=> 60
		)
	) ) );
}