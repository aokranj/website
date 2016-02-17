<?php 
/**
 * Export the theme's options
 */
function franz_export_options(){
    
    global $franz_settings, $franz_defaults;
    
    ob_clean();
	
	/* Check authorisation */
	$authorised = true;
	// Check nonce
	if ( ! wp_verify_nonce( $_POST['franz-export'], 'franz-export' ) ) { 
		$authorised = false;
	}
	// Check permissions
	if ( ! current_user_can( 'edit_theme_options' ) ){
		$authorised = false;
	}
	if ( $authorised ) {
    
		$is_colours = ( isset( $_POST['franz_export_colours'] ) ) ? true : false;
		$name = ( $is_colours ) ? 'franz-colour-presets.txt' : 'franz_options.txt';
		
		if ( ! $is_colours ) {
			$data = $franz_settings;
			if ( array_key_exists( 'template_dir', $data ) ) unset( $data['template_dir'] );
		} else {
			if ( empty( $_POST['presets'] ) ) wp_die( __( 'ERROR: You have not selected any colour presets to be exported.', 'franz-josef' ) );;
			$presets = $_POST['presets'];
			
			$data = array();
			foreach ( $presets as $preset ) {
				$data['colour_presets'][$preset] = $franz_settings['colour_presets'][$preset];
			}
			
			unset( $data['db_version'] );
		}
		/* Only export options that have different values than the default values - disabled for now
		foreach ( $data as $key => $value ){
			if ( $franz_defaults[$key] === $value || $value === '' ) {
				unset( $data[$key] );
			}
		}
		*/
		
		$data = json_encode( $data );
		$size = strlen( $data );
	
		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="'.$name.'"' );
		header( "Content-Transfer-Encoding: binary" );
		header( 'Accept-Ranges: bytes' );
	
		/* The three lines below basically make the download non-cacheable */
		header( "Cache-control: private" );
		header( 'Pragma: private' );
		header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	
		header( "Content-Length: " . $size);
		print( $data );
	
	} else {
		wp_die( __( 'ERROR: You are not authorised to perform that operation', 'franz-josef' ) );
	}

    die();   
}

if ( isset( $_POST['franz_export'] ) ){
	add_action( 'init', 'franz_export_options' );
}


/**
 * This file manages the theme settings uploading and import operations.
 * Uses WP_Filesystem
*/
function franz_import_form(){            
    
    $bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
    $size = size_format( $bytes, 2 );
    $upload_dir = wp_upload_dir();
    if ( ! empty( $upload_dir['error'] ) ) :
        ?><div class="error"><p><?php _e( 'Before you can upload your import file, you will need to fix the following error:', 'franz-josef' ); ?></p>
            <p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
    else :
    ?>
    <div class="wrap">
        <div id="icon-tools" class="icon32"><br></div>
        <h2><?php echo __( 'Import Franz Josef Options', 'franz-josef' );?></h2>    
        <form enctype="multipart/form-data" id="import-upload-form" method="post" action="" onsubmit="return franzCheckFile(this);">
            <p>
                <label for="upload"><?php _e( 'Choose a file from your computer:', 'franz-josef' ); ?></label> (<?php printf( __( 'Maximum size: %s', 'franz-josef' ), $size ); ?>)
                <input type="file" id="upload" name="import" size="25" />
                <input type="hidden" name="action" value="save" />
                <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
                <?php wp_nonce_field( 'franz-import', 'franz-import' ); ?>
                <input type="hidden" name="franz_import_confirmed" value="true" />
            </p>
            <button type="submit" class="button"><i class="fa fa-upload" style="font-size:16px;margin-right:5px;"></i> <?php _e( 'Upload file and import', 'franz-josef' ); ?></button>
        </form>
    </div> <!-- end wrap -->
    <?php
    endif;
} // Closes the franz_import_form() function definition


/**
 * Manages file upload and settings import
 */
