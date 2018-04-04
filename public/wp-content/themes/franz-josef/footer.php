		<?php global $franz_settings;
			
			/* Mentions Bar */
			if ( $franz_settings['mentions_bar_display'] != 'disable' && ! franz_has_custom_layout() ) {
				if ( 
					$franz_settings['mentions_bar_display'] == 'all' || 
					( $franz_settings['mentions_bar_display'] == 'front-page' && is_front_page() ) || 
					( $franz_settings['mentions_bar_display'] == 'pages' && ( is_front_page() || is_page() ) )
				) franz_stack( 'mentions-bar', array( 'items' => $franz_settings['brand_icons'] ) );
			}
		?>
        
		<?php do_action( 'franz_before_footer' ); ?>
        <div class="footer footer-inverse">
        	<?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
                <div class="footer-lg">
                    <div class="container">
                        <div class="row">
                            <?php dynamic_sidebar( 'footer-widgets' ); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( has_nav_menu( 'footer-menu' ) ) : ?>
            <div class="footer-menu-wrapper">
				<?php 
                    /* Footer menu */
                    $args = array(
                        'theme_location'=> 'footer-menu',
                        'container'     => false,
                        'menu_class'    => 'footer-menu container',
                        'echo'          => true,
                        'fallback_cb'   => false,
                        'items_wrap'    => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'depth'         => 1
                    );
                    
                    wp_nav_menu( $args );
                ?>
            </div>
            <?php endif; ?>
            
            <?php do_action( 'franz_before_bottom_bar' ); ?>
        	<div class="bottom-bar">
            	<div class="container clearfix">
                	<div class="row">
                    	<?php do_action( 'franz_bottom_bar' ); ?>
                        <div class="copyright col-xs-12 col-sm-6">
                        	<p class="copyright-text">
                            <?php 
                                global $franz_settings; 
                                if ( ! $franz_settings['hide_copyright'] ) {
                                    if ( $franz_settings['copyright_text'] ) echo wp_kses_post( $franz_settings['copyright_text'] );
                                    else printf( __( '&copy; %1$s %2$s. All rights reserved.', 'franz-josef' ), date( 'Y' ), get_bloginfo( 'name' ) );
                                    echo '<br />';
                                }
                            ?>
                            </p>
                            
                            <?php if ( ! $franz_settings['disable_credit'] ) : ?>
                            <p>
                            	<?php printf( __( 'Delicately crafted using %s and WordPress.', 'franz-josef' ), '<a href="http://www.graphene-theme.com/franz-josef/" rel="nofollow">' . __( 'Franz Josef theme', 'franz-josef' ) . '</a>' ); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <?php franz_social_links( array( 'classes' => array( 'col-xs-12 col-sm-6' ), 'text_align' => 'right' ) ); ?>
                    </div>
            	</div>
            </div>
            
        	<?php wp_footer(); ?>
        </div>
    </body>
</html>