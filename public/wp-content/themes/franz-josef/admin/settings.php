<?php global $franz_settings;
/**
 * Register the settings for the theme. This is required for using the
 * WordPress Settings API.
*/
function franz_settings_init(){
    // register options set and store it in franz_settings db entry
	register_setting( 'franz_options', 'franz_settings', 'franz_settings_validator' );
}
add_action( 'admin_init', 'franz_settings_init' );


/**
 * This function generates the theme's options page in WordPress administration.
 *
 * @package Franz Josef
 * @since Franz Josef 1.0
*/
function franz_options(){
	
	global $franz_settings, $franz_defaults;
	
	/* Checks if the form has just been submitted */
	if ( ! isset( $_REQUEST['settings-updated'] ) ) { $_REQUEST['settings-updated'] = false; } 
        	
	/* Import the franz theme options */
	if ( isset( $_POST['franz_import'] ) ) { 
		franz_import_form();
		return;
	}
	
	if ( isset( $_POST['franz_import_confirmed'] ) ) {            
		franz_import_file();
		return;                           
	}
	
	/* Reset theme settings  */
	require( FRANZ_ROOTDIR . '/admin/options-presets.php');
        
        /* Uninstall the theme if confirmed */
	if ( isset( $_POST['franz_uninstall_confirmed'] ) ) { 
		include( FRANZ_ROOTDIR . '/admin/uninstall.php' );
	}       
	
	/* Display a confirmation page to uninstall the theme */
	if ( isset( $_POST['franz_uninstall'] ) ) { 
	?>

		<div class="wrap">
        <div class="icon32" id="icon-themes"><br /></div>
        <h2><?php _e( 'Uninstall Franz Josef', 'franz-josef' ); ?></h2>
        <p><?php _e("Please confirm that you would like to uninstall the Franz Josef theme. All of the theme's options in the database will be deleted.", 'franz-josef' ); ?></p>
        <p><?php _e( 'This action is not reversible.', 'franz-josef' ); ?></p>
        <form action="" method="post">
        	<?php wp_nonce_field( 'franz-uninstall', 'franz-uninstall' ); ?>
        	<input type="hidden" name="franz_uninstall_confirmed" value="true" />
            <input type="submit" class="button franz_uninstall" value="<?php _e( 'Uninstall Theme', 'franz-josef' ); ?>" />
        </form>
        </div>
        
		<?php
		return;
	}
	
	/* Get the updated settings before outputting the options page */
	$franz_settings = franz_get_settings();
	
	/* This where we start outputting the options page */ ?>
	<div class="wrap meta-box-sortables">
		<div class="icon32" id="icon-themes"><br /></div>
        <h2><?php _e( 'Franz Josef Theme Options', 'franz-josef' ); ?></h2>
        
        <p><?php _e( 'These are the global settings for the theme. You may override some of the settings in individual posts and pages.', 'franz-josef' ); ?></p>
        
		<?php settings_errors( 'franz_options' ); ?>        
        
        <?php /* Print the options tabs */ ?>
        <?php 
            if ( $_GET['page'] == 'franz_options' ) :
                $current_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'about';
                franz_options_tabs( $current_tab, $franz_settings['options_tabs'] );
            endif;
        ?>
        
        <div class="left-wrap">
        
        <?php if ( ! in_array( $current_tab, array( 'addons', 'about' ) ) ) : ?>
        <form method="post" action="options.php" class="mainform clearfix" id="franz-options-form">
		
            <?php /* Output wordpress hidden form fields, e.g. nonce etc. */ ?>
            <?php settings_fields( 'franz_options' ); ?>
        
            <?php /* Display the current tab */ ?>
            <?php franz_options_tabs_content( $current_tab ); ?>    
            
            <?php /* The form submit button */ ?>
            <p class="submit"><input type="submit" class="button-primary" id="franz-save-options" value="<?php _e( 'Save Options', 'franz-josef' ); ?>" /></p>
        
        </form>
        <?php else : franz_options_tabs_content( $current_tab ); ?>
        <?php endif; ?>
        
        <div class="franz-ajax-response"></div>
        
        <?php franz_thank_you(); ?>
        
        </div><!-- #left-wrap -->
        
        <div class="side-wrap">
        
        
        <?php /* Franz Josef theme news RSS feed */ ?>
        <div class="postbox franz-news no-toggle">
            <div>
        		<h3 class="hndle"><?php _e( 'Franz Josef news', 'franz-josef' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <?php
				$franz_news = fetch_feed( array( 'http://www.graphene-theme.com/category/franz-josef/feed/rss2/' ) );
				if ( ! is_wp_error( $franz_news ) ) :
					$maxitems = $franz_news->get_item_quantity( 3 );
					$news_items = $franz_news->get_items( 0, $maxitems );
					?>
					<ol class="franz-news-list">
						<?php if ( $maxitems == 0 ) : echo '<li>' . __( 'No news items.', 'franz-josef' ) . '</li>'; else :
						foreach( $news_items as $news_item ) : ?>
							<li>
								<a href='<?php echo esc_url( $news_item->get_permalink() ); ?>'><?php echo esc_html( $news_item->get_title() ); ?></a><br />
								<?php echo esc_html( franz_truncate_words( strip_tags( $news_item->get_description() ), 20, ' [...]' ) ); ?><br />
								<span class="news-item-date"><?php echo 'Posted on '. $news_item->get_date( 'j F Y, g:i a' ); ?></span>
							</li>
						<?php endforeach; endif; ?>
					</ol>
                <?php else : ?>
					<div class="feed-error">
                    	<?php foreach ( $franz_news->get_error_message() as $error ) : ?>
                        	<p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
            
        
        <?php /* Options Presets. This uses separate form than the main form */ ?>
        <div class="postbox preset non-essential-option">
            <div class="head-wrap">
                <div title="<?php _e( 'Click to toggle', 'franz-josef' ); ?>" class="handlediv"><br /></div>
                <h3 class="hndle"><?php _e( 'Reset settings', 'franz-josef' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <p><?php _e( 'Reset all of the theme\'s settings to their default values. Only settings accessible via Franz Josef Options are affected. Custom Header, Custom Menus, and other WordPress settings won\'t be affected.', 'franz-josef' ); ?></p>
                <p><?php _e( '<strong>WARNING:</strong> This action is not reversible.', 'franz-josef' ); ?></p>
                <form action="" method="post">
                    <?php wp_nonce_field( 'franz-preset', 'franz-preset' ); ?>
                    <input type="hidden" name="franz_options_preset" value="reset" id="franz_options_preset-reset" />
                    <input type="hidden" name="franz_preset" value="true" />
                    <input type="submit" class="button-primary franz_preset" value="<?php _e( 'Reset settings', 'franz-josef' ); ?>" />
                </form>
            </div>
        </div>
        
        
        <?php /* Theme import/export */ ?>    
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="<?php _e( 'Click to toggle', 'franz-josef' ); ?>" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Import/export theme options', 'franz-josef' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <p><strong><?php _e( 'Import', 'franz-josef' ); ?></strong></p>    
                <form action="" method="post">
                    <input type="hidden" name="franz_import" value="true" />
                    <button type="submit" class="button"><i class="fa fa-upload" style="font-size:16px;margin-right:5px;"></i> <?php _e( 'Import theme options', 'franz-josef' ); ?></button>
                </form> <br />
                <p><strong><?php _e( 'Export', 'franz-josef' ); ?></strong></p>                
                <form action="" method="post">
                	<?php wp_nonce_field( 'franz-export', 'franz-export' ); ?>
                    <input type="hidden" name="franz_export" value="true" />
                    <button type="submit" class="button"><i class="fa fa-download" style="font-size:16px;margin-right:5px;"></i> <?php _e( 'Export theme options', 'franz-josef' ); ?></button>
                </form>              
            </div>
        </div>
            
        
        <?php /* Theme's uninstall */ ?>
        <div class="postbox non-essential-option">
            <div class="head-wrap">
                <div title="<?php _e( 'Click to toggle', 'franz-josef' ); ?>" class="handlediv"><br /></div>
        		<h3 class="hndle"><?php _e( 'Uninstall theme', 'franz-josef' ); ?></h3>
            </div>
            <div class="panel-wrap inside">
                <p><?php _e("<strong>Be careful!</strong> Uninstalling the theme will remove all of the theme's options from the database. Do this only if you decide not to use the theme anymore.",'franz-josef' ); ?></p>
                <p><?php _e( 'If you just want to try another theme, there is no need to uninstall this theme. Simply activate the other theme in the Appearance &gt; Themes admin page.','franz-josef' ); ?></p>
                <p><?php _e("Note that uninstalling this theme <strong>does not remove</strong> the theme's files. To delete the files after you have uninstalled this theme, go to Appearance &gt; Themes and delete the theme from there.",'franz-josef' ); ?></p>
                <form action="" method="post">
                    <?php wp_nonce_field( 'franz-options', 'franz-options' ); ?>
                
                    <input type="hidden" name="franz_uninstall" value="true" />
                    <input type="submit" class="button-primary franz_uninstall" value="<?php _e( 'Uninstall Theme', 'franz-josef' ); ?>" />
                </form>
            </div>
        </div>
        
        
         </div><!-- #side-wrap -->
    </div><!-- #wrap -->
    
    
<?php    
} // Closes the franz_options() function definition 

include( FRANZ_ROOTDIR . '/admin/options-import.php' );


/**
 * Recommended plugins notice
 */
function franz_admin_notice_plugins() {
	global $franz_settings, $current_user; $user_id = $current_user->ID;
	$screen = get_current_screen(); if ( $screen->id != $franz_settings['hook_suffix'] ) return;

    /* Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta( $user_id, 'franz_ignore_notice-plugins' ) ) : 
	?>
        <div class="updated">
        	<p><?php _e( '<strong>Thank you for installing the Franz Josef theme!</strong> This theme has been designed to work together with the following plugins. It\'s optional, but we recommend that you install them to extend the capability of the theme:', 'franz-josef' ); ?></p>
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
            <p><a class="dismiss button" href="<?php echo add_query_arg( 'franz-dismiss-notice', 'plugins' ); ?>"><?php _e( 'OK, got it!', 'franz-josef' ); ?></a></p>
            <p><?php _e( 'PS: The information above is also available under the "About Franz Josef" tab in WP Admin > Appearance > Franz Josef Options.', 'franz-josef' ); ?></p>
        </div>
	<?php
	endif;
}
add_action( 'admin_notices', 'franz_admin_notice_plugins' );


/**
 * Notice for options migrated to Customizer
 */
function franz_admin_notice_customizer() {
	global $franz_settings, $current_user; $user_id = $current_user->ID;
	$screen = get_current_screen(); if ( $screen->id != $franz_settings['hook_suffix'] ) return;

    /* Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta( $user_id, 'franz_ignore_notice-customizer' ) ) : 
	?>
        <div class="updated">
        	<p>
            	<?php 
				echo '<strong>' . __( 'IMPORTANT:', 'franz-josef' ) . '</strong>';
				printf( __( 'The General and Display options have been moved to the %s for better and faster previews of your changes.', 'franz-josef' ), 
					'<a href="' .  admin_url( 'customize.php' ) . '">' . __( 'Customizer', 'franz-josef' ) . '</a>'
				);
				?>
                <?php
				printf( __( 'The options for Mentions Bar and Social Profiles have been migrated to the free %s plugin.', 'franz-josef' ), 
					'<a href="http://www.graphene-theme.com/franz-josef/addons/additional-options/">' . __( 'Additional Options', 'franz-josef' ) . '</a>' 
				);
				?>
            </p>
            <p><a class="dismiss button" href="<?php echo add_query_arg( 'franz-dismiss-notice', 'customizer' ); ?>"><?php _e( 'OK, got it!', 'franz-josef' ); ?></a></p>
        </div>
	<?php
	endif;
}
add_action( 'admin_notices', 'franz_admin_notice_customizer' );


function franz_dismiss_notice() {
	global $current_user; $user_id = $current_user->ID;
	
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset( $_GET['franz-dismiss-notice'] ) ) {
		 add_user_meta( $user_id, 'franz_ignore_notice-' . trim( $_GET['franz-dismiss-notice'] ), 'true', true );
	}
}
add_action( 'admin_init', 'franz_dismiss_notice' );