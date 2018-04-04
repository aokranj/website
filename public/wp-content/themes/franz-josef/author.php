<?php 
	get_header(); 
	
	$user_id = get_the_author_meta( 'ID' );
	$userdata = get_userdata( $user_id );
	$usermeta = get_user_meta( $user_id );
?>

	<div class="author-banner">
    	<?php 
			$header = FRANZ_ROOTURI . '/images/content/author-banner.jpg';
			if ( isset( $usermeta['franz_author_header_imgurl'][0] ) && $usermeta['franz_author_header_imgurl'][0] ) $header = $usermeta['franz_author_header_imgurl'][0];
			$size = getimagesize( $header ); if ( ! $size ) { $size[0] = ''; $size[1] = ''; }
		?>
    	<img src="<?php echo esc_url( $header ); ?>" alt="" width="<?php echo esc_attr( $size[0] ); ?>" height="<?php echo esc_attr( $size[1] ); ?>" />
        <?php do_action( 'franz_author_banner' ); ?>
    </div>
    
    <div class="container">
    	<div class="author-wrap col-md-8 col-md-offset-2">
        	<p class="author-avatar"><?php echo get_avatar( $user_id, 125 ); ?></p>
            <?php do_action( 'franz_author_top' ); ?>
            <div class="author-bio">
            	<h1 class="entry-title"><?php echo $userdata->display_name; ?></h1>
                <?php franz_author_details( $user_id ); franz_author_social_links( $user_id ); ?>
                
                <?php if ( $usermeta['description'][0] ) : ?>
                    <div class="bio-text">
                        <h2 class="section-title-sm"><?php _e( 'About', 'franz-josef' ); ?></h2>
                        <?php echo wpautop( $usermeta['description'][0] ); ?>
                    </div>
                <?php endif; ?>
                
				<?php do_action( 'franz_author_bio' ); ?>
            </div>
            
            <div class="author-posts author-posts">
                <h2 class="section-title-sm"><?php _e( 'Popular', 'franz-josef' ); ?></h2>
                
                <?php
                	$args = array(
						'posts_per_page' 	=> 3,
						'author' 			=> $user_id,
						'orderby' 			=> 'comment_count',
					);
					$popular_posts = new WP_Query( apply_filters( 'franz_author_popular_posts_args', $args ) );
					
					while ( $popular_posts->have_posts() ) {
						$popular_posts->the_post();
						get_template_part( 'loop', 'author' );
					}
					wp_reset_postdata();
				?>
                <?php do_action( 'franz_author_popular_posts' ); ?>
            </div>
            
            
            <?php
				$args = array(
					'user_id'	=> $user_id,
					'status'	=> 'approve'
				);
				$comments = get_comments( apply_filters( 'franz_user_comment_args', $args ) );
				
				if ( $comments ) :
				
					shuffle( $comments );
					$i = 0; $count = count( $comments );
					$comment = $comments[$i];
					
					while ( strlen( $comment->comment_content ) > 100 && $i < $count ) {
						$i++;
						$comment = $comments[$i];
					}
					
					if ( ! $comment ) {
						$comment = $comments[0];
						$comment->comment_content = franz_truncate_words( $comment->comment_content, 25 );
					}
				
			?>
                <div class="featured-comment">
                    <blockquote>
                        <?php echo wpautop( $comment->comment_content ); ?>
                        <cite><?php printf( __( 'Commented on %s.', 'franz-josef' ), '<a href="' . esc_url( get_permalink( $comment->comment_post_ID ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>' ); ?> </cite>
                    </blockquote>
                    <?php do_action( 'franz_author_featured_comment' ); ?>
                </div>
            <?php endif; ?>
            
            <div class="latest-posts author-posts">
                <h2 class="section-title-sm"><?php _e( 'Recent Posts', 'franz-josef' ); ?></h2>
                
                <div class="entries-wrapper">
					<?php while ( have_posts() ) : ?>
                    <div class="item-wrap">
                    	<?php the_post(); get_template_part( 'loop', 'author' ); ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php franz_posts_nav(); ?>
                <?php do_action( 'franz_author_posts' ); ?>
            </div>
            <?php do_action( 'franz_author_bottom' ); ?>
        </div>
    </div>

<?php get_footer(); ?>