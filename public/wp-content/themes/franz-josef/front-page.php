<?php get_header(); global $franz_settings; ?>

	<?php if ( $franz_settings['enable_frontpage_sidebar'] ) : ?>
        <div class="container main">
            <div class="row">
                <div class="<?php franz_main_content_classes( array( 'main', 'col-md-9', 'flip' ) ); ?>">
    <?php endif; ?>
                    
                    <?php 
                        do_action( 'franz_front_page_top' );
                        if ( get_option( 'show_on_front' ) == 'page' ) : wp_reset_postdata(); 
                    ?>
                    <div class="highlights static-front-page">
                        <div class="<?php if ( ! $franz_settings['enable_frontpage_sidebar'] ) echo 'container'; ?>">
                            <h2 class="highlight-title"><?php the_title(); ?></h2>
                            <?php the_content(); ?>
                            <?php do_action( 'franz_front_page_content' ); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php 
                        $args = ( $franz_settings['enable_frontpage_sidebar'] ) ? array( 'full_width' => false ) : array();
                        franz_stack( 'posts', $args );
                        do_action( 'franz_front_page_bottom' );
                    ?>
                    
    <?php if ( $franz_settings['enable_frontpage_sidebar'] ) : ?>
                </div>
                <?php get_sidebar(); ?>
            </div>
        </div>
    <?php endif; ?>
    
<?php get_footer(); ?>