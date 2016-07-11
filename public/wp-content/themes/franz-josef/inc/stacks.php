<?php
/**
 * Output stacks
 */
function franz_stack( $stack, $args = array() ){
	$stack = str_replace( '-', '_', $stack );
	if ( function_exists( 'franz_stack_' . $stack ) ) {
		do_action( 'franz_before_stack_' . $stack );
		call_user_func( 'franz_stack_' . $stack, apply_filters( 'franz_stack_args', $args, $stack ) );
		do_action( 'franz_after_stack_' . $stack );
	}
}


if ( ! function_exists( 'franz_stack_gallery' ) ) :
/**
 * Stack: Gallery
 */
function franz_stack_gallery( $args = array() ){
	?>
    <div class="gallery">
        <div class="container">
            <div class="row">
                <div class="item col-xs-6 col-md-4">
                    <a href="#"><img src="<?php echo FRANZ_ROOTURI; ?>/images/content/frontpage-gallery.jpg" width="370" height="270" alt="" /></a>
                </div>
                <div class="item item-title col-xs-6 col-md-4">
                    <a href="#">
                        <img src="<?php echo FRANZ_ROOTURI; ?>/images/content/frontpage-gallery.jpg" width="370" height="270" alt="" />
                        <span></span>
                        <div class="gallery-title">
                            <h3>Aotearoa Travelogue</h3>
                            <p class="gallery-date">March 24, 2014</p>
                        </div>
                    </a>
                </div>
                <div class="item col-xs-6 col-md-4">
                    <a href="#"><img src="<?php echo FRANZ_ROOTURI; ?>/images/content/frontpage-gallery.jpg" width="370" height="270" alt="" /></a>
                </div>
                <div class="item col-xs-6 col-md-4">
                    <a href="#"><img src="<?php echo FRANZ_ROOTURI; ?>/images/content/frontpage-gallery.jpg" width="370" height="270" alt="" /></a>
                </div>
                <div class="item col-xs-6 col-md-4">
                    <a href="#"><img src="<?php echo FRANZ_ROOTURI; ?>/images/content/frontpage-gallery.jpg" width="370" height="270" alt="" /></a>
                </div>
                <div class="item col-xs-6 col-md-4">
                    <a href="#"><img src="<?php echo FRANZ_ROOTURI; ?>/images/content/frontpage-gallery.jpg" width="370" height="270" alt="" /></a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
endif;


if ( ! function_exists( 'franz_stack_quote' ) ) :
/**
 * Stack: Quote
 */
function franz_stack_quote( $args = array() ){
	?>
    <div class="quote">
        <div class="container">
            <div class="row">
                <blockquote>
                    <p>"The behavior you're seeing is the behavior you've designed for."</p>
                    <cite>Joshua Porter</cite>
                </blockquote>
            </div>
        </div>
    </div>
    <?php
}
endif;


if ( ! function_exists( 'franz_stack_mentions_bar' ) ) :
/**
 * Stack: Mentions Bar
 */
function franz_stack_mentions_bar( $args = array() ){
	global $franz_settings;

	$defaults = array(
		'title'			=> '',
		'description'	=> '',
		'full_width'	=> false,
		'items'			=> array(),
		'new_tab'		=> ( isset( $franz_settings['mentions_bar_new_window'] ) ) ? $franz_settings['mentions_bar_new_window'] : false,
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	if ( ! $title && isset( $franz_settings['mentions_bar_title'] ) ) $title = $franz_settings['mentions_bar_title'];
	if ( ! $description && isset( $franz_settings['mentions_bar_desc'] ) ) $description = $franz_settings['mentions_bar_desc'];

	if ( ! $items ) return;
	?>
	<div class="mentions-bar highlights">
        <?php if ( ! $full_width ) : ?><div class="container"><?php endif; ?>
        	<?php do_action( 'franz_mentions_bar_top' ); ?>
            <?php if ( $title ) : ?><h2 class="highlight-title"><?php echo $title; ?></h2><?php endif; ?>
            <?php if ( $description ) echo '<div class="description">' . wpautop( $description ) . '</div>'; ?>
            <ul class="mentions-bar-logo">
            	<?php
				foreach ( $items as $item ) :
					$icon = wp_get_attachment_image_src( $item['image_id'], 'full' );
					$icon_meta = wp_get_attachment_metadata( $item['image_id'] );
					$alt = ( isset( $icon_meta['image_meta']['title'] ) ) ? $icon_meta['image_meta']['title'] : '';
				?>
                <li>
                	<?php if ( $item['link'] ) : ?><a href="<?php echo $item['link']; ?>" <?php if ( $new_tab ) echo 'target="_blank"'; ?>><?php endif; ?>
                    	<img src="<?php echo $icon[0]; ?>" width="<?php echo floor( $icon[1] / 2 ); ?>" height="<?php echo floor( $icon[2] / 2 ); ?>" alt="<?php echo $alt; ?>" />
                    <?php if ( $item['link'] ) : ?></a><?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php do_action( 'franz_mentions_bar_bottom' ); ?>
        <?php if ( ! $full_width ) : ?></div><?php endif; ?>
    </div>
    <?php
}
endif;


if ( ! function_exists( 'franz_stack_testimonials' ) ) :
/**
 * Stack: Testimonials
 */
function franz_stack_testimonials( $args = array() ){
	?>
	<div class="testimonial highlights">
        <div class="container">
            <h2 class="highlight-title"><?php _e( 'Testimonials', 'franz-josef' ); ?></h2>
            <p><?php _e( 'They love you!', 'franz-josef' ); ?></p>
            <div class="row">
                <div class="item col-md-6">
                    <img src="<?php echo FRANZ_ROOTURI; ?>/images/content/profile.jpg" width="125" height="126" alt="" />
                    <blockquote>
                        <p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</p>
                        <cite>
                            <span class="name">Maxi Milli</span>
                            <span class="cred">Public Relations - <a href="#">Max Mobilcom</a></span>
                        </cite>
                    </blockquote>
                </div>
                <div class="item col-md-6">
                    <img src="<?php echo FRANZ_ROOTURI; ?>/images/content/profile.jpg" width="125" height="126" alt="" />
                    <blockquote>
                        <p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</p>
                        <cite>
                            <span class="name">Maxi Milli</span>
                            <span class="cred">Public Relations - <a href="#">Max Mobilcom</a></span>
                        </cite>
                    </blockquote>
                </div>
                <div class="item col-md-6">
                    <img src="<?php echo FRANZ_ROOTURI; ?>/images/content/profile.jpg" width="125" height="126" alt="" />
                    <blockquote>
                        <p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</p>
                        <cite>
                            <span class="name">Maxi Milli</span>
                            <span class="cred">Public Relations - <a href="#">Max Mobilcom</a></span>
                        </cite>
                    </blockquote>
                </div>
                <div class="item col-md-6">
                    <img src="<?php echo FRANZ_ROOTURI; ?>/images/content/profile.jpg" width="125" height="126" alt="" />
                    <blockquote>
                        <p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus.</p>
                        <cite>
                            <span class="name">Maxi Milli</span>
                            <span class="cred">Public Relations - <a href="#">Max Mobilcom</a></span>
                        </cite>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
    <?php
}
endif;


if ( ! function_exists( 'franz_stack_posts' ) ) :
/**
 * Stack: Posts
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
endif;


if ( ! function_exists( 'franz_stack_posts_meta' ) ) :
/**
 * Item meta for Posts stack
 */
function franz_stack_posts_meta( $post_id = '' ){
	global $franz_settings;
	if ( ! $post_id ) $post_id = get_the_ID();
	$meta = array();

	if ( ! $franz_settings['hide_post_date'] ) {
		$meta['date'] = array(
			'class'	=> 'date',
			'meta'	=> '<a href="' . esc_url( get_permalink() ) . '">' . get_the_time( get_option( 'date_format' ) ) . '</a>',
		);
	}

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
endif;
