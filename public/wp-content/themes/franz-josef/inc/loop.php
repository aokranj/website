<?php
/**
 * Return default placeholder thumbnail if post doesn't have post thumbnail
 */
function franz_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ){
	global $franz_no_default_thumb;
	
	if ( in_array( $size, array( 'thumbnail', 'franz-medium' ) ) ) {
		if ( ! $html && ! $franz_no_default_thumb ) {
			$html = '<span class="generic-thumb ' . esc_attr( $size ) . '"><i class="fa fa-camera"></i></span>';
		} else if ( $size == 'thumbnail' ) {
			$html = str_replace( 'class="', 'class="thumbnail ', $html );
		}
	}
	
	if ( in_array( $size, array( 'post-thumbnail', 'franz-medium' ) ) ) {
		$html = str_replace( 'class="', 'class="img-responsive ', $html );
	}
	
	return $html;
}
add_filter( 'post_thumbnail_html', 'franz_post_thumbnail_html', 10, 5 );


/**
 * Determine the correct template part to load
 */
function franz_get_template_part( $p1, $p2 = '' ){
	
	if ( $p1 == 'loop' && ! $p2 ) {
		$p2 = get_post_format();
		$filename = '/formats/loop-' . $p2 . '.php';
		if ( $p2 != 'standard' && ( file_exists( FRANZ_ROOTDIR . $filename ) || file_exists( FJ_CHILDDIR . $filename ) ) ) $p1 = 'formats/loop';
		else $p2 = '';
	}
	
	get_template_part( $p1, $p2 );
}


/**
 * Add custom classes to posts
 */
function franz_body_class( $classes ){
	global $franz_settings;
	if ( is_singular() || is_author() ) $classes[] = 'singular';
	else $classes[] = 'non-singular';
	
	if ( ! is_singular() && ! is_author() && $franz_settings['tiled_posts'] ) $classes[] = 'tiled-posts';
	if ( ! $franz_settings['disable_top_bar'] ) $classes[] = 'has-top-bar';
	
	if ( is_front_page() ) {
		$classes[] = 'front-page';
		if ( $franz_settings['enable_frontpage_sidebar'] ) $classes[] = 'has-sidebar';
	}
	
	$classes[] = franz_column_mode();
	
	return $classes;
}
add_filter( 'body_class', 'franz_body_class' );


/**
 * Determine the main content area class for layout
 */
function franz_main_content_classes( $classes = array() ) {

	$column_mode = franz_column_mode();
	if ( stripos( $column_mode, 'left-sidebar' ) !== false ) $classes[] = 'col-md-push-3';
	if ( stripos( $column_mode, 'one-column' ) !== false ) {
		$classes[] = 'col-md-12';
		$classes = array_diff( $classes, array( 'col-md-9') );
	}
	
	echo implode( ' ', $classes );
}


/**
 * Entry meta
 */
