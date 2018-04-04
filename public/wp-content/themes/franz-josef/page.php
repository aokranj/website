<?php get_header(); ?>

	<div class="container main">
    	<div class="row">
            
        	<div class="<?php franz_main_content_classes( array( 'main', 'col-md-9', 'flip' ) ); ?>">
            	<?php do_action( 'franz_page_top' ); ?>
            	<?php
					if ( have_posts() ) {
						the_post();
						get_template_part( 'loop', 'page' );
					}
				?>
                <?php do_action( 'franz_page_bottom' ); ?>
            </div>
            
            <?php get_sidebar(); ?>
            
        </div>
    </div>

<?php get_footer(); ?>