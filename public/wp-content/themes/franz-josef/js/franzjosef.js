jQuery(document).ready(function($) {
	
	/* Front page slider */
	if ( ! franzJS.sliderDisable ) {
		$('.carousel').carousel({
			interval: franzJS.sliderInterval * 1000,
			pause	: 'hover'
		});
		$('.carousel .slide-title').each(function(){
			charLimit = 10;
			if ( $(this).parents('.carousel').width() > 1140 ) charLimit = 17;
			defaultSize = 5.2;
			minSize = 2.5;
			if ($(this).text().length > charLimit) {
				fontSize = (charLimit / $(this).text().length) * defaultSize;
				if (fontSize < minSize ) fontSize = minSize;
				$(this).css('font-size',  fontSize + 'vw');
			}
		});
		
		/* Fix Bootstrap Carousel not pausing on hover */
		$(document).on( 'mouseenter hover', '.carousel', function() {
			$(this).carousel( 'pause' );
		});
		$(document).on( 'mouseleave', '.carousel', function() {
			$(this).carousel( 'cycle' );
		});
	}
	
	/* Trigger Bootstrap Submenu */
	$('[data-submenu]').submenupicker();
	
	/* Make the navbar smaller as visitor scrolls down */
	$(window).scroll(function() {
		var height = $(window).scrollTop();
		var heightBreakpoint = $('.navbar').outerHeight();
		if ( franzJS.hasTopBar ) {
			var topBarHeight = $('.top-bar').outerHeight();
			if ( height > topBarHeight ) { $('.navbar').addClass('navbar-fixed-top'); $('body').addClass('navbar-fixed'); }
			else { $('.navbar').removeClass('navbar-fixed-top'); $('body').removeClass('navbar-fixed'); }
			heightBreakpoint += topBarHeight;
		}
		
		if ( height > heightBreakpoint ) { $('body').addClass('navbar-pinned'); }
		else { if ( ! $('body').hasClass('navbar-persistent-pinned') ) $('body').removeClass('navbar-pinned'); }
	});
	
	/* Changes main menu and logo to full width if they are too wide */
	if ( ( $('.header .logo').outerWidth(true) + $('.header .navbar-nav').outerWidth(true) ) > $('.header').width()) {
		$('.header .navbar-nav').removeClass('navbar-right');
		$('.header').addClass('wide-nav');
		//$('body').css('padding-top', $('.header').outerHeight() + 'px' );
	}
	
	/* Give the site title container height if a logo is used */
	if ( $(window).width() > 1200 && $('.site-title img').length > 0 ) {
		$('.site-title').css('height',  + 'px');
	}
	
	/* Go to parent link of a dropdown menu if clicked when dropdown menu is open */
	$('.dropdown-toggle[href], .dropdown-submenu > a[href]').click(function(){
		if ($(this).parent().hasClass('open') || $(this).parent().hasClass('dropdown-submenu') ) window.location = $(this).attr('href');
	});	
	
	
	/* Masonry for posts listing pages */
	if ( franzJS.isTiledPosts ) {
		$postsList = $('.tiled-posts .entries-wrapper');
		$postsList.imagesLoaded(function(){
			$postsList.masonry({
				itemSelector: '.item-wrap',
				columnWidth: 0.25
			});
		});
	}
	
	/* Masonry for front page */
	if ( franzJS.isFrontPage ) {
		$postsList = $('.posts-list .items-container');
		$postsList.imagesLoaded(function(){
			$postsList.masonry({
				itemSelector: '.item-wrap',
				columnWidth: 0.25
			});
		});
	}
	
	/* Smooth scroll to comment form */
	$('.comment-form-jump a').click(function(e){
		e.preventDefault();
		$('html, body').animate({scrollTop: $($(this).attr('href')).offset().top - 100}, 1000);
	});
	
	/* Add .label and .label-default classes to RSS widget dates */
	$('.widget_rss .rss-date').addClass('label label-default');
	
	/* Show gallery image captions when viewed on touch devices */
	var isTouchDevice = 'ontouchstart' in document.documentElement;
    if (isTouchDevice) $('.gallery-caption').css('opacity',1);
	
	/* Automatically make tables in .entry-content responsive */
	if ( ! franzJS.disableResponsiveTables ){
		$('.entry-content table:not(.non-responsive)').each(function(){
			$(this).addClass('table');
			$(this).wrap('<div class="table-responsive"></div>');
		});
	}
});