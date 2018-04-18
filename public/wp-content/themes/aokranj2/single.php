<?php
/**
 * The template for displaying all single posts.
 *
 * @package aokranj
 */

get_header();
?>

<div class="wrapper border-bottom" id="single-wrapper">

	<div class="container" id="content" tabindex="-1">

		<main class="site-main" id="main">

			<?php while (have_posts()): the_post(); ?>

				<?php get_template_part('templates/content/content', 'single'); ?>

				<?php /* aokranj_post_nav(); */ ?>

			<?php endwhile; ?>

		</main>

	</div>

</div>

<?php get_footer(); ?>
