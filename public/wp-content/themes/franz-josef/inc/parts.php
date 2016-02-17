<?php
if ( ! function_exists( 'franz_social_links' ) ) :
/**
 * Display the social media icons
 */
function franz_social_links( $args = array() ){
	$defaults = array(
		'classes'	=> array(),
		'text_align'=> 'left'
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	
	$classes[] = 'social-links';
	$classes[] = 'text-align' . $text_align;
	
	global $franz_settings;
	
	$social_profiles = ( $franz_settings['social_profiles'][0] == false ) ? array_diff( $franz_settings['social_profiles'], array( '', false ) ) : $franz_settings['social_profiles'];
	if ( $social_profiles ) :
	?>
	<ul class="<?php echo implode( ' ', $classes ); ?>">
		<?php foreach ( $social_profiles as $social_profile ) : ?>
			<li><a href="<?php echo esc_url( $social_profile['url'] ); ?>" title="<?php echo esc_attr( $social_profile['title'] ); ?>" <?php if ( $franz_settings['social_media_new_window'] ) echo 'target="_blank"';?>>
				<?php if ( $social_profile['type'] != 'custom' ) : ?>
					<i class="fa fa-<?php echo esc_attr( $social_profile['type'] ); ?>"></i>
				<?php elseif ( $social_profile['icon_fa'] ) : ?>
					<i class="fa fa-<?php echo esc_attr( $social_profile['icon_fa'] ); ?>"></i>
				<?php else : ?>
					<img src="<?php echo esc_url( $social_profile['icon_url'] ); ?>" alt="" />
				<?php endif; ?>
			</a></li>
		<?php endforeach; ?>
		<?php do_action( 'franz_social_links' ); ?>
	</ul>
	<?php endif;
}
endif;