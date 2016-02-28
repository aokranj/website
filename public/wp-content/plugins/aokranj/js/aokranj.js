
jQuery(document).ready(function(){

    jQuery('.gallery').each(function(i, item){
        jQuery('a', this).colorbox({
            rel: 'gallery' + i,
            maxWidth: '98%',
            maxHeight: '98%',
            loop: false,
            title: function() {
                return jQuery('img', this).attr('alt');;
            },
            current: 'slika {current} od {total}',
            next: 'naprej',
            previous: 'nazaj',
            close: 'zapri',
            xhrError: 'Vsebine ni mogoče naložiti.',
            imgError: 'Slike ni mogoče naložiti.'
        });
    });

});
