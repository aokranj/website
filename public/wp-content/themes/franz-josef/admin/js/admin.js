jQuery(document).ready(function ($) {
	$('.head-wrap .hndle, .handlediv').parents('.postbox').addClass('closed');
	$('.head-wrap .hndle, .handlediv').click(function(e){
		e.preventDefault();
		$(this).parent().next().toggle().parent().toggleClass('closed');
	}).parent().next().hide();

	// Toggle all
	$('.toggle-all').click(function(e){
		$('.meta-box-sortables .head-wrap').each(function(){
			$(this).next().toggle();
			$(this).parent().toggleClass('closed');
		});
		e.preventDefault();
	})

	// Show/Hide the slider_type specific options
	$('input[name="franz_settings\\[slider_type\\]"]').change(function () {
		$('[class*="row_slider_type"]').hide();
		$('.row_slider_type_' + $(this).val()).fadeIn();
	});

	// Show/Hide home page panes specific options
	$('input[name="franz_settings\\[show_post_type\\]"]').change(function () {
		$('[id*="row_show_post_type"]').hide();
		$('#row_show_post_type_' + $(this).val()).fadeIn();
		if ($(this).val() == 'cat-latest-posts') {
			$('#row_show_post_type_latest-posts').fadeIn();
		}
	});

	// To hide/show complete section
	$('input[data-toggleOptions]').change(function () {
		var target = $(this).attr('rel');
		if (target)
			$('.' + target).fadeToggle();
		else
			$(this).closest('table').next().fadeToggle();
	});

	// To Show/Hide the widget hooks
	$('a.toggle-widget-hooks').click(function () {
		$(this).closest('li').find('li.widget-hooks').fadeToggle();
		return false;
	});

	// Select all
	$('.select-all').click(function () {
		franzSelectText($(this).prop('rel'));
		return false;
	});

	// Display spinning wheel when options form is submitted
	$('.left-wrap input[type="submit"]').click(function () {
		ajaxload = '<i class="ajaxload fa fa-spinner fa-spin"></i>';
		if ($(this).parents('.panel-wrap').length > 0)
			$(this).parent().prepend(ajaxload);
		else
			$(this).parent().append(ajaxload);
	});
	//$('<img/>')[0].src = franzAdminScript.franz_uri + '/images/ajax-loader.gif';

	// Save options via AJAX
	$('#franz-options-form').submit(function () {

		var data = $(this).serialize();
		data = data.replace('action=update', 'action=franz_ajax_update');

		$.post(ajaxurl, data, function (response) {
			$('.ajaxload').remove();
			franz_show_message(response);

			if (response.search('error') == -1) t = 1000
			else t = 4000;
			t = setTimeout('franz_fade_message()', t);
		});

		return false;
	});

	/* Improve <select> elements */
	if (franzAdminScript.is_rtl == false) {
		$('.chzn-select').each(function () {
			var chosenOptions = new Object();
			chosenOptions.disable_search_threshold = 10;
			chosenOptions.allow_single_deselect = true;
			chosenOptions.no_results_text = franzAdminScript.chosen_no_search_result;
			if ($(this).attr('multiple')) chosenOptions.width = '100%';
			else chosenOptions.width = '250px';

			$(this).chosen(chosenOptions);
		});
	}


	// Remember the opened options panes
	$('.head-wrap .hndle, .handlediv, .toggle-all').click(function(e) {
		e.preventDefault();
		var postboxes = $('.left-wrap .postbox');
		var openboxes = new Array();
		$('.left-wrap .panel-wrap:visible').each(function (index) {
			var openbox = $(this).parents('.postbox');
			openboxes.push(postboxes.index(openbox));
		});
		franzSetCookie('franz-tab-' + franz_tab + '-boxes', openboxes.join(','), 100);
	});

	// reopen the previous options panes
	var oldopenboxes = franzGetCookie('franz-tab-' + franz_tab + '-boxes');
	if (oldopenboxes != null && oldopenboxes != '') {
		var boxindexes = oldopenboxes.split(',');
		for (var boxindex in boxindexes) {
			$('.left-wrap .postbox:eq(' + boxindexes[boxindex] + ')').removeClass('closed').children('.panel-wrap').show();
		}
	}


	// To support the Media Uploader in the theme options
	var customUploader, uploaderTarget;
    $(document).on('click', '.media-upload', function(e) {
        e.preventDefault();
 
        // Extend the wp.media object
		var uploaderOpts = {
			title	: 'Choose Image',
			library	: { type: 'image' },
            button	: { text: 'Choose Image' },
            multiple: false
		};
		if ( $(this).data('title') ) uploaderOpts.title = $(this).data('title');
		if ( $(this).data('button') ) uploaderOpts.button.text = $(this).data('button');
		if ( $(this).data('multiple') ) uploaderOpts.multiple = $(this).data('multiple');
        customUploader = wp.media.frames.file_frame = wp.media(uploaderOpts);
 
		fieldName = $(this).data('field');
		uploaderTarget = '#' + fieldName;
		
        customUploader.on('select', function() {
			attachment = customUploader.state().get('selection').first().toJSON();
			
			if ( fieldName.indexOf('brand_icon') === 0 ) {
				if (window.franzBrandIconIndex == undefined) window.franzBrandIconIndex = $(uploaderTarget).data('count') + 1;
				else window.franzBrandIconIndex += 1;
				
				$(uploaderTarget).val(attachment.id);
				$('.left', $(uploaderTarget).parent()).html('<img src="' + attachment.url + '" alt="' + attachment.alt + '" width="' + attachment.width + '" height="' + attachment.height + '" />');
				$(uploaderTarget).parent().append('<span class="delete"><a href="#">' + franzAdminScript.delete + '</a></span>');
				$('#brand_icons').append('\
					<li class="clearfix">\
						<div class="left"><span class="image-placeholder"></span></div>\
						<input type="hidden" name="franz_settings[brand_icons][' + window.franzBrandIconIndex + '][image_id]" value="" id="brand_icon_' + window.franzBrandIconIndex + '" />\
						<label for="brand_icon_link_' + window.franzBrandIconIndex + '">' + franzAdminScript.link + '</label>\
						<input id="brand_icon_link_' + window.franzBrandIconIndex + '" type="text" name="franz_settings[brand_icons][' + window.franzBrandIconIndex + '][link]" value="" class="code" placeholder="' + franzAdminScript.optional + '" size="60" />\
						<a data-field="brand_icon_' + window.franzBrandIconIndex + '" data-title="' + uploaderOpts.title + '" data-button="' + uploaderOpts.button.text + '" href="#" class="media-upload button">' + uploaderOpts.button.text + '</a>\
					</li>\
				');
			} else {
				$(uploaderTarget).val(attachment.url);
			}				
        });
 
        //Open the uploader dialog
        customUploader.open();
    });

});


