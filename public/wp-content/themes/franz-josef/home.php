<?php get_header(); ?>

	<div class="container main">
    	<div class="row">
        	<div class="<?php franz_main_content_classes( array( 'main', 'archive', 'col-md-9', 'flip' ) ); ?>">
            	<?php do_action( 'franz_home_top' ); ?>
            	<?php if ( get_option( 'show_on_front' ) == 'page' ) : ?>
	            	<h1 class="section-title-lg"><?php echo get_the_title( get_option( 'page_for_posts' ) ); ?></h1>
                <?php endif; ?>
                
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
                <?php do_action( 'franz_home_bottom' ); ?>
            </div>
            
            <?php get_sidebar(); ?>
            
        </div>
    </div>

<?php get_footer(); ?>