function franz_entry_meta(){
	global $franz_settings, $post;
	$post_id = get_the_ID();
	$author_id = $post->post_author;
	$meta = array();
	
	/* Don't get meta for pages */
	if ( 'page' == get_post_type( $post_id ) ) return;
	
	/* Print button */
	if ( $franz_settings['print_button'] && is_singular() ) {
		$meta['print'] = array(
			'class'	=> 'print-button',
			'meta'	=> '<a href="javascript:print();" title="' . esc_attr__( 'Print this page', 'franz-josef' ) . '"><i class="fa fa-print"></i></a>',
		);
	}
	
	/* Post date */
	if ( ! $franz_settings['hide_post_date'] ) {
		$meta['date'] = array(
			'class'	=> 'date',
			'meta'	=> '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . get_the_time( get_option( 'date_format' ) ) . '</a>',
		);
	}
	
	/* Post author and categories */
	if ( ! $franz_settings['hide_post_cat'] ) {
		$cats = get_the_category(); $categories = array();
		if ( $cats ) {
			foreach ( $cats as $cat ) $categories[] = '<a class="term term-' . esc_attr( $cat->taxonomy ) . ' term-' . esc_attr( $cat->term_id ) . '" href="' . esc_url( get_term_link( $cat->term_id, $cat->taxonomy ) ) . '">' . $cat->name . '</a>';
		}
		if ( $categories ) $categories = '<span class="terms">' . implode( ', ', $categories ) . '</span>';
	}
	if ( ! $franz_settings['hide_post_author'] ) {
		$author = '<span class="author"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ) . '" rel="author">' . get_the_author_meta( 'display_name' , $author_id ) . '</a></span>';
	}
	
	if ( $categories && $author ) $byline = sprintf( __( 'By %1$s under %2$s', 'franz-josef' ), $author, $categories );
	elseif ( $categories ) $byline = sprintf( __( 'Filed under %2$s', 'franz-josef' ), $author, $categories );
	elseif ( $author ) $byline = sprintf( __( 'By %s', 'franz-josef' ), $author );
	else $byline = false;
	
	if ( $byline ) $meta['byline'] = array( 'class'	=> 'byline', 'meta'	=> $byline );
	
	/* Comments link */
	if ( franz_should_show_comments( $post_id ) ) {
		$comment_count = get_comment_count( $post_id );
		$approved_comment_count = $comment_count['approved'];
		$comment_text = ( $comment_count['approved'] ) ? sprintf( _n( '%d comment', '%d comments', $approved_comment_count, 'franz-josef' ), $approved_comment_count ) : __( 'Leave a reply', 'franz-josef' );
		$comments_link = ( $comment_count['approved'] ) ? get_comments_link() : str_replace( '#comments', '#respond', get_comments_link() );
		$meta['comments'] = array(
			'class'	=> 'comments-count',
			'meta'	=> '<a href="' . esc_url( $comments_link ) . '">' . $comment_text . '</a>',
		);
	}
	
	/* Post tags */
	$tags = get_the_tags();
	if ( $tags ) {
		$html = '';
		if ( count( $tags ) > 1 ) $html .= '<i class="fa fa-tags"></i>';
		else $html .= '<i class="fa fa-tag"></i>';
		
		$tag_links = array();
		foreach ( $tags as $tag ) $tag_links[] = '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . $tag->name . '</a>';
		$html .= implode( ', ', $tag_links );
		
		if ( $html ) $meta['tags'] = array(
			'class'	=> 'entry-tags',
			'meta'	=> $html
		);
	}
	
	$meta = apply_filters( 'franz_entry_meta', $meta, $post_id );
	if ( ! $meta ) return;
	?>
    <ul class="entry-meta">
    	<?php foreach ( $meta as $item ) : ?>
        <li class="<?php echo esc_attr( $item['class'] ); ?>"><?php echo $item['meta']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
	do_action( 'franz_do_entry_meta' );
}


/**
 * Entry meta for author page
 */
function franz_author_entry_meta(){
	$meta = array();
	$post_id = get_the_ID();
	
	$meta['date'] = array(
		'class'	=> 'date updated',
		'meta'	=> '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . get_the_time( get_option( 'date_format' ) ) . '</a>',
	);
	
	$comment_count = get_comment_count( $post_id );
	$comment_text = ( $comment_count['approved'] ) ? $comment_count['approved'] : __( 'Leave a reply', 'franz-josef' );
	$comments_link = ( $comment_count['approved'] ) ? get_comments_link() : str_replace( '#comments', '#respond', get_comments_link() );
	$meta['comments'] = array(
		'class'	=> 'comments-count',
		'meta'	=> '<a href="' . esc_url( $comments_link ) . '"><i class="fa fa-comment"></i> ' . $comment_text . '</a>',
	);
	
	$meta = apply_filters( 'franz_author_entry_meta', $meta );
	if ( ! $meta ) return;
	?>
    <ul class="entry-meta">
    	<?php foreach ( $meta as $item ) : ?>
        <li class="<?php echo esc_attr( $item['class'] ); ?>"><?php echo $item['meta']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
}


/**
 * Add structured data markup
 */
