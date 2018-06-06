<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package aokranj
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php if (has_post_thumbnail()): ?>
		<div class="entry-thumbnail">
			<a href="<?= esc_url(get_permalink()); ?>">
				<?php the_post_thumbnail('large', ['class' => 'img-fluid w-100']); ?>
			</a>
		</div>
	<?php endif; ?>

	<header class="entry-header">
		<?php the_title(sprintf(
			'<h2 class="entry-title"><a href="%s" rel="bookmark">',
				esc_url(get_permalink())),
			'</a></h2>');
		?>

		<?php if (get_meta('subtitle')): ?>
			<h3 class="entry-subtitle"><?= get_meta('subtitle'); ?></h3>
		<?php endif; ?>

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
