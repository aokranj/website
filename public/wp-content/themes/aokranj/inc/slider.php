<?php

/**
 * Store slider posts ids
 *
 * This is needed on the frontpage to exclude posts that are already shown in slider
 *
 * @var array
 */
$franz_sliderposts_ids = [];

function franz_slider_excerpt_length($length) {
	return 18;
}

function franz_slider( $args = array() ){
	global $franz_settings;

	add_filter('excerpt_length', 'franz_slider_excerpt_length');

	$defaults = array(
		'title'					=> '',
		'description'			=> '',
		'type' 					=> $franz_settings['slider_type'],
		'specific_posts' 		=> $franz_settings['slider_specific_posts'],
		'specific_categories'	=> $franz_settings['slider_specific_categories'],
		'exclude_categories'	=> $franz_settings['slider_exclude_categories'],
		'random_category_posts' => $franz_settings['slider_random_category_posts'],
		'content'				=> $franz_settings['slider_content'],
		'postcount' 			=> $franz_settings['slider_postcount'],
		'height' 				=> $franz_settings['slider_height'],
		'interval'				=> $franz_settings['slider_interval'],
		'trans_duration' 		=> $franz_settings['slider_trans_duration'],
		'id'					=> 'fj-slider',
		'layout'				=> 'full-stretched'
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	$slides = franz_get_slider_posts( $args );

	if ( $slides->have_posts() ) : $i = 0; franz_set_excerpt_length( 20 );
	?>

    <div class="highlights slider">
    <?php if ( $title ) : ?><h2 class="highlight-title"><?php echo $title; ?></h2><?php endif; ?>
	<?php if ( $description ) echo '<div class="description">' . wpautop( $description ) . '</div>'; ?>

    <!-- Carousel -->
    <div data-ride="carousel" class="carousel slide carousel-fade" id="<?php echo $id; ?>">
    	<?php do_action( 'franz_slider_outer' ); ?>
        <!-- Indicators -->
        <ol class="carousel-indicators">
        	<?php for ( $j = 0; $j < $slides->post_count; $j++ ) : ?>
            <li data-slide-to="<?php echo $j; ?>" data-target="#<?php echo $id; ?>" class="<?php if ( $j == 0 ) echo 'active'; ?>"></li>
            <?php endfor; ?>
        </ol>
        <div class="carousel-inner">
        	<?php while ( $slides->have_posts() ) :
				$slides->the_post();

				$style = '';

				/* Get the image to be used as background image */
				$image_size = ( $layout == 'full-stretched' ) ? 'franz-slider' : 'franz-slider-contained';
				$bg_image = franz_get_post_image( $image_size, get_the_ID() );
				if ( $bg_image ) $style = 'style="background-image: url(' . $bg_image['url'] . ')"';

				$style = apply_filters( 'franz_slide_style_attr', $style );
			?>
            <div class="item <?php if ( $slides->current_post == 0 ) echo 'active'; ?>" id="slide-<?php the_ID(); ?>" <?php echo $style; ?>>
                <div class="container">
                    <div class="carousel-caption">
                        <h3 class="slide-title"><?php the_title(); ?></h3>
                        <div class="excerpt">
                            <?php
								if ( $content == 'excerpt' ) the_excerpt();
								elseif ( $content == 'full_content' ) the_content();
							?>
                        </div>

                        <?php if ( $content != 'full_content' ) : ?>
                            <div class="call-to-action">
                                <p><a role="button" href="<?php the_permalink(); ?>" class="btn btn-lg btn-warning btn-sharp"><?php _e( 'View post', 'franz-josef' ); ?></a></p>
                            </div>
                        <?php endif; ?>

                        <?php do_action( 'franz_slide_content' ); ?>
                    </div>
                </div>
                <?php do_action( 'franz_slide_content_outer' ); ?>
            </div>
            <?php endwhile; ?>
        </div>
        <a data-slide="prev" role="button" href="#<?php echo $args['id']; ?>" class="left carousel-control"><span class="fa fa-chevron-left glyphicon-chevron-left"></span></a>
        <a data-slide="next" role="button" href="#<?php echo $args['id']; ?>" class="right carousel-control"><span class="fa fa-chevron-right glyphicon-chevron-right"></span></a>
    </div>
    </div>
    <?php
	endif; wp_reset_postdata(); franz_reset_excerpt_length();

	remove_filter('excerpt_length', 'franz_slider_excerpt_length');
}

/**
 * Override for franz slider posts query
 * @param  array  $args
 * @return WP_Query
 */
function franz_get_slider_posts( $args = array() ){
	global $franz_settings, $franz_slider_request, $franz_sliderposts_ids;
	$franz_slider_request = true;

	$defaults = array(
		'type' 					=> $franz_settings['slider_type'],
		'specific_posts' 		=> $franz_settings['slider_specific_posts'],
		'specific_categories'	=> $franz_settings['slider_specific_categories'],
		'random_category_posts' => $franz_settings['slider_random_category_posts'],
		'postcount' 			=> $franz_settings['slider_postcount'],
		'id'					=> ''
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	/* Set the post types to be displayed */
	$slider_post_type = ( in_array( $type, array( 'posts_pages', 'categories' ) ) ) ? array( 'post', 'page' ) : array( 'post' ) ;
	$slider_post_type = apply_filters( 'franz_slider_post_type', $slider_post_type, $args );

	$query_args = array(
		'posts_per_page'	=> $postcount,
		'orderby' 			=> 'menu_order date',
		'order' 			=> 'DESC',
		'suppress_filters' 	=> 0,
		'post_type' 		=> $slider_post_type,
		'ignore_sticky_posts' => 1, // otherwise the sticky posts show up undesired
	);

	if ( $type == 'random' ) $query_args = array_merge( $query_args, array( 'orderby' => 'rand' ) );
	else if ( $type == 'posts_pages' ) {
		$post_ids = $specific_posts;
		$post_ids = preg_split("/[\s]*[,][\s]*/", $post_ids, -1, PREG_SPLIT_NO_EMPTY); // post_ids are comma separated, the query needs a array
		$post_ids = franz_object_id( $post_ids );
		$query_args = array_merge( $query_args, array( 'post__in' => $post_ids, 'posts_per_page' => -1, 'orderby' => 'post__in' ) );
	}
	else if ( $type == 'categories' && is_array( $specific_categories ) ) {
		$cats = $specific_categories;
		$cats = franz_object_id( $cats, 'category' );
		$query_args = array_merge( $query_args, array( 'category__in' => $cats ) );

		if ( $random_category_posts ) $query_args = array_merge( $query_args, array( 'orderby' => 'rand' ) );
	}

	// OVERRIDE: add filters
	add_filter('posts_join', 'franz_get_slider_posts_join');
	add_filter('posts_groupby', 'franz_get_slider_posts_groupby');
	//add_filter('posts_where', 'franz_get_slider_posts_where');

	/* Get the posts */
	$sliderposts = new WP_Query( apply_filters( 'franz_slider_args', $query_args, $args ) );
	$franz_slider_request = false;

	// OVERRIDE: remove filters
	remove_filter('posts_join', 'franz_get_slider_posts_join');
	remove_filter('posts_groupby', 'franz_get_slider_posts_groupby');
	//remove_filter('posts_where', 'franz_get_slider_posts_where');

	// OVERRIDE: store franz slider posts ids
	$franz_sliderposts_ids = [];
	foreach($sliderposts->posts as $p) {
		$franz_sliderposts_ids[] = $p->ID;
	}

	return apply_filters( 'franz_slider_posts', $sliderposts );
}

/**
 * Restrict slider type to category utrinki
 * @param  array $query_args
 * @return array
 */
function fix_franz_slider_args($query_args) {
	$category = get_category_by_slug('utrinki');
	$query_args['cat'] = $category->cat_ID;
	return $query_args;
}
//add_filter('franz_slider_args', 'fix_franz_slider_args');

/**
 * Select only posts with attachment using INNER JOIN
 * @param  string $join
 * @return string
 */
function franz_get_slider_posts_join($join) {
	global $wpdb;
   	$join .= "INNER JOIN wp_posts p2 ON $wpdb->posts.ID = p2.post_parent AND p2.post_type = 'attachment' ";
   	return $join;
}

/**
 * Groub by for INNER JOIN
 * @param  string $groupby
 * @return string
 */
function franz_get_slider_posts_groupby($groupby) {
	global $wpdb;
    $groupby = "ID";
    return $groupby;
}

/**
 * Select only posts with attachment using WHERE
 * @param  string $join
 * @return string
 */
function franz_get_slider_posts_where($where) {
	global $wpdb;
	$where .= " AND ID IN (SELECT post_parent FROM wp_posts WHERE post_type = 'attachment' )";
	return $where;
}
