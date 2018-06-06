<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package aokranj
 */

get_header();
?>

<div class="wrapper border-bottom" id="error-404-wrapper">

	<div class="container" id="content" tabindex="-1">

		<main class="site-main" id="main">

			<section class="error-404 not-found">

				<header class="page-header mb-5">
					<h1 class="page-title">
						<?php esc_html_e('Ups! Na tem mestu ni bilo mogoče najti ničesar.', 'aokranj'); ?>
					</h1>
				</header>

				<div class="page-content">

					<p class="mb-5">
						<?php esc_html_e('Morda lahko poskusite z iskalnikom?', 'aokranj'); ?>
					</p>

					<div class="row">
						<div class="col-md-6">
							<?php get_search_form(); ?>
						</div>
					</div>

				</div>

			</section>

		</main>

	</div>

</div>

<?php get_footer(); ?>
