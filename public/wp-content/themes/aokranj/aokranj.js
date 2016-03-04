jQuery(document).ready(function(){

    // fix header 
    jQuery(window).resize(function(){
        var width = jQuery(window).width();
        if (width > 768 && width < 992) {
            jQuery('.header').addClass('wide-nav');
        } else {
            jQuery('.header').removeClass('wide-nav');
        }
    });
});
