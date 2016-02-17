<?php
if ( ! function_exists( 'franz_comment' ) ) :
/**
 * Defines the callback function for use with wp_list_comments(). This function controls
 * how comments are displayed.
*/
function franz_comment( $comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	?>
    	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment' ); ?>>
            <div class="row">
                <div class="comment-avatar col-sm-1">
                    <?php echo get_avatar( $comment->comment_author_email, 50 ); ?>
                </div>
                <div class="comment-entry col-sm-11">
                    <div class="comment-meta-wrap row">
                        <?php franz_comment_meta( $comment ); ?>
                        <p class="comment-reply col-md-3 col-xs-12">
                        	<?php 
								$args = array(
									'depth' 		=> $depth, 
									'max_depth' 	=> $args['max_depth'], 
									'reply_text' 	=> __( 'Reply', 'franz-josef' ),
								);
								comment_reply_link( apply_filters( 'franz_comment_reply_link_args', $args ) ); 
							?>
                       	</p>
                    </div>
                    <div class="comment-content">
                    	<?php if ( $comment->comment_approved == '0' ) : ?>
						   <p><em><?php _e( 'Your comment is awaiting moderation.', 'franz-josef' ) ?></em></p>
						<?php else : comment_text(); endif; ?>
                    </div>
                </div>
            </div>
	<?php
}
endif;


/**
 * Comment meta
 */
function franz_comment_meta( $comment ){
	global $post; 
	$meta = array();
	
	$meta['attr'] = array(
		'class'	=> 'comment-attr',
		'meta'	=> sprintf( __( '%1$s on %2$s at %3$s', 'franz-josef' ), '<span class="comment-author">' . franz_comment_author_link( $comment->user_id ) . '</span>', '<span class="comment-date">' . get_comment_date(), get_comment_time() . '</span>' )
	);
	
	if ( $comment->user_id === $post->post_author ) {
		$meta['author'] = array(
			'class'	=> 'author-cred label',
			'meta'	=> __( 'Author', 'franz-josef' ),
		);
	}
	
	$meta = apply_filters( 'franz_comment_meta', $meta );
	if ( ! $meta ) return;
	?>
    <ul class="comment-meta col-md-9 col-xs-12">
        <?php foreach ( $meta as $item ) : ?>
        <li class="<?php echo esc_attr( $item['class'] ); ?>"><?php echo $item['meta']; ?></li>
        <?php endforeach; ?>
    </ul>
    <?php
}


/**
 * Modify the HTML output of the comment reply link
 */
function franz_comment_reply_link( $link, $args, $comment, $post ){
	$link = str_replace( 'comment-reply', 'btn btn-default comment-reply', $link );
	return $link;
}
add_filter( 'comment_reply_link', 'franz_comment_reply_link', 10, 4 );


/**
 * Customise the comment form
*/
function franz_comment_form_fields(){
	
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? ' aria-required="true"' : '' );
	$commenter = wp_get_current_commenter();
	
	$fields =  array( 
		'author' => '<div class="form-group col-sm-4">
						<label for="author" class="sr-only"></label>
						<input type="text" class="form-control"' . $aria_req . ' id="author" name="author" value="" placeholder="' . esc_attr__( 'Name', 'franz-josef' ) . '" />
					</div>',
		'email'  => '<div class="form-group col-sm-4">
						<label for="email" class="sr-only"></label>
						<input type="text" class="form-control"' . $aria_req . ' id="email" name="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" placeholder="' . esc_attr__( 'Email', 'franz-josef' ) . '" />
					</div>',
		'url'    => '<div class="form-group col-sm-4">
						<label for="url" class="sr-only"></label>
						<input type="text" class="form-control" id="url" name="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" placeholder="' . esc_attr__( 'Website (optional)', 'franz-josef' ) . '" />
					</div>',
	);
	
	$fields = apply_filters( 'franz_comment_form_fields', $fields );
	
	return $fields;
}


/**
 * Add some bootstrap grid layout elements
 */
function franz_bs_grid_row(){ ?><div class="row"><?php }
function franz_div_close(){ ?></div><?php }
add_action( 'comment_form_before_fields', 'franz_bs_grid_row' );
add_action( 'comment_form_after_fields', 'franz_div_close' );


/**
 * Modify default comment parameters
 */
function franz_comment_args( $defaults ){
	$args = array(
		'comment_field'	=> 	'<div class="form-group">
								<label for="comment" class="sr-only"></label>
								<textarea name="comment" class="form-control" id="comment" cols="40" rows="10" aria-required="true" placeholder="' . esc_attr__( 'Your message', 'franz-josef' ) . '"></textarea>
							</div>',
	);
	return array_merge( $defaults, $args );
}
function franz_comment_clear(){echo '<div class="clear"></div>';}
add_filter( 'comment_form_default_fields', 'franz_comment_form_fields' );
add_filter( 'comment_form_defaults', 'franz_comment_args', 5 );
add_filter( 'comment_form', 'franz_comment_clear', 1000 );


/**
 * Modify the comment submit button
 */
