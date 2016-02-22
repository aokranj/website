<?php get_header(); ?>

	<div class="container main">
    	<div class="row">
        	<div class="main col-md-9">
                <?php do_action( 'franz_index_top' ); ?>
                <div class="entries-wrapper">
                <?php 
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							franz_get_template_part( 'loop' );
						}
					}
				?>
                </div>
                <?php franz_posts_nav(); ?>
                <?php do_action( 'franz_index_bottom' ); ?>
            </div>
            
            <?php get_sidebar(); ?>
            
        </div>
    </div>

<?php get_footer(); ?>