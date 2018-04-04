<?php
if ( ! function_exists( 'franz_get_avatar_uri' ) ) :
/**
 * Retrieve the avatar URL for a user who provided a user ID or email address.
 *
 * @uses WordPress' get_avatar() function, except that it
 * returns the URL to the gravatar image only, without the <img> tag.
 *
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int $size Size of the avatar image
 * @param string $default URL to a default image to use if no avatar is available
 * @param string $alt Alternate text to use in image tag. Defaults to blank
 * @return string URL for the user's avatar
*/
function franz_get_avatar_uri( $id_or_email, $size = '125', $default = '', $alt = false ) {
	
	// Silently fails if < PHP 5
	if ( ! function_exists( 'simplexml_load_string' ) ) return;
	
	$avatar = get_avatar( $id_or_email, $size, $default, $alt );
	if ( ! $avatar ) return false;
	
	$avatar_xml = simplexml_load_string( $avatar );
	$attr = $avatar_xml->attributes();
	$src = $attr['src'];

	return apply_filters( 'franz_get_avatar_url', $src, $id_or_email, $size, $default, $alt );
}
endif;


if ( ! function_exists( 'franz_author_social_links' ) ) :
/**
 * Display author's social links
 */
function franz_author_social_links( $user_id ){
	$userdata = get_userdata( $user_id );
	$user_social = get_user_meta( $user_id, 'franz_author_social', true );
	
	if ( ! $userdata ) return;
	?>
    <ul class="author-social">
    	<?php if ( $user_social ) : foreach ( $user_social as $social_media => $url ) : if ( ! $url ) continue; ?>
        	<li><a href="<?php echo esc_url( $url ); ?>"><i class="fa fa-<?php echo esc_attr( $social_media ); ?>"></i></a></li>
        <?php endforeach; endif; ?>
        
		<?php if ( ! get_user_meta( $user_id, 'franz_author_hide_email', true ) ) : ?>
	        <li><a href="mailto:<?php echo esc_attr( $userdata->user_email ); ?>"><i class="fa fa-envelope-o"></i></a></li>
        <?php endif; ?>
    </ul>
    <?php
}
 endif;
 
 
 if ( ! function_exists( 'franz_author_details' ) ) :
/**
 * Display author details
 */
function franz_author_details( $user_id ){
	$user_id = get_the_author_meta( 'ID' );
	$userdata = get_userdata( $user_id );
	$usermeta = get_user_meta( $user_id );
	
	$user_details = array();
	
	if ( isset( $usermeta['franz_author_location'][0] ) && $usermeta['franz_author_location'][0] ) {
		$user_details['location'] = array(
			'class'		=> 'location',
			'detail'	=> $usermeta['franz_author_location'][0]
		);
	}
	
	if ( $userdata->user_url ) {
		$user_details['url'] = array(
			'class'		=> 'url',
			'detail'	=> '<a href="' . esc_url( $userdata->user_url ) . '">' . esc_url( $userdata->user_url ) . '</a>'
		);
	}
	
	$user_details = apply_filters( 'franz_author_details', $user_details );
	if ( $user_details ) : 
	?>
    <ul class="author-details">
    	<?php foreach ( $user_details as $user_detail ) : ?>
        <li class="<?php echo esc_attr( $user_detail['class'] ); ?>"><?php echo $user_detail['detail']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
	endif;
}
endif;