function franz_structured_data_markup(){
	global $post, $franz_settings;
	if ( ! is_singular() ) return;
	
	$markup = array();
	
	/* Date published and updated */
	$markup[] = '<span class="published"><span class="value-title" title="' . date( 'c', strtotime( $post->post_date_gmt ) ) . '" /></span>';
	$markup[] = '<span class="updated"><span class="value-title" title="' . date( 'c', strtotime( $post->post_modified_gmt ) ) . '" /></span>';
	
	/* Author */
	$markup[] = '<span class="vcard author"><span class="fn nickname"><span class="value-title" title="'. get_the_author_meta( 'display_name' ) . '" /></span></span>';
	
	$markup = apply_filters( 'franz_structured_data_markup', $markup );
	if ( ! $markup ) return;
	
	echo implode( "\n", $markup );
}
add_action( 'franz_do_entry_meta', 'franz_structured_data_markup' );


/**
 * Control the excerpt length
*/
function franz_modify_excerpt_length( $length ) {
	global $franz_excerpt_length;

	if ( $franz_excerpt_length ) return $franz_excerpt_length;
	else return $length;
}
add_filter( 'excerpt_length', 'franz_modify_excerpt_length' );


/**
 * Set the excerpt length
*/
function franz_set_excerpt_length( $length ){
	if ( ! $length ) return;
	global $franz_excerpt_length;
	$franz_excerpt_length = $length;
}


/**
 * Reset the excerpt length
*/
function franz_reset_excerpt_length(){
	global $franz_excerpt_length;
	unset( $franz_excerpt_length );
}


if ( ! function_exists( 'franz_page_navigation' ) ) :
/**
 * List subpages of current page
 */
function franz_page_navigation(){
	global $franz_settings;
	if ( $franz_settings['disable_child_pages_nav'] ) return;
	
	$current = get_the_ID();
	$ancestors = get_ancestors( $current, 'page' );
	if ( $ancestors ) $parent = $ancestors[0];
	else $parent = $current;
	
	$args = array(
		'post_type'			=> array( 'page' ),
		'posts_per_page'	=> -1,
		'post_parent'		=> $parent,
		'orderby'			=> 'menu_order title',
		'order'				=> 'ASC'
	);
	$children = new WP_Query( apply_filters( 'franz_page_navigation_args', $args ) );
	
	if ( $children->have_posts() ) :
	?>
        <div class="widget">
            <h3 class="section-title-sm"><?php _e( 'In this section', 'franz-josef' ); ?></h3>
            <div class="list-group page-navigation">
            	<a class="list-group-item parent <?php if ( $parent == $current ) echo 'active'; ?>" href="<?php echo esc_url( get_permalink( $parent ) ); ?>"><?php echo get_the_title( $parent ); ?></a>
                <?php while ( $children->have_posts() ) : $children->the_post(); ?>
                <a class="list-group-item <?php if ( get_the_ID() == $current ) echo 'active'; ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php endwhile; ?>
            </div>
        </div>
    <?php 
	endif; wp_reset_postdata(); 
}
endif;


if ( ! function_exists( 'franz_posts_nav' ) ) :
/**
 * Posts navigation
 */
