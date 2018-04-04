<?php
if ( ! function_exists( 'franz_header_image' ) ) :
/**
 * Creates the tag for header image
 */
function franz_header_image(){
	$header = get_custom_header(); 
	if ( $header->url ) :
	
		/* Scale hidpi header image */
		$custom_header = get_theme_support( 'custom-header' );
		$header_args = array_pop( $custom_header );
		if ( $header->width >= ( 2 * $header_args['width'] ) ) {
			$header->width = floor( $header->width / 2 );
			$header->height = floor( $header->height / 2 );
		}
	?>
        <img src="<?php echo esc_url( $header->url ); ?>" height="<?php echo esc_attr( $header->height ); ?>" width="<?php echo esc_attr( $header->width ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
    <?php else : bloginfo( 'name' ); endif;
}
endif;


/**
 * Breadcrumb NavXT
 */
function franz_breadcrumbs(){
	if ( ! function_exists( 'bcn_display' ) ) return;
	if ( ! is_singular() && ! is_archive() && ! is_search() ) return;
	if ( is_front_page() || is_author() ) return;
	if ( franz_has_custom_layout() ) return;
	?>
    <div class="breadcrumbs-wrapper">
        <div class="container breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#"><?php bcn_display(); ?></div>
    </div>
    <?php
}
add_action( 'franz_before_content', 'franz_breadcrumbs' );