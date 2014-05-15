jQuery(function($) {
    
    $('#tabs').tabs();
    
    $('#datum').datepicker({ dateFormat: "d.m.yy" });
    
    $('#dodajvzpon').submit(function(e) {
        
        var url = $(this).attr('action');
        
        var formData = {
            action:        'dodaj_vzpon',
            _wpnonce:      $('input[name=_wpnonce_dodaj-vzpon]').val(),
            tip:           $('select[name=tip]').val(),
            destinacija:   $('input[name=destinacija]').val(),
            smer:          $('input[name=smer]').val(),
            datum:         $('input[name=datum]').val(),
            ocena:         $('input[name=ocena]').val(),
            cas:           $('input[name=cas]').val(),
            vrsta:         $('select[name=vrsta]').val(),
            visina_smer:   $('input[name=visina_smer]').val(),
            visina_izstop: $('input[name=visina_izstop]').val(),
            pon_vrsta:     $('select[name=pon_vrsta]').val(),
            pon_nacin:     $('select[name=pon_nacin]').val(),
            stil:          $('select[name=stil]').val(),
            mesto:         $('select[name=mesto]').val(),
            partner:       $('input[name=partner]').val(),
            opomba:        $('select[name=opomba]').val()
        };
        
        // process the form
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            data: formData
        }).done(function(data) {
            console.log(data);
        });
        
        e.preventDefault();
    });
    
});
