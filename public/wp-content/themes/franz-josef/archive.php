<?php get_header(); ?>

	<div class="container main">
    	<div class="row">
        	<div class="main archive col-md-9">
            	<?php do_action( 'franz_archive_top' ); ?>
            	<?php
					$term = get_queried_object();
				
					$title = '';
					if ( is_category() ) $title = single_cat_title( '', false );
					elseif ( is_tax() ) $title = single_term_title( '', false );
					elseif ( is_day() ) $title = sprintf( __( 'Daily Archive: %s', 'franz-josef' ), '<span>' . get_the_date() . '</span>' );
					elseif ( is_month() ) {
						$title = sprintf( __( 'Monthly Archive: %s', 'franz-josef' ),
						/* translators: F will be replaced with month, and Y will be replaced with year, so "F Y" in English would be replaced with something like "June 2008". */
			            '<span>' . get_the_date( __( 'F Y', 'franz-josef' ) ) . '</span>' );
					} elseif ( is_year() ) $title = sprintf( __( 'Yearly Archive: %s', 'franz-josef' ), '<span>' . get_the_date( 'Y' ) . '</span>' );
				?>
                <div class="section-title-lg">
            		<h1><?php echo $title; ?></h1>
                    <?php if ( $term->description ) echo wpautop( $term->description ); ?>
                </div>
                
                <div class="entries-wrapper row">
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
                <?php do_action( 'franz_archive_bottom' ); ?>
            </div>
            
            <?php get_sidebar(); ?>
            
        </div>
    </div>

<?php get_footer(); ?>