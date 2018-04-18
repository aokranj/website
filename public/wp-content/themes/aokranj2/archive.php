<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package aokranj
 */

get_header();
?>

<div class="wrapper border-bottom" id="archive-wrapper">

	<div class="container" id="content" tabindex="-1">

		<main class="site-main" id="main">

			<?php if (have_posts()): ?>

				<header class="page-header mb-5">
					<?php
					the_archive_title('<h1 class="page-title">', '</h1>');
					the_archive_description('<div class="taxonomy-description">', '</div>');
					?>
				</header>

				<?php while (have_posts()): the_post(); ?>

					<?php get_template_part('templates/content/content', get_post_format()); ?>

				<?php endwhile; ?>

			<?php else: ?>

				<?php get_template_part('templates/content/content', 'none'); ?>

			<?php endif; ?>

		</main>

		<?php aokranj_pagination(); ?>

	</div>

</div>

<?php get_footer(); ?>