function franz_posts_nav( $args = array() ){
	global $wp_query, $franz_settings;
	$defaults = apply_filters( 'franz_posts_nav_defaults', array(
		'current'			=> max( 1, get_query_var( 'paged' ) ),
		'total'				=> $wp_query->max_num_pages,
		'base'				=> '',
		'format'			=> '',
		'add_fragment'		=> '',
		'type'				=> 'post',
		'prev_text'			=> '&laquo;',
		'next_text' 		=> '&raquo;'
	) );
	$args = wp_parse_args( $args, $defaults );
	
	$paginate_args = array(
		'current' 		=> $args['current'],
		'total'			=> $args['total'],
		'prev_text' 	=> $args['prev_text'],
		'next_text' 	=> $args['next_text'],
		'type'      	=> 'array',
		'echo'			=> false,
		'add_fragment'	=> $args['add_fragment'],
	);
	if ( $args['base'] ) $paginate_args['base'] = $args['base'];
	if ( $args['format'] ) $paginate_args['format'] = $args['format'];
	
	
	if ( $args['type'] == 'comment' ) $links = paginate_comments_links( apply_filters( 'franz_comments_nav_args', $paginate_args ) );
	else $links = paginate_links( apply_filters( 'franz_posts_nav_args', $paginate_args ) );
	
	if ( $links ) :
	?>
		<ul class="pagination">
			<?php if ( $args['current'] == 1 ) : ?><li class="disabled"><span class="page-numbers"><?php echo $args['prev_text']; ?></span></li><?php endif; ?>
			<?php 
				foreach ( $links as $link ) {
					if ( stripos( $link, 'current' ) !== false ) $link = '<li class="active">' . $link . '</li>';
					else $link = '<li>' . $link . '</li>';
					echo $link;
				}
			?>
		</ul>
	<?php
		do_action( 'franz_posts_nav', $args );
	endif;
}
endif;


if ( ! function_exists( 'franz_comments_nav' ) ) :
/**
 * Comments pagination
 */
function franz_comments_nav( $args = array() ){
	
	if ( ! get_option( 'page_comments' ) ) return;
	
	$defaults = apply_filters( 'franz_comments_nav_defaults', array(
		'current'			=> max( 1, get_query_var('cpage') ),
		'total'				=> get_comment_pages_count(),
		'base'				=> add_query_arg( 'cpage', '%#%' ),
		'format'			=> '',
		'add_fragment' 		=> '#comments',
		'prev_text'			=> __( '&laquo; Prev', 'franz-josef' ),
		'next_text' 		=> __( 'Next &raquo;', 'franz-josef' ),
		'type'				=> 'comment',
	) );
	$args = wp_parse_args( $args, $defaults );
	franz_posts_nav( $args );
}
endif;


/**
 * Add pagination links in pages
 */
function franz_link_pages(){
	$args = array(
		'before'           => '<div class="page-links"><h4 class="section-title-sm">' . __( 'Pages:', 'franz-josef' ) . '</h4><ul class="pagination"><li><span class="page-numbers">',
		'after'            => '</span></li></ul></div>',
		'link_before'      => '',
		'link_after'       => '',
		'next_or_number'   => 'number',
		'separator'        => '</span></li><li><span class="page-numbers">',
		'pagelink'         => '%',
		'echo'             => 0
	); 
	$pages_link = wp_link_pages( apply_filters( 'franz_link_pages_args', $args ) );
	
	$pages_link = explode( '</li>', $pages_link );
	foreach ( $pages_link as $i => $link ) {
		if ( stripos( $link, '<a ' ) === false ) {
			$pages_link[$i] = str_replace( '<li', '<li class="active"', $link );
			break;
		}
	}
	echo implode( '</li>', $pages_link );
}


/**
* Override the output of the submit button on forms, useful for
* adding custom classes or other attributes.
*
* @param string $button An HTML string of the default button
* @param array $form An array of form data
* @return string $button
*
* @filter gform_submit_button
*/
function franz_gform_submit_button( $button, $form ) {
	$button = sprintf(
		'<input type="submit" class="btn btn-lg btn-default" id="gform_submit_button_%d" value="%s">',
		absint( $form['id'] ),
		esc_attr( $form['button']['text'] )
	);
	 
	return $button;
}
add_filter( 'gform_submit_button', 'franz_gform_submit_button', 10, 2 );


/**
 * Allows post queries to sort the results by the order specified in the post__in parameter. 
 * Just set the orderby parameter to post__in!
 *
 * Based on the Sort Query by Post In plugin by Jake Goldman (http://www.get10up.com)
*/
function franz_sort_query_by_post_in( $sortby, $thequery ) {
	global $wpdb;
	if ( ! empty( $thequery->query['post__in'] ) && isset( $thequery->query['orderby'] ) && $thequery->query['orderby'] == 'post__in' )
		$sortby = "find_in_set(" . $wpdb->prefix . "posts.ID, '" . implode( ',', $thequery->query['post__in'] ) . "')";
	
	return $sortby;
}
add_filter( 'posts_orderby', 'franz_sort_query_by_post_in', 9999, 2 );


