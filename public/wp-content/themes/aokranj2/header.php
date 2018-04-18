<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package aokranj
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="manifest" href="<?= AOKRANJ_URL; ?>/manifest.json">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="application-name" content="AOKranj Junior">
	<meta name="apple-mobile-web-app-title" content="AOKranj Junior">
	<meta name="msapplication-starturl" content="/">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- header -->
<header
	class="aokranj-header wrapper-fluid wrapper-navbar <?= has_featured_image() ? 'transparent' : 'plain'; ?>"
	id="wrapper-navbar"
>

	<!-- main title -->
	<h1 class="main-title">
		<?= esc_attr(get_bloginfo('name', 'display')); ?>
	</h1>

	<!-- skip to content -->
	<a class="skip-link screen-reader-text sr-only" href="#content">
		<?php esc_html_e('Preskoči na vsebino', 'aokranj'); ?>
	</a>

	<!-- navbar -->
	<nav class="navbar navbar-expand-lg">

		<div class="container-fluid">

			<!-- title/logo -->
			<?php if (is_front_page() && is_home()): ?>
				<h1
					class="navbar-brand mb-0"
				>
					<a
						rel="home"
						href="<?= esc_url(home_url('/')); ?>"
						title="<?= esc_attr(get_bloginfo('name', 'display')); ?>"
					>
						<?php bloginfo('name'); ?>
					</a>
				</h1>
			<?php else: ?>
				<a
					class="navbar-brand"
					rel="home"
					href="<?= esc_url(home_url('/')); ?>"
					title="<?= esc_attr(get_bloginfo('name', 'display')); ?>"
				>
					<?php bloginfo('name'); ?>
				</a>
			<?php endif; ?>

			<!-- toggler -->
			<button
				class="navbar-toggler collapsed"
				type="button"
				aria-controls="main-menu"
				aria-expanded="false"
				aria-label="Toggle navigation"
			>
				<div class="navbar-toggler-icon">
					<span></span>
					<span></span>
					<span></span>
					<span></span>
				</div>
			</button>

			<!-- menu -->
			<?php wp_nav_menu([
				'theme_location'  => 'primary',
				'container_class' => 'navbar-collapse collapse',
				'container_id'    => 'main-menu',
				'menu_class'      => 'navbar-nav',
				'fallback_cb'     => '',
				'menu_id'         => 'main-menu',
				'walker'          => new AOKranjNavwalker(),
			]); ?>

		</div>

	</nav>

	<!-- language -->
	<?php /*
	<ul class="aokranj-language">
		<?php pll_the_languages([
			'display_names_as' => 'slug',
		]); ?>
	</ul>
	*/ ?>

</header>
