<?php 
	global $franz_settings; 
	
	if ( $franz_settings['tiled_posts'] ) $col = 'col-sm-6';
	else $col = 'col-md-12';
?>
<div class="item-wrap <?php echo $col; ?>">
    <div <?php post_class(); ?> id="entry-<?php the_ID(); ?>">
        
        <?php 
            if ( ( $embed_code = franz_get_archive_post_embed( get_the_ID() ) ) || franz_has_post_image() ) : ?>
            
                <div class="featured-image">
                    <?php 
						if ( $embed_code ) : echo $embed_code; else : 
							global $content_width;
							$size = ( is_home() && $franz_settings['disable_blog_sidebar'] ) ? array( $content_width, 650 ) : 'post-thumbnail';
					?>
                        <a href="<?php the_permalink(); ?>"><?php franz_the_post_image( $size ); ?></a>
                    <?php endif; ?>
                 </div>
                <?php do_action( 'franz_loop_thumbnail' ); ?>
            
        <?php endif; ?>
        
        <div class="title-wrap">
            <?php if ( is_singular() ) : ?><h1 class="entry-title"><?php else : ?><h2 class="entry-title"><?php endif; ?>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            <?php if ( is_singular() ) : ?></h1><?php else : ?></h2><?php endif; ?>
            <div class="entry-meta-wrap"><?php franz_entry_meta(); ?></div>
            <?php franz_author_avatar(); do_action( 'franz_title_wrap' ); ?>
        </div>
        
         <div class="entry-content">
            <?php 
                if ( $franz_settings['archive_full_content'] ) : the_content();
                else : the_excerpt(); 
            ?>
            <p class="read-more"><a class="btn btn-lg btn-default" href="<?php the_permalink();?>"><?php _e( 'Read More', 'franz-josef' ); ?></a></p>
            <?php endif; ?>
        </div>
        
        <?php do_action( 'franz_loop' ); ?>
    </div>
</div>