if ( ! function_exists( 'franz_single_author_bio' ) ) :
/**
 * Print out author's bio
 */
function franz_single_author_bio(){
	global $franz_settings, $post;
	if ( ! is_singular() || ( $franz_settings['hide_author_bio'] && ! franz_has_custom_layout() ) ) return;
	?>
    <div class="entry-author">
        <div class="row">
            <div class="author-avatar col-sm-2">
            	<a href="<?php $author_id = $post->post_author; echo esc_url( get_author_posts_url( $author_id ) ); ?>" rel="author">
					<?php echo get_avatar( $author_id, 125 ); ?>
                </a>
            </div>
            <div class="author-bio col-sm-10">
                <h3 class="section-title-sm"><?php echo get_the_author_meta( 'display_name', $author_id ); ?></h3>
                <?php echo wpautop( get_the_author_meta( 'description', $author_id ) ); franz_author_social_links( $author_id ); ?>
            </div>
        </div>
    </div>
    <?php
}
endif;


if ( ! function_exists( 'franz_prev_next_posts' ) ) :
/**
 * Display links to the previous and next post
 */
function franz_prev_next_posts( $args = array() ){
	global $franz_settings;
	$defaults = array(
		'in_same_term'	=> $franz_settings['adjacent_posts_same_term'],
		'excluded_terms'=> '',
		'taxonomy'		=> 'category'
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	
	/* Exclude categories used for sliders */
	if ( $franz_settings['slider_type'] == 'categories' && $franz_settings['slider_exclude_categories'] == 'everywhere' && $franz_settings['slider_specific_categories'] ) {
		$excluded_terms .= implode( ',', $franz_settings['slider_specific_categories'] );
	}
	
	$prev_post = apply_filters( 'franz_prev_post', get_previous_post( $in_same_term, $excluded_terms, $taxonomy ) );
	$next_post = apply_filters( 'franz_next_post', get_next_post( $in_same_term, $excluded_terms, $taxonomy ) );
	
	if ( ! $prev_post && ! $next_post ) return;
	?>
    <div class="prev-next-posts well">
    	<div class="row">
            <div class="col-sm-6 prev-post">
                <?php if ( $prev_post ) :	?>
                    <h3 class="section-title-sm"><i class="fa fa-chevron-circle-left"></i> <?php _e( 'Previous', 'franz-josef' ); ?></h3>
                    <h4><?php echo get_the_title( $prev_post->ID ); ?></h4>
                    <?php echo wpautop( $prev_post->post_excerpt ); ?>
                    <a href="<?php echo get_permalink( $prev_post->ID ); ?>" class="post-link">&nbsp;</a>
                <?php endif; ?>
            </div>
    
            <div class="col-sm-6 next-post">
                <?php if ( $next_post ) :	?>
                    <h3 class="section-title-sm"><?php _e( 'Next', 'franz-josef' ); ?> <i class="fa fa-chevron-circle-right"></i></h3>
                    <h4><?php echo get_the_title( $next_post->ID ); ?></h4>
                    <?php echo wpautop( $next_post->post_excerpt ); ?>
                    <a href="<?php echo get_permalink( $next_post->ID ); ?>" class="post-link">&nbsp;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
endif;


if ( ! function_exists( 'franz_author_avatar' ) ) :
/**
 * Print out post author's avatar
 */
function franz_author_avatar(){
	global $franz_settings;
	if ( $franz_settings['hide_author_avatar'] ) return;
	?>
    <p class="entry-author-avatar">
        <a href="<?php $author_id = get_the_author_meta( 'ID' ); echo esc_url( get_author_posts_url( $author_id ) ); ?>" rel="author">
            <?php echo get_avatar( $author_id, 50 ); ?>
        </a>
    </p>
    <?php
}
endif;


/**
 * Improves the WordPress default excerpt output. This function will retain HTML tags inside the excerpt.
 * Based on codes by Aaron Russell at http://www.aaronrussell.co.uk/blog/improving-wordpress-the_excerpt/
*/
function franz_improved_excerpt( $text ){
	global $franz_settings, $post;
	
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content( '' );
		$text = strip_shortcodes( $text );
		$text = apply_filters( 'the_content', $text);
		$text = str_replace( ']]>', ']]&gt;', $text);
		
		/* Remove unwanted JS code */
		$text = preg_replace( '@<script[^>]*?>.*?</script>@si', '', $text);
		
		/* Strip HTML tags, but allow certain tags */
		$text = strip_tags( $text, $franz_settings['excerpt_html_tags'] );

		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count( $words) > $excerpt_length ) {
			array_pop( $words);
			$text = implode( ' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode( ' ', $words);
		}
	}
	
	// Try to balance the HTML tags
	$text = force_balance_tags( $text );
	
	return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt);
}


