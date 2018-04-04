<?php global $franz_settings; ?>
<div <?php post_class(); ?> id="entry-<?php the_ID(); ?>">
    <h1 class="entry-title"><?php the_title(); ?></h1>
    
	<?php franz_featured_image(); ?>
    
    <div class="entry-content clearfix">
    	<?php 
			the_content();
			franz_link_pages();
		?>
    </div>
    
    <?php 
		franz_related_posts();
		comments_template(); 
	?>
    <?php do_action( 'franz_loop_page' ); ?>
</div>