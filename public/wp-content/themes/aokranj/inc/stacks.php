<?php


/**
 * Remove posts that are already shown in slider
 * @param  array $query_args
 * @return array
 */
function fix_franz_stack_posts_query_args($query_args) {
	global $franz_settings, $franz_sliderposts_ids;

	if (!$franz_settings['slider_disable']) {
		$query_args['post__not_in'] = (array)$franz_sliderposts_ids;
	}

	return $query_args;
}
//add_filter('franz_stack_posts_query_args', 'fix_franz_stack_posts_query_args');

/**
 * Override franz stack posts
 *
 * Override template
 */
function franz_stack_posts( $args = array() ){
	global $franz_settings, $franz_no_default_thumb;
	$franz_no_default_thumb = true;
	if ( 'page' == get_option( 'show_on_front' ) && $franz_settings['disable_front_page_blog'] && ! franz_has_custom_layout() ) return;

	$defaults = array(
		'title'					=> __( 'Latest Articles', 'franz-josef' ),
		'description'			=> '',
		'post_type'				=> array( 'post' ),
		'posts_per_page'		=> get_option( 'posts_per_page' ),
		'taxonomy'				=> '',
		'terms'					=> array(),
		'orderby'				=> 'date',
		'order'					=> 'DESC',
		'ignore_sticky_posts'	=> false,
		'offset'				=> '',
		'full_width'			=> true,
		'columns'				=> $franz_settings['front_page_blog_columns'],
		'lead_posts'			=> false,
		'disable_masonry'		=> false,
		'disable_nav'			=> false,
		'container_id'			=> 'posts-stack'
	);
	$args = wp_parse_args( $args, $defaults );

	/* Prepare the query args */
	$query_args = array(
		'post_type'				=> $args['post_type'],
		'posts_per_page'		=> $args['posts_per_page'],
		'orderby'				=> $args['orderby'],
		'order'					=> $args['order'],
		'ignore_sticky_posts'	=> $args['ignore_sticky_posts'],
		'paged' 				=> get_query_var( 'paged' ),
	);

	if ( $args['offset'] ) $query_args['offset'] = $args['offset'];

	if ( $args['taxonomy'] && $args['terms'] ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy'	=> $args['taxonomy'],
				'field'		=> 'term_id',
				'terms'		=> $args['terms']
			)
		);
	}

	if ( is_front_page() && get_option( 'show_on_front' ) == 'page' ) {
		$query_args['ignore_sticky_posts'] = true;
		$query_args['paged'] = get_query_var( 'page' );
	}

	if ( $args['lead_posts'] === false && is_front_page() && ! $franz_settings['disable_full_width_post'] ) $args['lead_posts'] = 1;

	if ( $franz_settings['slider_type'] == 'categories' && $franz_settings['slider_exclude_categories'] != 'disabled' ) {
		$query_args['category__not_in'] =  franz_object_id( $franz_settings['slider_specific_categories'], 'category' );
	}
	if ( $franz_settings['frontpage_posts_cats'] ) {
		$query_args['category__in'] =  franz_object_id( $franz_settings['frontpage_posts_cats'], 'category' );
	}

	/* Disable lead posts for the next pages if Infinite Scroll is turned on */
	if ( $query_args['paged'] > 0 && isset( $franz_settings['inf_scroll_disable'] ) && ! $franz_settings['inf_scroll_disable'] ) {
		$args['lead_posts'] = 0;
	}

	$posts = new WP_Query( apply_filters( 'franz_stack_posts_query_args', $query_args, $args ) );

	$classes = 'posts-list highlights';
	if ( $args['full_width'] ) $classes .= ' full-width';
	?>
	<div class="<?php echo $classes; ?>" id="<?php echo $args['container_id']; ?>">
        <div class="<?php if ( $args['full_width'] ) echo 'container'; ?>">
            <?php if ( $args['title'] ) : ?><h2 class="highlight-title"><?php echo $args['title']; ?></h2><?php endif; ?>
            <?php echo wpautop( $args['description'] ); ?>
            <div class="row items-container" data-disable-masonry="<?php echo ( $args['disable_masonry'] ) ? 1 : 0; ?>">
            	<?php
					while ( $posts->have_posts() ) :
						$posts->the_post();
						$post_id = get_the_ID();

						if ( $args['lead_posts'] && $posts->current_post < $args['lead_posts'] ) {
							$col = 'col-md-12';
							$image_size = 'full';
						} elseif ( $args['columns'] == 2 ) {
							$col = 'col-sm-6';
							$image_size = 'franz-medium';
						} elseif ( $args['columns'] == 3 ) {
							$col = 'col-sm-4';
							$image_size = 'franz-medium';
						} elseif ( $args['columns'] == 4 ) {
							$col = 'col-md-3 col-sm-6';
							$image_size = 'medium';
						}
				?>
                    <div class="item-wrap <?php echo $col; ?>" id="item-<?php echo $post_id; ?>">
                        <div <?php post_class( 'item clearfix' ); ?>>
                        	<?php if ( franz_has_post_image() ) : ?>
                            	<a href="<?php the_permalink(); ?>"><?php franz_the_post_image( $image_size ); ?></a>
                            <?php endif; ?>
                            <h3 class="item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<div class="author">
								<?php /* OVERRIDE */ ?>
                                <?php the_time( get_option( 'date_format' ) ) ?>,
                                <?php the_author_posts_link(); ?>
                            </div>
                            <div class="excerpt"><?php the_excerpt(); ?></div>
                            <?php franz_stack_posts_meta( $post_id ); ?>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <?php
				if ( ! $args['disable_nav'] ) {
					$nav_args = array(
						'current'			=> max( 1, $posts->query['paged'] ),
						'total'				=> $posts->max_num_pages,
						'add_fragment'		=> '#' . $args['container_id'],
					);
					if ( is_front_page() ) $nav_args['base'] = add_query_arg( 'paged', '%#%' );

					franz_posts_nav( apply_filters( 'franz_posts_stack_nav_args', $nav_args, $posts, $args ) );
				}
			?>

        </div>
    </div>
    <?php
}


