<?php
/**
 * Add custom user fields in the user profile page
*/
function franz_show_custom_user_fields( $user ){ 
	global $current_user;
	?>
	<h3><?php _e( 'Franz Josef-specific User Profile Information', 'franz-josef' ); ?></h3>
    <p><?php _e( 'Additional user profile fields used in the Franz Josef theme.', 'franz-josef' ); ?></p>
    
    <?php do_action( 'franz_custom_user_fields_top', $user ); ?>
    <table class="form-table">
        <tr>
        	<th>
            	<label for="author_imgurl"><?php _e( 'Profile header image URL', 'franz-josef' ); ?></label><br />
            </th>
            <td>
            	<input type="text" name="franz_author_header_imgurl" id="franz_author_header_imgurl" value="<?php echo esc_attr( get_user_meta( $user->ID, 'franz_author_header_imgurl', true ) ); ?>" class="code" size="75" /><br />
                <?php /* translators: %1$s will be replaced by link to the user's profile page */ ?>
                <span class="description">
					<?php printf( __( 'Header image for your %1$s. Recommended size is 1920x350 pixels.', 'franz-josef' ), '<a href="' . get_author_posts_url( $user->ID ) . '">profile page</a>' ); ?>
                </span>
            </td>
        </tr>
        <tr>
        	<th>
            	<label for="franz_author_hide_email"><?php _e( 'Hide email', 'franz-josef' ); ?></label><br />
            </th>
            <td>
            	<input type="checkbox" name="franz_author_hide_email" id="franz_author_hide_email" value="1" <?php checked( get_user_meta( $user->ID, 'franz_author_hide_email', true ), true ); ?> /> 
				<label for="franz_author_hide_email"><?php _e( 'Remove email address from your author bio.', 'franz-josef' ); ?></label>
            </td>
        </tr>
        <tr>
        	<th>
            	<label for="franz_author_location"><?php _e( 'Current location', 'franz-josef' ); ?></label><br />
            </th>
            <td>
            	<input type="text" name="franz_author_location" id="franz_author_location" value="<?php echo esc_attr( get_user_meta( $user->ID, 'franz_author_location', true ) ); ?>" class="code" size="75" /><br />
                <span class="description">
					<?php _e( 'Will be displayed on your front-facing author profile page. Recommended to use "City, Country" format, e.g. "Lisbon, Portugal".', 'franz-josef' ); ?>
                </span>
            </td>
        </tr>
        <tr>
        	<th><?php _e( 'Social media URLs', 'franz-josef' ); ?></th>
            <td>
            	<?php 
					$social_media = array( 
						'facebook' 		=> 'Facebook', 
						'twitter'		=> 'Twitter', 
						'google-plus'	=> 'Google+', 
						'linkedin' 		=> 'LinkedIn',
						'pinterest'		=> 'Pinterest',
						'youtube'		=> 'YouTube',
						'instagram'		=> 'Instagram',
						'github'		=> 'Github'
					);
					$social_media = apply_filters( 'franz_author_social_media', $social_media );
					
					$current_values = get_user_meta( $user->ID, 'franz_author_social', true );
					foreach ( $social_media as $name => $label ) :
				?>
                    <p>
                        <label for="franz_author_social_<?php echo $name; ?>" style="display:inline-block;width:100px"><?php echo $label; ?></label>
                        <input type="text" name="franz_author_social[<?php echo $name; ?>]" id="franz_author_social_<?php echo $name; ?>" value="<?php if ( isset( $current_values[$name] ) ) echo esc_attr( $current_values[$name] ); ?>" class="code" size="75" />
                    </p>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <?php
	do_action( 'franz_custom_user_fields_bottom', $user );
}
add_action( 'show_user_profile', 'franz_show_custom_user_fields' );
add_action( 'edit_user_profile', 'franz_show_custom_user_fields' );


/**
 * Save the custom user fields
*/
function franz_save_custom_user_fields( $user_id ){
	if ( ! current_user_can( 'edit_user', $user_id ) ) return false;
	update_user_meta( $user_id, 'franz_author_header_imgurl', esc_url_raw( $_POST['franz_author_header_imgurl'] ) );
	update_user_meta( $user_id, 'franz_author_hide_email', $_POST['franz_author_hide_email'] );
	update_user_meta( $user_id, 'franz_author_location', $_POST['franz_author_location'] );
	update_user_meta( $user_id, 'franz_author_social' . $social_media, $_POST['franz_author_social'] );
	
	do_action( 'franz_save_custom_user_fields', $user_id );
}
add_action( 'personal_options_update', 'franz_save_custom_user_fields' );
add_action( 'edit_user_profile_update', 'franz_save_custom_user_fields' );