/**
 * Only use the custom excerpt trimming function if user decides to retain html tags.
*/
function franz_excerpts_filter(){
	global $franz_settings;
	
	if ( $franz_settings['excerpt_html_tags'] ) {
		remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
		add_filter( 'get_the_excerpt', 'franz_improved_excerpt' );
	}
}
add_action( 'template_redirect', 'franz_excerpts_filter' );


/**
 * Remove additional padding from captioned images
 */
function franz_cleaner_caption( $output, $attr, $content ) {

	/* We're not worried abut captions in feeds, so just return the output here. */
	if ( is_feed() ) return $output;

	/* Set up the default arguments. */
	$defaults = array(
		'id' 		=> '',
		'align' 	=> 'alignnone',
		'width' 	=> '',
		'caption' 	=> ''
	);

	/* Merge the defaults with user input. */
	$attr = shortcode_atts( $defaults, $attr );

	/* If the width is less than 1 or there is no caption, return the content wrapped between the [caption]< tags. */
	if ( 1 > $attr['width'] || empty( $attr['caption'] ) ) return $content;

	/* Set up the attributes for the caption <div>. */
	$attributes = ( !empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '' );
	$attributes .= ' class="wp-caption ' . esc_attr( $attr['align'] ) . '"';
	$attributes .= ' style="width: ' . esc_attr( $attr['width'] ) . 'px"';

	/* Open the caption <div>. */
	$output = '<div' . $attributes .'>';

	/* Allow shortcodes for the content the caption was created for. */
	$output .= do_shortcode( $content );

	/* Append the caption text. */
	$output .= '<p class="wp-caption-text">' . $attr['caption'] . '</p>';

	/* Close the caption </div>. */
	$output .= '</div>';

	/* Return the formatted, clean caption. */
	return $output;
}
add_filter( 'img_caption_shortcode', 'franz_cleaner_caption', 10, 3 );


if ( ! function_exists( 'franz_has_custom_layout' ) ) :
/**
 * Check if current post has custom page layout
 *
 * @return boolean
 */
function franz_has_custom_layout() {
	
	/* If this function has not been declared, it means Stacks addon has not been installed */
    return false;
}
endif;


/**
 * Output the custom layout if the current post has one
 *
 * @return boolean
 */
function franz_do_custom_layout() {
    if ( $custom_layout = franz_has_custom_layout() ) {
    	if ( is_numeric( $custom_layout ) ) echo apply_filters( 'the_content', siteorigin_panels_render( $custom_layout ) );
    	else the_content();
		
		get_footer();
		exit();
	}
}
add_action( 'franz_before_content', 'franz_do_custom_layout', 1000 );


if ( ! function_exists( 'franz_get_archive_post_embed' ) ) :
/**
 * Check if the post has embedded videos and return the embed code if there is
 */