/**
 * Override franz stack posts meta
 */
function franz_stack_posts_meta( $post_id = '' ){
	global $franz_settings;
	if ( ! $post_id ) $post_id = get_the_ID();
	$meta = array();

	/*
	if ( ! $franz_settings['hide_post_date'] ) {
		$meta['date'] = array(
			'class'	=> 'date',
			'meta'	=> '<a href="' . esc_url( get_permalink() ) . '">' . get_the_time( get_option( 'date_format' ) ) . '</a>',
		);
	}
	*/

	if ( franz_should_show_comments( $post_id ) ) {
		$comment_count = get_comment_count( $post_id );
		$approved_comment_count = $comment_count['approved'];
		$comment_text = ( $comment_count['approved'] ) ? sprintf( _n( '%d comment', '%d comments', $approved_comment_count, 'franz-josef' ), $approved_comment_count ) : __( 'Leave a reply', 'franz-josef' );
		$comments_link = ( $comment_count['approved'] ) ? get_comments_link() : str_replace( '#comments', '#respond', get_comments_link() );
		$meta['comments'] = array(
			'class'	=> 'comments-count',
			'meta'	=> '<a href="' . $comments_link . '"><i class="fa fa-comment"></i> ' . $comment_text . '</a>',
		);
	}

	$meta = apply_filters( 'franz_stack_posts_meta', $meta );
	if ( ! $meta ) return;
	?>
    	<div class="item-meta clearfix">
        	<?php foreach ( $meta as $item ) : ?>
            <p class="<?php echo esc_attr( $item['class'] ); ?>"><?php echo $item['meta']; ?></p>
            <?php endforeach; ?>
        </div>
    <?php
}
