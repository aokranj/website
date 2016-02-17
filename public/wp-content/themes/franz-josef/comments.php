<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to franz_comment which is
 * located in the functions.php file.
 *
 */
global $franz_settings, $is_paginated;
if ( ! franz_should_show_comments() ) return;

if ( post_password_required() && ( comments_open() || have_comments() ) ) : ?>
	<div id="comments">
        <h3 class="comments-heading"><?php _e( 'Comments', 'franz-josef' ); ?></h3>
        <p class="nopassword alert alert-info"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'franz-josef' ); ?></p>        
        <?php do_action( 'franz_protected_comment' ); ?>
    </div>
<?php return; endif; 

do_action( 'franz_before_comment_template' ); 

/* Lists all the comments for the current post */
if ( have_comments() ) :
	
	/* Get the comments and pings count */ 
	$comments_num = franz_get_comment_count();
	$allcomments_num = franz_get_comment_count( 'comments', false );
	$pings_num = franz_get_comment_count( 'pings' );
	if ( $comments_num )
		$comment_count = sprintf( _n( '%s comment', '%s comments', $comments_num, 'franz-josef' ), number_format_i18n( $comments_num ) );
	if ( $pings_num ) 
		$ping_count = sprintf( _n( '%s ping', '%s pings', $pings_num, 'franz-josef' ), number_format_i18n( $pings_num ) );
	
	$is_paginated = get_option( 'page_comments' );
	$comments_per_page = get_option( 'comments_per_page' );
?>
    <div id="comments">
    	<div class="row">
            <h3 class="comments-heading col-sm-8"><?php echo $comment_count; ?></h3>            
            <?php if ( ( ( $is_paginated && $comments_per_page > 3 ) || ! $is_paginated ) && ( $comments_num > 3 || $pings_num > 6 ) ) : ?>
                <p class="comment-form-jump col-sm-4"><a href="#respond"><?php _e( 'Skip to comment form', 'franz-josef' ); ?> <i class="fa fa-arrow-circle-o-down"></i></a></p>
            <?php endif; ?>
        </div>
    
        <?php do_action( 'franz_before_comments' ); ?>
    
        <div class="comments-list-wrapper">
            <ol class="comments-list">
                <?php
                /* Loop through and list the comments. Tell wp_list_comments()
                 * to use franz_comment() to format the comments.
                 * If you want to overload this in a child theme then you can
                 * define franz_comment() and that will be used instead.
                 * See franz_comment() in functions.php for more.
                 */
                $args = array( 
				 	'callback' 	=> 'franz_comment', 
					'style' 	=> 'ol', 
					'type' 		=> 'comment' 
				);
                wp_list_comments( apply_filters( 'franz_comments_list_args', $args ) ); ?>
            </ol>
            
            <?php franz_comments_nav(); ?>
        </div>
        
        <?php do_action( 'franz_after_comments' ); ?>
    </div>
<?php endif; // Ends the comment listing ?>

<?php /* Display comments disabled message if there's already comments, but commenting is disabled */ if ( ! comments_open() && have_comments() ) : ?>
    <div id="respond" class="comment-respond">
        <p class="alert alert-info"><?php _e( 'Comments have been closed.', 'franz-josef' ); ?></p>
        <?php do_action( 'franz_comments_closed' ); ?>
    </div>
<?php endif; ?>


<?php /* Display the comment form if comment is open */ if ( comments_open() ) : ?>
	<?php         
        $args = array(
			'id_form'				=> 'commentform',
			'label_submit'  		=> __( 'Submit Comment', 'franz-josef' ),
			'comment_notes_before' 	=> '<p  class="comment-notes">' . __( 'Your email address will not be published.', 'franz-josef' ) . '</p>',
			'comment_notes_after'	=> '',
		 );
        comment_form( apply_filters( 'franz_comment_form_args', $args ) ); 
	?>
<?php endif; ?>