<?php function franz_options_about() { ?>               
	
    <p><?php _e( '<strong>Thank you for using the Franz Josef theme!</strong> Below are some useful information that you might want to know. Enjoy!', 'franz-josef' ); ?></p>
    
    <h2><?php _e( 'Where are the options?', 'franz-josef' ); ?></h2>
    <p>
		<?php 
			printf( __( 'The General and Display options have been moved to the %s for better and faster previews of your changes.', 'franz-josef' ), 
				'<a href="' .  admin_url( 'customize.php' ) . '">' . __( 'Customizer', 'franz-josef' ) . '</a>'
			);
		?>
        <br />
        <?php
			printf( __( 'The options for Mentions Bar and Social Profiles have been migrated to the free %s plugin.', 'franz-josef' ), 
				'<a href="http://www.graphene-theme.com/franz-josef/addons/additional-options/">' . __( 'Additional Options', 'franz-josef' ) . '</a>' 
			); 
		?>
    </p>
  
    <h2><?php _e( 'Recommended Plugins', 'franz-josef' ); ?></h2>
    <p><?php _e( 'This theme has been designed to work together with the following plugins. It\'s optional, but we recommend that you install them to extend the capability of the theme:', 'franz-josef' ); ?></p>
    <ol>
        <li>
            <?php printf( __( '%s for displaying related posts.', 'franz-josef' ), '<a target="_blank" href="' . admin_url( 'plugin-install.php?tab=search&s=yarpp' ) . '">Yet Another Related Posts Plugin (YARPP)</a>' ); ?>
        </li>
        <li>
            <?php printf( __( '%s for navigational breadcrumbs.', 'franz-josef' ), '<a target="_blank" href="' . admin_url( 'plugin-install.php?tab=search&s=breadcrumb-navxt' ) . '">Breadcrumb NavXT</a>' ); ?>
        </li>
        <li>
            <?php printf( __( '%s for adding various shortcodes you can use in your content.', 'franz-josef' ), '<a target="_blank" href="http://www.graphene-theme.com/?ddownload=3403">Graphene Shortcodes</a>' ); ?>
        </li>
        <li>
            <?php printf( __( '%s to ensure all images in your content maintains its sharpness on retina devices.', 'franz-josef' ), '<a target="_blank" href="https://wordpress.org/plugins/wp-retina-2x/">WP Retina 2x</a>' ); ?>
        </li>
    </ol>
    
    <h2><?php _e( 'Size and Dimensions', 'franz-josef' ); ?></h2>
    <ol>
    	<li><?php _e( 'Width of the main content area is 848px with sidebar, 1140px without sidebar.', 'franz-josef' ); ?></li>
        <li><?php _e( 'Width of the sidebar is 262px.', 'franz-josef' ); ?></li>
        <li><?php _e( 'Featured Images are 850px wide and 450px high.', 'franz-josef' ); ?></li>
    </ol>
    
    <h2><?php _e( 'Other Notes', 'franz-josef' ); ?></h2>
    <ol>
    	<li><?php _e( 'The "Header Menu" menu location supports up to 2 levels deep dropdown.', 'franz-josef' ); ?></li>
		<li><?php _e( 'The "Footer Menu" menu location does not support dropdown.', 'franz-josef' ); ?></li>
    </ol>
    
    <h2><?php _e( 'Help and Support', 'franz-josef' ); ?></h2>
    <p><?php printf( __( 'You can ask questions, request for help, provide suggestions, and report bugs at the %s.', 'franz-josef' ), '<a href="http://forum.graphene-theme.com/franz-josef/">' . __( 'Franz Josef Support Forum', 'franz-josef' ) . '</a>' ); ?></p>
    
    <h2><?php _e( 'Rate Us!', 'franz-josef' ); ?></h2>
    <p><?php printf( __( 'If you\'ve enjoyed using this theme, please rate us on %s and spread the word! We\'d really appreciate it!', 'franz-josef' ), '<a href="https://wordpress.org/themes/franz-josef/">WordPress.org</a>' ); ?></p>
    
    <div class="developer clearfix">
        <p class="alignleft" style="margin-right:20px"><a href="http://www.graphene-theme.com/"><img src="<?php echo esc_url( FRANZ_ROOTURI ); ?>/admin/images/graphene-logo.png" width="100" height="105" alt="Graphene Themes Solutions" /></a></p>
        <p><?php printf( __( 'Meticulously developed by %s.', 'franz-josef' ), '<a href="http://www.graphene-theme.com">Graphene Themes Solutions</a>' ); ?></p>
        <p style="width:500px"><?php printf( __( 'When we\'re not making awesome themes, we do custom web development for clients from more than 15 countries.', 'franz-josef' ), '<a href="http://www.graphene-theme.com">Graphene Themes Solutions</a>' ); ?></p>
        <p><a href="http://www.graphene-theme.com/about-us/"><?php _e( 'Let us work the magic for you too.', 'franz-josef' ); ?></a></p>
    </div>
    
<?php }