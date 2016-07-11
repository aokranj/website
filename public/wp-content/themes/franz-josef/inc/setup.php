<?php
global $content_width;
$content_width = 848;
/**
 * Setup the theme
 */
function franz_setup(){
	load_theme_textdomain( 'franz-josef', FRANZ_ROOTDIR . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-background' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5' );
	add_theme_support( 'comment-form' );
	add_theme_support( 'post-formats', array( 'quote', 'status' ) );
	add_editor_style( array( 'bootstrap/css/bootstrap.min.css', 'editor-style.css' ) );
	
	$args = array(
		'width'                  => 250,
		'height'                 => 30,
		'flex-height'            => true,
		'flex-width'             => true,
		'default-text-color'     => 'ffffff',
		'wp-head-callback'       => '',
		'admin-head-callback'    => '',
		'admin-preview-callback' => '',
	);
	add_theme_support( 'custom-header', apply_filters( 'franz_custom_header_args', $args ) );
	
	$args = array( 'default-color'          => 'ffffff' );
	add_theme_support( 'custom-background', apply_filters( 'franz_custom_background_args', $args ) );
	
	set_post_thumbnail_size( 850, 450, true );
	add_image_size( 'franz-medium', 600, 300, true );
	add_image_size( 'franz-slider', 1920, 500, true );
	add_image_size( 'franz-slider-contained', 1140, 500, true );
	
	add_post_type_support( 'page', 'excerpt' );
	
	register_nav_menus( array( 
		'top-menu' 		=> __( 'Top Menu', 'franz-josef' ),
		'header-menu' 	=> __( 'Header Menu', 'franz-josef' ),
		'footer-menu' 	=> __( 'Footer Menu', 'franz-josef' ),
	) );
	
	do_action( 'franz_setup' );
}
add_action( 'after_setup_theme', 'franz_setup' );


/**
 * Register widgetized areas
 */
function franz_widgets_init() {
	global $franz_settings;
		
	$args = array( 
		'name' 			=> __( 'Sidebar', 'franz-josef' ),
		'id' 			=> 'sidebar',
		'before_widget' => '<div id="%1$s" class="clearfix widget %2$s">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h3 class="section-title-sm">',
		'after_title' 	=> '</h3>',
	);
	register_sidebar( apply_filters( 'franz_sidebar_args', $args ) );
	
	$args = array( 
		'name' 			=> __( 'Footer', 'franz-josef' ),
		'id' 			=> 'footer-widgets',
		'before_widget' => '<div id="%1$s" class="clearfix item col-sm-6 col-md-' . floor( 12 / $franz_settings['footerwidget_column'] ) . ' %2$s">',
		'after_widget' 	=> '</div>',
		'before_title' 	=> '<h4 class="item-title">',
		'after_title' 	=> '</h4>',
	);
	register_sidebar( apply_filters( 'franz_footer_widget_args', $args ) );
	
	$args = array( 
		'name' 			=> __( 'Top Bar', 'franz-josef' ),
		'description'	=> __( "Use this widget area to customise the content and element positioning in the top bar. Leave empty to use the theme's default.", 'franz-josef' ),
		'id' 			=> 'top-bar',
		'before_widget' => '',
		'after_widget' 	=> '',
		'before_title' 	=> '',
		'after_title' 	=> '',
	);
	register_sidebar( apply_filters( 'franz_top_bar_widget_args', $args ) );
	
}
add_action( 'widgets_init', 'franz_widgets_init' );


if ( ! function_exists( 'franz_column_mode' ) ) :
/**
 * Get the theme's final column mode setting for display
 */
function franz_column_mode( $post_id = NULL ){
    global $franz_settings, $franz_column_mode;
	
	if ( $franz_column_mode ) return apply_filters( 'franz_column_mode', $franz_column_mode, $post_id );
    
    // Check the front-end template
	if ( ! is_admin() && ! $post_id ){
		if ( is_page_template( 'template-single-column.php' ) )	$franz_column_mode = 'one-column';
		elseif ( is_home() && $home_page = get_option( 'page_for_posts' ) && $franz_settings['disable_blog_sidebar'] ) $franz_column_mode = 'one-column'; // Check for static posts page
		else $franz_column_mode = 'two-column right-sidebar';
	}
	
		
	/* Check the template in Edit Page screen in admin */
	if ( is_admin() || $post_id ){
		if ( ! $post_id ) $post_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : NULL;
		$page_template = get_post_meta( $post_id, '_wp_page_template', true );
		
		if ( $page_template != 'default' ) if ( strpos( $page_template, 'template-single-column' ) === 0 ) $franz_column_mode = 'one-column';
	}
	
	return apply_filters( 'franz_column_mode', $franz_column_mode, $post_id );
}
endif;


/**
 * Get the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
function franz_get_content_width(){
	global $franz_settings, $franz_defaults, $content_width;
	
	$column_mode = franz_column_mode();
	if ( $column_mode == 'one-column' ) {
		$content_width = 1140;
		set_post_thumbnail_size( $content_width, 450, true );
	}

	return apply_filters( 'franz_content_width', $content_width );
}


/**
 * Set the global $content_width variable
 */
function franz_set_content_width(){
	global $content_width;
	$content_width = franz_get_content_width();
}
add_action( 'template_redirect', 'franz_set_content_width' );


/**
 * Add content width parameter to the WordPress editor
 */
function franz_editor_width(){
	global $content_width, $franz_settings;
	$content_width = franz_get_content_width();
	?>
    <script type="text/javascript">
		jQuery(document).ready(function($) {
			setTimeout( function(){
				$('#content_ifr').contents().find('#tinymce').css( 'width', '<?php echo $content_width; ?>' );
			}, 2000 );
		});
	</script>
    <?php
}
add_action( 'after_wp_tiny_mce', 'franz_editor_width' );