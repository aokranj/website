jQuery(document).ready(function(){

    // fix header
    jQuery(window).resize(function(){
        var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
        if (width >= 768 && width < 992) {
            jQuery('.header').addClass('wide-nav');
        } else {
            jQuery('.header').removeClass('wide-nav');
        }
    });

});
