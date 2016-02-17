<?php
	global $wpdb;
	$search_term = urldecode( stripslashes( untrailingslashit( substr( $_SERVER['REQUEST_URI'], 1 ) ) ) );
	$find = array( "'.html'", "'.+/'", "'[-/_]'" ); $replace = " ";
	$search_term = trim( preg_replace( $find, $replace, $search_term ) );
	$search_term_q = esc_js( sanitize_text_field( urlencode( strip_tags( $search_term ) ) ) );
	
	$redirect_location = home_url() . '?s=' . $search_term_q;
	get_header();
?>
	<div class="container main">
    	<div class="row">
        	<div class="main col-md-9">
            	<h1 class="section-title-lg"><?php _e( 'Oops.. there\'s nothing here', 'franz-josef' ); ?></h1>
                <div class="term-description">
	            	<p><?php _e( "We couldn't find any content at this URL.", 'franz-josef' ); ?></p>
                    <div class="alert alert-info">
                    	<p><i class="fa fa-spinner fa-spin"></i> <?php printf( __( 'Searching for the keyword %s ...', 'franz-josef' ), '<strong>"' . $search_term_q . '"</strong>' ); ?></p>
                    </div>
                </div>
            </div>
            
            <?php get_sidebar(); ?>
            
        </div>
    </div>
    
    <script type="text/javascript">
		setTimeout(function(){window.location.replace( "<?php echo esc_url( $redirect_location ); ?>" );},2000);
    </script>

<?php get_footer(); ?>