function franz_get_archive_post_embed( $post_id = '' ){

	global $franz_settings;
	$embed_code = false;

	if ( $franz_settings['disable_archive_video'] ) return false;

	/* Make sure we have a valid post ID */
	if ( ! $post_id ) {
		global $post;
		$post_id = ( $post->ID ) ? $post->ID : get_the_ID();
		if ( ! $post_id ) return false;
	}

	/* Check if there is auto-embed item in the post */
	$post_meta = get_post_custom( $post_id );
	foreach ( $post_meta as $key => $meta ) {
	    if ( stripos( $key, '_oembed_' ) === 0 && strlen( $key ) == 40 ) {
			if ( trim( $meta[0] ) == '{{unknown}}' ) continue;
	        $embed_code = $meta[0];
	        break;
	    }
	}

	return apply_filters( 'franz_get_archive_post_embed', $embed_code, $post_id );
}
endif;


/**
 * Check if there is usable image in the post
 */
function franz_has_post_image( $post_id = '' ){
	/* Get the post ID if not provided */
	if ( ! $post_id ) $post_id = get_the_ID();
	
	if ( has_post_thumbnail( $post_id ) ) return true;
	if ( get_attached_media( 'image', $post_id ) ) return true;
	if ( get_post_gallery( $post_id, false ) ) return true;
	
	return false;
}


/**
 * Get the best available post image based on requested size
 */
function franz_get_post_image( $size = 'thumbnail', $post_id = '' ){
	
	/* Get the requested dimension */
	$size = apply_filters( 'franz_post_image_size', $size, $post_id );
	if ( ! is_array( $size ) ) {
		$dimension = franz_get_image_sizes( $size );
		if ( $dimension ) {
			$width = $dimension['width'];
			$height = $dimension['height'];
		} else {
			$width = 1140;
			$height = 650;
		}
	} else {
		$width = $size[0];
		$height = $size[1];
	}
	
	/* Get the post ID if not provided */
	if ( ! $post_id ) $post_id = get_the_ID();
	
	/* Get and return the cached result if available */
	$cached_images = get_post_meta( $post_id, '_franz_post_images', true );
	if ( $cached_images ) {
		if ( array_key_exists( $width . 'x' . $height, $cached_images ) ) return $cached_images[$width . 'x' . $height];
	} else {
		$cached_images = array();
	}
	
	$images = array();
	$image_ids = array();
	
	/* Check if the post has a featured image */
	if ( has_post_thumbnail( $post_id ) ) {
		$image_id = get_post_thumbnail_id( $post_id );
		$image = wp_get_attachment_image_src( $image_id, $size );
		if ( $image ) {
			$images[] = array(
				'id'			=> $image_id,
				'featured'		=> true,
				'url'			=> $image[0],
				'width'			=> $image[1],
				'height'		=> $image[2],
				'aspect_ratio'	=> $image[1] / $image[2]
			);
			$image_ids[] = $image_id;
		}
	}
	
	/* Get other images uploaded to the post */
	$media = get_attached_media( 'image', $post_id );
	if ( $media ) {
		foreach ( $media as $image ) {
			$image_id = $image->ID;
			$image = wp_get_attachment_image_src( $image_id, $size );
			if ( $image ) {
				$images[] = array(
					'id'			=> $image_id,
					'featured'		=> false,
					'url'			=> $image[0],
					'width'			=> $image[1],
					'height'		=> $image[2],
					'aspect_ratio'	=> $image[1] / $image[2]
				);
				$image_ids[] = $image_id;
			}
		}
	}
	
	/* Get the images from galleries in the post */
	$galleries = get_post_galleries( $post_id, false );
	if ( $galleries ) {
		foreach ( $galleries as $gallery ) {
			if ( ! array_key_exists( 'ids', $gallery ) ) continue;
			$gallery_images = explode( ',', $gallery['ids'] );
			foreach ( $gallery_images as $image_id ) {
				if ( in_array( $image_id, $image_ids ) ) continue;
				$image = wp_get_attachment_image_src( $image_id, $size );
				if ( $image ) {
					$images[] = array(
						'id'			=> $image_id,
						'featured'		=> false,
						'url'			=> $image[0],
						'width'			=> $image[1],
						'height'		=> $image[2],
						'aspect_ratio'	=> $image[1] / $image[2]
					);
					$image_ids[] = $image_id;
				} 
			}
		}
	}
	
	/* Score the images for best match to the requested size */
	$weight = array(
		'dimension'		=> 1.5,
		'aspect_ratio'	=> 1,
		'featured_image'=> 1
	);
	$target_aspect = $width / $height;
	
	foreach ( $images as $key => $image ) {
		
		$score = 0.0;
		
		/* Aspect ratio */
		if ( $image['aspect_ratio'] > $target_aspect ) $score += ( $target_aspect / $image['aspect_ratio'] ) * $weight['aspect_ratio'];
		else $score += ( $image['aspect_ratio'] / $target_aspect ) * $weight['aspect_ratio'];
		
		/* Dimension: ( width ratio + height ratio ) / 2 */
		$dim_score = min( array( ( $image['width'] / $width ), 1 ) ) + min( array( ( $image['height'] / $height ), 1 ) );
		$score += ( $dim_score / 2 ) * $weight['dimension'];
		
		/* Featured image */
		if ( $image['featured'] ) $score += $weight['featured_image'];
		
		$images[$key]['score'] = $score;
	}
	
	/* Sort the images based on the score */
	usort( $images, 'franz_sort_array_key_score' );
	
	$images = apply_filters( 'franz_get_post_image', $images, $size, $post_id );
	
	if ( $images ) {
		$cached_images = array_merge( $cached_images, array( $width . 'x' . $height => $images[0] ) );
		update_post_meta( $post_id, '_franz_post_images', $cached_images );
		return $images[0];
	} else {
		$cached_images = array_merge( $cached_images, array( $width . 'x' . $height => false ) );
		update_post_meta( $post_id, '_franz_post_images', $cached_images );
		return false;
	}
}


