<?php
/**
 * Display a thank you message for users who have been using the theme for more than a month
 */
function franz_thank_you(){
	
	$days_used = get_option( 'franz_used_days' );
	if ( ! $days_used ) return;
	if ( $days_used <= 30 ) return;
	
	$current_user = wp_get_current_user();
	if ( get_user_meta( $current_user->ID, '_franz_thanks_shown' ) ) return;
	
	add_thickbox();
	?>
    <div id="thank-you-modal" style="display:none">
    	<div class="thank-you">
        	<h2><?php _e( 'Woohoo! Thanks for using Franz Josef!', 'franz-josef' ); ?></h2>
            <p><?php _e( 'We\'re glad that you\'ve chosen Franz Josef!', 'franz-josef' ); ?></p>
            <p><strong><?php _e( 'It has been a month since you started using Franz Josef.</strong> To keep making your experience better, may we suggest a few things you might be interested in:', 'franz-josef' ); ?></p>
            <ul>
            	<li><?php _e( 'Check out the <a href="http://www.graphene-theme.com/franz-josef/addons/">Franz Josef addons</a> to build amazing websites quickly and easily.', 'franz-josef' ); ?></li>
                <li><?php _e( 'Check out and <a href="http://www.graphene-theme.com/developers-blog/">subscribe to our Developer Blog</a>.', 'franz-josef' ); ?></li>
                <li><?php _e( 'Visit the Franz Josef forum to:', 'franz-josef' ); ?>
                	<ul>
                    	<li><?php _e( '<a href="http://forum.graphene-theme.com/franz-josef-feature-requests/">Suggest a feature</a> to be added to the theme.', 'franz-josef' ); ?></li>
                        <li><?php _e( '<a href="http://forum.graphene-theme.com/franz-josef-showcase/">Showcase your website</a> with us.', 'franz-josef' ); ?></li>
                        <li><?php _e( '<a href="http://forum.graphene-theme.com/franz-josef-support/">Get support</a> for any bugs you might find.', 'franz-josef' ); ?></li>
                        <li><?php _e( '<a href="http://forum.graphene-theme.com/franz-josef-support/">Help a fellow</a> Franz Josef user out.', 'franz-josef' ); ?></li>
                    </ul>
                </li>
                <li><?php _e( '<a href="https://wordpress.org/themes/franz-josef/">Give Franz Josef a rating</a> on WordPress.org', 'franz-josef' ); ?></li>
            </ul>
            <p><?php _e( 'Thanks again for choosing Franz Josef!', 'franz-josef' ); ?></p>
            <p><a class="button button-primary franz-close-thanks" href="#"><?php _e( 'Got it!', 'franz-josef' ); ?></a></p>
        </div>
    </div>
    <a class="thickbox trigger-franz-thanks" href="#TB_inline?width=500&height=500&inlineId=thank-you-modal" style="display:none"></a>
    <script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.trigger-franz-thanks').trigger('click');
			$('.franz-close-thanks').click(function(e){
				e.preventDefault();
				$('#TB_window, #TB_overlay').fadeOut();
				
				data = 'action=franz_dismiss_thanks';
				$.post(ajaxurl, data);
			});
		});
	</script>
    <?php
}


/**
 * Count the days the theme has been used
 */
function franz_count_days(){
	
	if ( mt_rand(1, 10) < 10 ) return;
	
	$days = get_option( 'franz_used_days' );
	if ( ! $days ) $days = 0;
	if ( $days > 30 ) return;
	
	$last_count = get_transient( 'franz_day_count' );
	if ( $last_count === false ) {
		$days++;
		update_option( 'franz_used_days', $days );
		set_transient( 'franz_day_count', true, 60*60*24 );
	}
	
	return;
}
add_action( 'template_redirect', 'franz_count_days' );