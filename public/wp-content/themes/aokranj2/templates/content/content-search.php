<?php
/**
 * Search results partial template.
 *
 * @package aokranj
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<?php the_title(sprintf(
			'<h2 class="entry-title"><a href="%s" rel="bookmark">',
				esc_url(get_permalink())),
			'</a></h2>');
		?>
		<?php /*
		<div class="entry-meta">
			<?php aokranj_posted_on(); ?>
		</div>
		*/ ?>
	</header>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>

	<footer class="entry-footer">
		<?php aokranj_entry_footer(); ?>
	</footer>

</article>
