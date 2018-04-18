<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package aokranj
 */

?>

<section class="no-results not-found">

	<header class="entry-header">
		<h1 class="entry-title">
			<?php esc_html_e('Ni rezultatov', 'aokranj'); ?>
		</h1>
	</header>

	<div class="entry-content">
		<?php if (is_home() && current_user_can('publish_posts')): ?>
			<p>
				<?php printf(wp_kses(
					__('Ste pripravljeni na objavo prvega prispevka? <a href="%1$s">Začnite tukaj</a>.', 'aokranj'),
					['a' => ['href' => []]]
				), esc_url(admin_url('post-new.php'))); ?></p>
		<?php elseif (is_search()): ?>
			<p>
				<?php esc_html_e('Ni rezultatov iskanja. Poskusite z drugimi ključnimi besedami.', 'aokranj'); ?>
			</p>
			<?php get_search_form(); ?>
		<?php else: ?>
			<p>
				<?php esc_html_e('Stran ne obstaja. Morda lahko poskusite z iskalnikom?', 'aokranj'); ?>
			</p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>

</section>
