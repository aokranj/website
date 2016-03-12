<?php

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
		'orderby'			=> 'title',
		'order'				=> 'ASC'
	);
	$children = new WP_Query( apply_filters( 'franz_page_navigation_args', $args ) );

	if ( $children->have_posts() ) :
	?>
        <div class="widget widget-page-navigation">
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
