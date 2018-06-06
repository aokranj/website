<?php
/**
 * Single post partial template.
 *
 * @package aokranj
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php if (!get_meta('post_thumbnail_title')): ?>
		<header class="entry-header">
			<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
			<?php if (get_post_type() === 'priporocila' && get_meta('position')): ?>
				<h3 class="entry-subtitle"><?= get_meta('position'); ?></h3>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
		wp_link_pages([
			'before' => '<div class="page-links">' . __('Strani:', 'aokranj'),
			'after'  => '</div>',
		]);
		?>
	</div>

	<footer class="entry-footer">
		<?php aokranj_entry_footer(); ?>
	</footer>

</article>
