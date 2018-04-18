<?php
/**
 * The template for displaying the author pages.
 *
 * Learn more: https://codex.wordpress.org/Author_Templates
 *
 * @package aokranj
 */

get_header();
?>

<div class="wrapper border-bottom" id="author-wrapper">

	<div class="container" id="content" tabindex="-1">

		<main class="site-main" id="main">

			<header class="page-header author-header">
				<?php
				$curauth = (isset($_GET['author_name']))
					? get_user_by('slug',$author_name)
					: get_userdata(intval($author));
				?>
				<h1>
					<?php esc_html_e('O avtorju:', 'aokranj'); ?>
					<?= esc_html($curauth->nickname); ?>
				</h1>
				<?php if (!empty($curauth->ID)): ?>
					<?= get_avatar($curauth->ID); ?>
				<?php endif; ?>
				<dl>
					<?php if (!empty($curauth->user_url)): ?>
						<dt><?php esc_html_e('Spletna stran', 'aokranj'); ?></dt>
						<dd>
							<a href="<?= esc_url($curauth->user_url); ?>">
								<?= esc_html($curauth->user_url); ?>
							</a>
						</dd>
					<?php endif; ?>
					<?php if (!empty($curauth->user_description)): ?>
						<dt><?php esc_html_e('Profil', 'aokranj'); ?></dt>
						<dd><?= esc_html($curauth->user_description); ?></dd>
					<?php endif; ?>
				</dl>
				<h2>
					<?php esc_html_e('Objave uporabnika', 'aokranj'); ?>
					<?= esc_html($curauth->nickname); ?>:
				</h2>
			</header>

			<ul>

				<?php if (have_posts()): ?>
					<?php while (have_posts()): the_post(); ?>
						<li>
							<a rel="bookmark" href="<?php the_permalink() ?>"
							   title="Permanent Link: <?php the_title(); ?>">
								<?php the_title(); ?></a>,
							<?php aokranj_posted_on(); ?> <?php esc_html_e('v',
							'aokranj'); ?> <?php the_category('&'); ?>
						</li>
					<?php endwhile; ?>

				<?php else: ?>

					<?php get_template_part('templates/content/content', 'none'); ?>

				<?php endif; ?>

			</ul>

		</main>

		<?php aokranj_pagination(); ?>

	</div>

</div>

<?php get_footer(); ?>