function franz_comment_id_fields( $result, $id, $replytoid ){
	ob_start();
	?>
    <div class="row">
        <div class="col-sm-8">
        	<?php echo $result; ?>
            <!--
            <div class="checkbox">
                <label><input type="checkbox" value="">Subscribe to future comments on this post</label>
            </div>
            -->
        </div>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-default"><?php _e( 'Post Comment', 'franz-josef' ); ?></button>
        </div>
    </div>
    <?php
	$result = ob_get_clean();
	
	return $result;
}
add_filter( 'comment_id_fields', 'franz_comment_id_fields', 1000, 3 );


if ( ! function_exists( 'franz_get_comment_count' ) ) :
/**
 * Adds the functionality to count comments by type, eg. comments, pingbacks, tracbacks.
 * Based on the code at WPCanyon (http://wpcanyon.com/tipsandtricks/get-separate-count-for-comments-trackbacks-and-pingbacks-in-wordpress/)
 * 
*/
function franz_comment_count( $type = 'comments', $oneText = '', $moreText = '' ){
	
	$result = franz_get_comment_count( $type );

    if( $result == 1  )
		return str_replace( '%', $result, $oneText );
	elseif( $result > 1 )
		return str_replace( '%', $result, $moreText );
	else
		return false;
}
endif;


if ( ! function_exists( 'franz_get_comment_count' ) ) :
/**
 * Adds the functionality to count comments by type, eg. comments, pingbacks, tracbacks. Return the number of comments, but do not print them.
 * Based on the code at WPCanyon (http://wpcanyon.com/tipsandtricks/get-separate-count-for-comments-trackbacks-and-pingbacks-in-wordpress/)
 * 
*/
function franz_get_comment_count( $type = 'comments', $only_approved_comments = true, $top_level = false ){
	if ( ! get_the_ID() ) return;
	if 		( $type == 'comments' ) 	$type_sql = 'comment_type = ""';
	elseif 	( $type == 'pings' )		$type_sql = 'comment_type != ""';
	elseif 	( $type == 'trackbacks' ) 	$type_sql = 'comment_type = "trackback"';
	elseif 	( $type == 'pingbacks' )	$type_sql = 'comment_type = "pingback"';
	
	$type_sql = apply_filters( 'franz_comments_typesql', $type_sql, $type );
	$approved_sql = $only_approved_comments ? ' AND comment_approved="1"' : '';
	$top_level_sql = ( $top_level ) ? ' AND comment_parent="0" ' : '';
        
	global $wpdb;

	$query = $wpdb->prepare( 'SELECT COUNT(comment_ID) FROM ' . $wpdb->comments . ' WHERE ' . $type_sql . $approved_sql . $top_level_sql . ' AND comment_post_ID = %d', get_the_ID() );
    $result = $wpdb->get_var( $query );
	
	return $result;
}
endif;


if ( ! function_exists( 'franz_should_show_comments' ) ) :
/**
 * Helps to determine if the comments should be shown.
 */
function franz_should_show_comments( $post_id = '' ) {
    global $franz_settings, $post;
    if ( ! $post_id && ! isset( $post ) ) return false;
	if ( ! $post_id ) $post_id = $post->ID;
	
	if ( array_key_exists( 'comments_setting', $franz_settings ) ) {
		if ( $franz_settings['comments_setting'] == 'disabled_completely' ) return false;
		if ( $franz_settings['comments_setting'] == 'disabled_pages' && get_post_type( $post_id ) == 'page' ) return false;
	}
	if ( ! comments_open( $post_id ) ) return false;
	if ( ! comments_open() && ! is_singular() && get_comments_number( $post_id ) == 0 ) return false;
	
    return true;
}
endif;


if ( ! function_exists( 'franz_moderate_comment' ) ) :
/**
 * Delete and mark spam link for comments. Show only if current user can moderate comments
 */
function franz_moderate_comment( $id ) {
	$html = '| <a class="comment-delete-link" title="' . esc_attr__( 'Delete this comment', 'franz-josef' ) . '" href="' . esc_url( get_admin_url() . 'comment.php?action=cdc&c=' . $id ) . '">' . __( 'Delete', 'franz-josef' ) . '</a>';
	$html .= '&nbsp;';
	$html .= '| <a class="comment-spam-link" title="' . esc_attr__( 'Mark this comment as spam', 'franz-josef' ) . '" href="' . esc_url( get_admin_url() . 'comment.php?action=cdc&dt=spam&c=' . $id ) . '">' . __( 'Spam', 'franz-josef' ) . '</a> |';

	if ( current_user_can( 'moderate_comments' ) ) echo $html;
}
endif;


if ( ! function_exists( 'franz_comment_author_link' ) ) :
/**
 * Display comment author's display name if author is registered
 *
 */
function franz_comment_author_link( $user_id ){
	if ( $user_id ) {
		$author = get_userdata( $user_id );
		$author_link = get_comment_author_url_link( $author->display_name, '' , '' );
	} else {
		$author_link = get_comment_author_link();
	}
	
	return apply_filters( 'franz_comment_author_link', $author_link );
}
endif;