<!DOCTYPE html><?php global $franz_settings; ?>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
    <head>
        <meta charset="<?php esc_attr( bloginfo( 'charset' ) ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
    	<?php do_action( 'franz_body_start' ); ?>
    
    	<?php if ( ! $franz_settings['disable_top_bar'] ) : ?>
    	<!-- Top Bar -->
        <div class="top-bar">
        	<div class="container clearfix">
            	<div class="row">
                	<?php 
						if ( ! dynamic_sidebar( 'top-bar' ) ) {
							the_widget( 'Franz_Top_Bar_Text', array( 'width' => 4, 'text_align' => 'left', 'text' => get_bloginfo( 'description' ), 'filter' => true ) );
							the_widget( 'Franz_Top_Bar_Menu', array( 'width' => 4, 'text_align' => 'center' ) );
							the_widget( 'Franz_Top_Bar_Social', array( 'width' => 4, 'text_align' => 'right' ) );
						}
					?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
    	<!-- Header -->
    	<div class="navbar yamm navbar-inverse<?php if ( $franz_settings['disable_top_bar'] ) echo ' navbar-fixed-top'; ?>">
            
            <div class="header container">
                <div class="navbar-header logo">
                	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-menu-wrapper">
                        <span class="sr-only"><?php _e( 'Toggle navigation', 'franz-josef' ); ?></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php $tag = ( is_front_page() ) ? 'h1' : 'h2'; ?>
                    <<?php echo $tag; ?> class="site-title"><a href="<?php echo home_url(); ?>">
                    	<?php franz_header_image(); ?>
                    </a></<?php echo $tag; ?>>
                </div>
                <div class="collapse navbar-collapse" id="header-menu-wrapper">
	                <?php do_action( 'franz_before_header_menu' ); ?>
                	<?php 
						/* Header menu */
						$args = array(
							'theme_location'=> 'header-menu',
							'container'     => false,
							'menu_class'    => 'nav navbar-nav flip',
							'echo'          => true,
							'fallback_cb'   => 'franz_page_menu',
							'items_wrap'    => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							'depth'         => 10,
							'walker'		=> new FJ_Walker_Page()
						);
						if ( has_nav_menu( $args['theme_location'] ) ) $args['walker'] = new FJ_Walker_Nav_Menu();
						
						wp_nav_menu( apply_filters( 'franz_header_menu_args', $args ) );
					?>
                    <?php do_action( 'franz_header_menu' ); ?>
                </div>
            </div>
        </div>
        
        <?php do_action( 'franz_before_content' ); ?>