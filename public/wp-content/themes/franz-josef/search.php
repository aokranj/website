<?php get_header(); global $wp_query; ?>

	<div class="container main">
    	<div class="row">
        	<div class="main col-md-9">
            	<?php do_action( 'franz_search_top' ); ?>
                
            	<h1 class="section-title-lg"><?php _e( 'Search Results', 'franz-josef' ); ?></h1>
            	<?php if ( have_posts() ) : ?>
                	<div class="alert alert-info">
                    	<p>
						<?php printf( _n( '%d result found matching the keyword <strong>"%s"</strong>.', '%d results found matching the keyword <strong>"%s"</strong>.', $wp_query->found_posts, 'franz-josef' ), $wp_query->found_posts, get_search_query() ); ?>
                        </p>
                    </div>
                    
                    <div class="entries-wrapper row">
                    <?php
						while ( have_posts() ){
							the_post();
							get_template_part( 'loop', 'search' );
						}
					?>
                    </div>
                    <?php franz_posts_nav( array( 'base' => add_query_arg( 'paged', '%#%' ), 'format' => '?paged=%#%' ) ); ?>
				<?php else : ?>
                    <div class="alert alert-warning">
                    	<p><?php printf( __( 'No result found matching the keyword <strong>"%s"</strong>. Try a different keyword?', 'franz-josef' ), get_search_query() ); ?></p>
                    </div>
                    <?php get_search_form(); ?>
                <?php endif; ?>
                
                <?php do_action( 'franz_search_bottom' ); ?>
            </div>
            
            <?php get_sidebar(); ?>
            
        </div>
    </div>

<?php get_footer(); ?>