/**
 * Clear the post image cache when post is updated
 */
function franz_clear_post_image_cache( $post_id ){
	if ( wp_is_post_revision( $post_id ) ) return;
	
	delete_post_meta( $post_id, '_franz_post_images' );
}
add_action( 'save_post', 'franz_clear_post_image_cache' );


/**
 * Display the post's image
 */
function franz_the_post_image( $size = 'post-thumbnail', $attr = '' ) {
	$post_id = get_the_ID();
	$image = franz_get_post_image( $size, $post_id );

	if ( $image ) {

		do_action( 'begin_fetch_post_thumbnail_html', $post_id, $image['id'], $size );
		$html = wp_get_attachment_image( $image['id'], $size, false, $attr );
		do_action( 'end_fetch_post_thumbnail_html', $post_id, $image['id'], $size );

	} else {
		$html = '';
	}

	echo apply_filters( 'post_thumbnail_html', $html, $post_id, $image['id'], $size, $attr );
}


if ( ! function_exists( 'franz_featured_image' ) ) :
/**
 * Display the featured image in single post pages
 */
function franz_featured_image( $force = false ) {
	global $franz_settings;
	
	$has_featured_image = true;
	if ( $franz_settings['hide_featured_image'] && ! $force ) $has_featured_image = false;
	if ( ! has_post_thumbnail() ) $has_featured_image = false;
	else {
		/* Check if featured image size is at least as wide as the content area width */
		global $content_width;
		$featured_image_id = get_post_thumbnail_id();
		$featured_image = wp_get_attachment_metadata( $featured_image_id );
		if ( $featured_image['width'] < $content_width ) $has_featured_image = false;
	}

	if ( $has_featured_image ) :
?>
	<div class="featured-image">
		<?php the_post_thumbnail(); ?>
		<?php 
			/* Featured image caption */
			$featured_image = get_post( $featured_image_id );
			if ( $featured_image->post_excerpt ) { 
		?>
			<div class="caption"><i class="fa fa-camera"></i> <?php echo $featured_image->post_excerpt; ?></div>
		<?php } 
			do_action( 'franz_featured_image' );
		?>
	</div>
	<?php endif;
}
endif;