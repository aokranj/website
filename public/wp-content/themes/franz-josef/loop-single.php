<?php global $franz_settings; ?>
<div <?php post_class(); ?> id="entry-<?php the_ID(); ?>">
    <h1 class="entry-title"><?php the_title(); ?></h1>
    <div class="entry-meta-wrap"><?php franz_entry_meta(); ?></div>
    
    <?php franz_featured_image(); ?>
    
    <div class="entry-content clearfix">
    	<?php the_content(); ?>
    </div>
    
    <?php 
		franz_single_author_bio();
		franz_prev_next_posts();
		franz_related_posts();
		comments_template();
	?>
    <?php do_action( 'franz_loop_single' ); ?>
</div>