function hexToR(h) {
	if (h.length == 4)
		return parseInt((cutHex(h)).substring(0, 1) + (cutHex(h)).substring(0, 1), 16);
	if (h.length == 7)
		return parseInt((cutHex(h)).substring(0, 2), 16);
}

function hexToG(h) {
	if (h.length == 4)
		return parseInt((cutHex(h)).substring(1, 2) + (cutHex(h)).substring(1, 2), 16);
	if (h.length == 7)
		return parseInt((cutHex(h)).substring(2, 4), 16);
}

function hexToB(h) {
	if (h.length == 4)
		return parseInt((cutHex(h)).substring(2, 3) + (cutHex(h)).substring(2, 3), 16);
	if (h.length == 7)
		return parseInt((cutHex(h)).substring(4, 6), 16);
}

function cutHex(h) {
	return (h.charAt(0) == "#") ? h.substring(1, 7) : h
}

function franzCheckFile(f, type) {
	type = (typeof type === "undefined") ? 'options' : type;
	f = f.elements;
	if (/.*\.(txt)$/.test(f['upload'].value.toLowerCase()))
		return true;
	if (type == 'options') alert(franzAdminScript.import_select_file);
	else if (type == 'colours') alert(franzAdminScript.preset_select_file);
	f['upload'].focus();
	return false;
};

function franzSetCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	} else var expires = "";
	document.cookie = name + "=" + value + expires + "; path=/";
}

function franzGetCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

function franzDeleteCookie(name) {
	franzSetCookie(name, "", -1);
}

function franzSelectText(element) {
	var doc = document;
	var text = doc.getElementById(element);

	if (doc.body.createTextRange) { // ms
		var range = doc.body.createTextRange();
		range.moveToElementText(text);
		range.select();
	} else if (window.getSelection) { // moz, opera, webkit
		var selection = window.getSelection();
		var range = doc.createRange();
		range.selectNodeContents(text);
		selection.removeAllRanges();
		selection.addRange(range);
	}
}

function franz_show_message(response) {
	jQuery('.franz-ajax-response').html(response).fadeIn(400);
}

function franz_fade_message() {
	jQuery('.franz-ajax-response').fadeOut(1000);
	clearTimeout(t);
}