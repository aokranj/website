jQuery(document).ready(function($) {
  
  	/* Slider height */
	wp.customize('franz_settings[slider_height]', function(value){
		value.bind(function(to){
			$('.carousel .item').css('height', to + 'px');
		});
	});
	
	/* Copyright text */
	wp.customize('franz_settings[copyright_text]', function(value){
		value.bind(function(to){
			$('.copyright-text').html(to);
		});
	});
  
});