function franz_import_file() {
    global $franz_settings;
    
    /* Check authorisation */
    $authorised = true;
    // Check nonce
    if ( ! wp_verify_nonce( $_POST['franz-import'], 'franz-import' ) ) {$authorised = false;}
    // Check permissions
    if ( ! current_user_can( 'edit_theme_options' ) ){ $authorised = false; }
    
    // If the user is authorised, import the theme's options to the database
    if ( $authorised) { 
		
		$is_colours = ( isset( $_POST['franz_import_colour_presets_confirmed'] ) ) ? true : false;
		$title = ( ! $is_colours ) ? __( 'Import Theme Options', 'franz-josef' ) : __( 'Import Theme Colour Presets', 'franz-josef' );
		?>
        <div class="wrap">
        <div id="icon-tools" class="icon32"><br></div>
        <h2><?php echo $title; ?></h2>
        <?php
        // make sure there is an import file uploaded
        if ( isset( $_FILES['import'] ) ) {
			
			$form_fields = array( 'import' );
			$method = '';
			
			$url = wp_nonce_url( 'themes.php?page=franz_options', 'franz-import' );
			
			// Get file writing credentials
			if (false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $form_fields ) ) ) {
				return true;
			}
			
			if ( ! WP_Filesystem( $creds ) ) {
				// our credentials were no good, ask the user for them again
				request_filesystem_credentials( $url, $method, true, false, $form_fields );
				return true;
			}
			
			// Write the file if credentials are good
			$upload_dir = wp_upload_dir();
			$filename = trailingslashit( $upload_dir['path'] ) . 'franz_options.txt';
				 
			// by this point, the $wp_filesystem global should be working, so let's use it to create a file
			global $wp_filesystem;
			if ( ! $wp_filesystem->move( $_FILES['import']['tmp_name'], $filename, true) ) {
				echo 'Error saving file!';
				return;
			}
			
			$file = $_FILES['import'];
			
			if ( $file['type'] == 'text/plain' ) {
				$data = $wp_filesystem->get_contents( $filename );
				// try to read the file
				if ( $data !== FALSE ){
					$settings = json_decode( $data, true );
					// try to read the settings array
					if ( ! $is_colours ) {
						if ( isset( $settings['db_version'] ) ) {
							$settings = array_merge( $franz_settings, $settings );
							update_option( 'franz_settings', $settings );
							echo '<p><i class="fa fa-check-circle" style="font-size:24px;vertical-align:middle;color:#7AD03A;margin-right:5px;"></i> ' . __( 'Options import completed.', 'franz-josef' ) . '</p>';
							echo '<p><a href="' . admin_url( 'themes.php?page=franz_options' ) . '">' . __( '&laquo; Return to Franz Josef Options', 'franz-josef' ) . '<a></p>';
						} else { // else: try to read the settings array
							echo '<p><strong>'.__( 'Sorry, there has been an error.', 'franz-josef' ).'</strong><br />';
							echo __( 'The uploaded file does not contain valid Franz Josef options.', 'franz-josef' ).'</p>';
						}
					} else {
						if ( isset( $settings['colour_presets'] ) ) {
							$franz_settings['colour_presets'] = array_merge( $franz_settings['colour_presets'], $settings['colour_presets'] );
							update_option( 'franz_settings', $franz_settings );
							echo '<p>' . __( 'Colour presets import completed.', 'franz-josef' ) . '</p>';
							echo '<p><a href="' . admin_url( 'themes.php?page=franz_options&tab=colours' ) . '">' . __( '&laquo; Return to Franz Josef Options', 'franz-josef' ) . '<a></p>';
						} else { // else: try to read the settings array
							echo '<p><strong>'.__( 'Sorry, there has been an error.', 'franz-josef' ).'</strong><br />';
							echo __( 'The uploaded file does not contain valid Franz Josef colour presets.', 'franz-josef' ).'</p>';
						}
					}
				} 
				else { // else: try to read the file
					echo '<p><strong>'.__( 'Sorry, there has been an error.', 'franz-josef' ).'</strong><br />';
					echo __( 'The uploaded file could not be read.', 'franz-josef' ).'</p>';
				} 
			}
			else { // else: make sure the file uploaded was a plain text file
				echo '<p><strong>'.__( 'Sorry, there has been an error.', 'franz-josef' ).'</strong><br />';
				echo __( 'The uploaded file is not supported.', 'franz-josef' ).'</p>';
			}
			
			// Delete the file after we're done
			$wp_filesystem->delete( $filename);
			
        }
        else { // else: make sure there is an import file uploaded           
            echo '<p>'.__( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'franz-josef' ).'</p>';        
        }
        echo '</div> <!-- end wrap -->';
    }
    else {
        wp_die( __( 'ERROR: You are not authorised to perform that operation', 'franz-josef' ) );            
    }           
} // Closes the franz_import_file() function definition 