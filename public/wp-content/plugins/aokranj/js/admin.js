jQuery(document).ready(function(){

    if (jQuery.fn.pikaday) {
        jQuery('.datepicker').pikaday({
            format: 'YYYY-MM-DD',
            firstDay: 1
        });
    }

    if (jQuery('#vzpon #tip').length) {
        // handle on change
        jQuery('#vzpon #tip').on('change', setForm);
        function setForm() {
            var tip = jQuery('#vzpon #tip').val();
            switch (tip) {
                case 'ALP':
                    enable('#vzpon #partner');
                    enable('#vzpon #cas');
                    enable('#vzpon #visina_izstop');
                    enable('#vzpon #vrsta');
                    enable('#vzpon #pon_vrsta');
                    enable('#vzpon #pon_nacin');
                    enable('#vzpon #stil');
                    enable('#vzpon #mesto');
                    break;
                case 'ŠP':
                    disable('#vzpon #partner');
                    disable('#vzpon #cas');
                    disable('#vzpon #visina_izstop');
                    disable('#vzpon #vrsta');
                    enable('#vzpon #pon_vrsta');
                    enable('#vzpon #pon_nacin');
                    disable('#vzpon #stil');
                    disable('#vzpon #mesto');
                    break;
                case 'SMUK':
                    enable('#vzpon #partner');
                    disable('#vzpon #cas');
                    disable('#vzpon #visina_izstop');
                    disable('#vzpon #vrsta');
                    disable('#vzpon #pon_vrsta');
                    disable('#vzpon #pon_nacin');
                    disable('#vzpon #stil');
                    disable('#vzpon #mesto');
                    break;
                case 'PR':
                    enable('#vzpon #partner');
                    disable('#vzpon #cas');
                    disable('#vzpon #visina_izstop');
                    disable('#vzpon #vrsta');
                    disable('#vzpon #pon_vrsta');
                    disable('#vzpon #pon_nacin');
                    disable('#vzpon #stil');
                    disable('#vzpon #mesto');
                    break;
            }
        }
        function enable(selector) {
            jQuery(selector).prop('disabled', false).closest('tr').removeClass('hidden');
        }
        function disable(selector) {
            jQuery(selector).prop('disabled', true).closest('tr').addClass('hidden');
        }
    }

});
