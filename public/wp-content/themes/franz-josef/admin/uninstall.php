<?php 
/* Check authorisation */
$authorised = true;
// Check nonce
if ( ! wp_verify_nonce( $_POST['franz-uninstall'], 'franz-uninstall' ) ) { 
	$authorised = false;
}
// Check permissions
if ( ! current_user_can( 'edit_theme_options' ) ) {
	$authorised = false;
}

// If the user is authorised, delete the theme's options from the database
if ( $authorised ) {

	delete_option( 'franz_settings' );
	delete_transient( 'franz-action-hooks-list' );
	delete_transient( 'franz-action-hooks' );
	switch_theme( 'twentyfifteen', 'twentyfifteen' );
	wp_cache_flush(); ?>
	
    <div class="wrap">
    <h2><?php _e( 'Uninstall Franz Josef', 'franz-josef' ); ?></h2>
    <p><?php printf( __( 'Theme uninstalled. Redirecting to %s', 'franz-josef' ), '<a href="' . esc_url( home_url() ) . '/wp-admin/themes.php?activated=true">' . esc_url( home_url() ) . '/wp-admin/themes.php?activated=true</a>...' ); ?></p>
	<script type="text/javascript">
		window.location = '<?php echo esc_url( home_url() ); ?>/wp-admin/themes.php?activated=true';
	</script>;
	</div>
    
    <?php  
} else {
	wp_die( __( 'ERROR: You are not authorised to perform that operation', 'franz-josef' ));
}