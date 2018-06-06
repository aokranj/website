<?php
/**
 * The template for displaying search results pages.
 *
 * @package aokranj
 */

get_header();
?>

<div class="wrapper border-bottom" id="search-wrapper">

	<div class="container" id="content" tabindex="-1">

		<main class="site-main" id="main">

			<?php if (have_posts()): ?>

				<header class="page-header mb-5">
					<h1 class="page-title">
						<?php printf(
							esc_html__('Rezultati iskanja za: %s', 'aokranj'),
							'<span>' . get_search_query() . '</span>');
						?>
					</h1>
				</header>

				<?php while (have_posts()): the_post(); ?>
					<?php get_template_part('templates/content/content', 'search'); ?>
				<?php endwhile; ?>

			<?php else: ?>

				<?php get_template_part('templates/content/content', 'none'); ?>

			<?php endif; ?>

		</main>

		<?php aokranj_pagination(); ?>

	</div>

</div>

<?php get_footer(); ?>
