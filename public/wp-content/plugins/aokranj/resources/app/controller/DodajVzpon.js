Ext.define('AO.controller.DodajVzpon', {
    extend: 'Ext.app.Controller',
    
    refs: [
        {ref: 'form', selector: 'ao-dodaj-vzpon'}
    ],
    
    init: function() {
        this.control({
            'ao-dodaj-vzpon button[action=save]': {
                click: this.dodajVzpon
            }
        });
    },
    
    dodajVzpon: function() {
        //var formData = this.getForm().getValues();
        var form = this.getForm();
        
        form.mask('Shranjujem ...');
        
        this.getForm().submit({
            params: {
                nonce: AO.nonce
            },
            success: function(baseform, action) {
                form.unmask();
                form.getForm().reset();
            },
            failure: function(baseform, action) {
                form.unmask();
                switch (action.failureType) {
                    case Ext.form.action.Action.CLIENT_INVALID:
                        Ext.Msg.alert('Napaka', 'Forma vsebuje napačne podatke.');
                        break;
                    case Ext.form.action.Action.CONNECT_FAILURE:
                        Ext.Msg.alert('Napaka', 'Prišlo je do napake na strežniku. Prosimo kontaktirajte administratorja.');
                        break;
                    case Ext.form.action.Action.SERVER_INVALID:
                       Ext.Msg.alert('Napaka', action.result.msg);
               }
            },
            callback: function() {
                console.log('callback');
                form.unmask();
            }
        });
    }
    
});