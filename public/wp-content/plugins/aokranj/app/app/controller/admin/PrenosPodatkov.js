Ext.define('AO.controller.admin.PrenosPodatkov', {
    extend: 'Ext.app.Controller',
    
    views: [
        'admin.PrenosPodatkov'
    ],
    
    refs: [
        {ref: 'form', selector: 'ao-admin-prenospodatkov'}
    ],
    
    init: function() {
        this.control({
            'ao-admin-prenospodatkov button[action=submit]': {
                click: this.onSubmitClick
            }
        });
    },
    
    onSubmitClick: function() {
        var form = this.getForm();
        var progress = form.down('progressbar');
        var submit = form.down('button[action=submit]');
        
        var timeout = Ext.clone(Ext.Ajax.timeout);
        Ext.Ajax.timeout = 3600000;
        
        submit.disable();
        
        progress.wait({
            interval: 500, //bar will move fast!
            increment: 20,
            animate: true,
            text: 'Prenašam podatke ...'
        });
        
        form.submit({
            params: {
                nonce: AO.nonce
            },
            success: function(baseform, action) {
                console.log('success', arguments);
                
                Ext.Ajax.timeout = timeout;
                
                submit.enable();
                progress.reset();
                progress.updateProgress(1, 'Končano!', true);
                
                //form.unmask();
                //form.getForm().reset();
            },
            failure: function(baseform, action) {
                console.log('failure', arguments);
                
                Ext.Ajax.timeout = timeout;
                
                submit.enable();
                progress.reset();
                progress.updateProgress(1, 'Napaka!', true);
                
                //form.unmask();
            }
        });
    }
    
});