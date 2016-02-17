<?php $format = get_post_format(); ?>
<div <?php post_class( 'row author-post' ); ?> id="entry-<?php the_ID(); ?>">
	<?php if ( ! in_array( $format, array( 'quote' ) ) ) : ?>
        <div class="entry-image col-md-5 pull-right flip">
            <a href="<?php the_permalink(); ?>"><?php franz_the_post_image( 'franz-medium' ); ?></a>
            <?php do_action( 'franz_loop_thumbnail' ); ?>
        </div>
        <div class="col-md-7">
            <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php franz_author_entry_meta(); ?>
            <div class="excerpt">
                <?php the_excerpt(); ?>
            </div>
            <p class="more-link"><a href="<?php the_permalink(); ?>"><?php _e( 'Read more...', 'franz-josef' ); ?></a></p>
        </div>
        <?php do_action( 'franz_loop_author' ); ?>
    <?php elseif ( $format == 'quote' ) : ?>
    	<div class="col-md-12">
            <?php the_content(); franz_author_entry_meta(); ?>
        </div>
        <?php do_action( 'franz_loop_author_format_quote' ); ?>
    <?php endif; ?>
</div>