<?php 
	global $franz_settings; 
	
	if ( $franz_settings['tiled_posts'] ) $col = 'col-sm-6';
	else $col = 'col-md-12';
?>
<div class="item-wrap <?php echo $col; ?>">
    <div <?php post_class(); ?> id="entry-<?php the_ID(); ?>">
        
        <?php 
            $post_meta = get_post_custom();
            $has_embed = false;
            foreach ( $post_meta as $key => $meta ) {
                if ( stripos( $key, '_oembed_' ) === 0 && strlen( $key ) == 40 ) {
					if ( trim( $meta[0] ) == '{{unknown}}' ) continue;
                    $has_embed = true;
                    $embed_code = $meta[0];
                    break;
                }
            }
            
            if ( $has_embed || franz_has_post_image() ) : ?>
            
                <div class="featured-image">
                    <?php if ( $has_embed ) : echo $embed_code; else : ?>
                        <a href="<?php the_permalink(); ?>"><?php franz_the_post_image(); ?></a>
                    <?php endif; ?>
                 </div>
                <?php do_action( 'franz_loop_thumbnail' ); ?>
            
        <?php endif; ?>
        
        <div class="title-wrap">
            <div class="status-content"><?php the_content(); ?></div>
            <div class="entry-meta-wrap"><?php franz_entry_meta(); ?></div>
            <?php franz_author_avatar(); ?>
        </div>
        <?php do_action( 'franz_loop_format_status' ); ?>
    </div>
</div>