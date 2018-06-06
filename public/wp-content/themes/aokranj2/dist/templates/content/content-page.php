<?php
/**
 * Partial template for content in page.php
 *
 * @package aokranj
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
	</header>

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
		<?php edit_post_link(__('Uredi', 'aokranj'), '<span class="edit-link">', '</span>'); ?>
	</footer>

</article>
