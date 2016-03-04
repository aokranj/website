<?php

/**
 * Store slider posts ids
 *
 * This is needed on the frontpage to exclude posts that are already shown in slider
 *
 * @var array
 */
$franz_sliderposts_ids = [];

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
