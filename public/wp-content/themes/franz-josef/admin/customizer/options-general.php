<?php
/**
 * Franz Josef General options
 */
function franz_customizer_general_options( $wp_customize ){
	
	/* =Static Front Page
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_control( 'franz_settings[disable_blog_sidebar]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'static_front_page',
		'label'		=> __( 'Disable sidebar in Posts page', 'franz-josef' ),
		'active_callback'	=> 'franz_has_static_posts_page',
	) );
	
	
	/* =Top Bar
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-general-top-bar', array(
	  'title' 		=> __( 'Top Bar', 'franz-josef' ),
	  'panel'		=> 'fj-general',
	  'description'	=> '<p>' . 
	  					sprintf( __( 'Positioning of elements in the Top Bar can be customised using the "Top Bar" widget area and "Franz Josef Top Bar" widgets in %s.', 'franz-josef' ), 
						'<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . __( 'Appearance > Widgets', 'franz-josef' ) . '</a>' ) .
						'</p><p>' .
						sprintf( __( 'Custom menu can be assigned to the "Top Menu" location in %s.', 'franz-josef' ), 
							'<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . __( 'Appearance > Menus', 'franz-josef' ) . '</a>' ) .
						'</p>'
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_top_bar]', array(
	  'type' 	=> 'checkbox',
	  'section' => 'fj-general-top-bar',
	  'label' => __( 'Disable Top Bar', 'franz-josef' ),
	) );
	
	
	/* =Slider
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-general-slider', array(
		'title' 		=> __( 'Slider', 'franz-josef' ),
		'panel'			=> 'fj-general',
		'active_callback'	=> 'is_front_page'
	) );
	
	$wp_customize->add_control( 'franz_settings[slider_disable]', array(
		'type' 	=> 'checkbox',
		'section' => 'fj-general-slider',
		'label' 	=> __( 'Disable slider', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[slider_type]', array(
		'type' 		=> 'select',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Slider posts', 'franz-josef' ),
		'choices'	=> array(
			'latest_posts'	=> __( 'Show latest posts', 'franz-josef' ),
			'random'		=> __( 'Show random posts', 'franz-josef' ),
			'posts_pages'	=> __( 'Show specific posts/pages', 'franz-josef' ),
			'categories'	=> __( 'Show posts from categories', 'franz-josef' )
		)
	) );
	
	$wp_customize->add_control( 'franz_settings[slider_specific_posts]', array(
		'type' 		=> 'text',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Posts and/or pages to display', 'franz-josef' ),
		'input_attrs' => array(
			'placeholder'	=> __( 'Post IDs, eg: 1,13,45', 'franz-josef' ),
		),
	) );
	
	$cat_choices = array();
	$categories = get_categories( array( 'hide_empty' => true ) );
    foreach ( $categories as $cat ) $cat_choices[$cat->cat_ID] = $cat->cat_name;
	
	$wp_customize->add_control( new Franz_Multiple_Select_Control( $wp_customize, 'franz_settings[slider_specific_categories]', array(
		'type' 		=> 'select',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Slider categories', 'franz-josef' ),
		'multiple'	=> true,
		'choices'	=> $cat_choices,
		'input_attrs'	=> array(
			'data-placeholder'	=> __( 'Select categories', 'franz-josef' ),
		)
	) ) );
	
	$wp_customize->add_control( 'franz_settings[slider_random_category_posts]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Randomize posts from categories', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[slider_exclude_categories]', array(
		'type' 		=> 'select',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Hide the categories', 'franz-josef' ),
		'choices'	=> array(
			'disabled'	=> __( 'Disabled', 'franz-josef' ),
			'homepage'	=> __( 'Home Page', 'franz-josef' ),
			'everywhere'=> __( 'Everywhere', 'franz-josef' ),
		),
	) );
	
	$wp_customize->add_control( 'franz_settings[slider_postcount]', array(
		'type' 		=> 'number',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Number of posts to display', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[slider_content]', array(
		'type' 		=> 'select',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Slider content', 'franz-josef' ),
		'choices'	=> array(
			'excerpt'		=> __( 'Excerpt', 'franz-josef' ),
			'full_content'	=> __( 'Full content', 'franz-josef' )
		)
	) );
	
	$wp_customize->add_control( new Franz_Enhanced_Text_Control( $wp_customize, 'franz_settings[slider_postcount]', array(
		'type' 		=> 'number',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Number of posts to display', 'franz-josef' ),
	) ) );
	
	$wp_customize->add_control( new Franz_Enhanced_Text_Control( $wp_customize, 'franz_settings[slider_height]', array(
		'type' 		=> 'number',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Slider height', 'franz-josef' ),
		'unit'		=> 'pixels',
	) ) );
	
	$wp_customize->add_control( new Franz_Enhanced_Text_Control( $wp_customize, 'franz_settings[slider_interval]', array(
		'type' 		=> 'number',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Slider interval', 'franz-josef' ),
		'unit'		=> 'seconds'
	) ) );
	
	$wp_customize->add_control( new Franz_Enhanced_Text_Control( $wp_customize, 'franz_settings[slider_trans_duration]', array(
		'type' 		=> 'number',
		'section' 	=> 'fj-general-slider',
		'label' 	=> __( 'Slider transition duration', 'franz-josef' ),
		'unit'		=> 'seconds',
	) ) );
	
	
	/* =Front Page
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-general-front-page', array(
		'title' 		=> __( 'Front Page', 'franz-josef' ),
		'panel'			=> 'fj-general',
		'active_callback'	=> 'is_front_page'
	) );
	
	$wp_customize->add_control( 'franz_settings[enable_frontpage_sidebar]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-front-page',
		'label' 	=> __( 'Show sidebar', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( new Franz_Multiple_Select_Control( $wp_customize, 'franz_settings[frontpage_posts_cats]', array(
		'type' 		=> 'select',
		'section' 	=> 'fj-general-front-page',
		'label' 	=> __( 'Front page posts categories', 'franz-josef' ),
		'description'	=> __( 'Only posts that belong to the categories selected here will be displayed on the front page.', 'franz-josef' ),
		'multiple'	=> true,
		'choices'	=> $cat_choices,
		'input_attrs'	=> array(
			'data-placeholder'	=> __( 'Select categories', 'franz-josef' ),
		)
	) ) );
	
	$wp_customize->add_control( 'franz_settings[front_page_blog_columns]', array(
		'type' 		=> 'select',
		'section' 	=> 'fj-general-front-page',
		'label' 	=> __( 'Blog posts columns', 'franz-josef' ),
		'choices'	=> array(
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
		)
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_full_width_post]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-front-page',
		'label' 	=> __( 'Disable full-width first blog post', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_front_page_blog]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-front-page',
		'label' 	=> __( "Don't show blog posts", 'franz-josef' ),
		'description'	=> __( 'Disable listing of blog posts on static front page', 'franz-josef' ),
		'active_callback'	=> 'franz_has_static_front_page',
	) );
	
	
	/* =Head Tags
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-general-head-tags', array(
		'title' 		=> __( 'Custom <head> tags', 'franz-josef' ),
		'panel'			=> 'fj-general',
	) );
	
	$wp_customize->add_control( new Franz_Code_Control( $wp_customize, 'franz_settings[head_tags]', array(
		'type' 		=> 'textarea',
		'section' 	=> 'fj-general-head-tags',
		'label' 	=> __( 'Code to insert into the <head> element', 'franz-josef' ),
		'input_attrs'	=> array(
			'rows'	=> 7,
			'cols'	=> 60
		)
	) ) );
	
	
	/* =Footer
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-general-footer', array(
		'title' 		=> __( 'Footer', 'franz-josef' ),
		'panel'			=> 'fj-general',
	) );
	
	$wp_customize->add_control( 'franz_settings[footerwidget_column]', array(
		'type' 		=> 'number',
		'section' 	=> 'fj-general-footer',
		'label' 	=> __( 'Number of footer widget area columns', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[hide_copyright]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-footer',
		'label' 	=> __( 'Do not show copyright info', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( new Franz_Code_Control( $wp_customize, 'franz_settings[copyright_text]', array(
		'type' 		=> 'textarea',
		'section' 	=> 'fj-general-footer',
		'label' 	=> __( 'Copyright text (html allowed)', 'franz-josef' ),
		'input_attrs'	=> array(
			'rows'	=> 3,
			'cols'	=> 60
		)
	) ) );
	
	
	/* =Print
	--------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'fj-general-print', array(
		'title' 		=> __( 'Print', 'franz-josef' ),
		'panel'			=> 'fj-general',
	) );
	
	$wp_customize->add_control( 'franz_settings[disable_print_css]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-print',
		'label' 	=> __( 'Disable print stylesheet', 'franz-josef' ),
	) );
	
	$wp_customize->add_control( 'franz_settings[print_button]', array(
		'type' 		=> 'checkbox',
		'section' 	=> 'fj-general-print',
		'label' 	=> __( 'Show print button', 'franz-josef' ),
	) );
}