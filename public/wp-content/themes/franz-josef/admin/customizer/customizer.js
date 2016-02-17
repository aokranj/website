jQuery(document).ready(function ($) {
	  
	/* Toggle the controls when the referenced setting changes */
	$(document).on('change', '#customize-control-franz_settings-slider_type [data-customize-setting-link]', function(){
		
		prefix = '#customize-control-franz_settings-';
		
		if ( $(this).val() == 'posts_pages' ) $(prefix + 'slider_specific_posts').show();
		else $(prefix + 'slider_specific_posts').hide();
		
		if ( $(this).val() == 'categories' ) $(prefix + 'slider_specific_categories, ' + prefix + 'slider_exclude_categories, ' + prefix + 'slider_random_category_posts').show();
		else $(prefix + 'slider_specific_categories, ' + prefix + 'slider_exclude_categories, ' + prefix + 'slider_random_category_posts').hide();
	});
	$('#customize-control-franz_settings-slider_type [data-customize-setting-link]').trigger('change');
	
	/* Improve <select> elements */
	$('.chzn-select').each(function () {
		var chosenOptions = new Object();
		chosenOptions.disable_search_threshold = 10;
		chosenOptions.allow_single_deselect = true;
		chosenOptions.no_results_text = franzCustomizer.chosen_no_search_result;
		if ($(this).attr('multiple')) chosenOptions.width = '100%';
		else chosenOptions.width = '250px';

		$(this).chosen(chosenOptions);
	});
	
	$('.chzn-select').on('change', function(e,params){
		settingID = $(this).data('customize-setting-link');
		if ( $(':selected', $(this)).length == 0 ) {
			wp.customize( settingID, function ( obj ) {
				obj.set( '' );
			} );
		}
	});
	
});