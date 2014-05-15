Ext.onReady(function(){
    
    // init controllers
    var dodajVzpon = new AO.controller.DodajVzpon();
    dodajVzpon.init();
    
    // init body
    var body = Ext.getBody();
    body.removeCls('x-body');
    
    // get menu reference
    var menu = Ext.get('adminmenuwrap');
    
    // create ao view
    var tabs = Ext.create('AO.view.Tabs', {
        renderTo: 'aokranj'
    });
    
    // setup nonce
    AO.nonce = Ext.get('aokranj').getAttribute('data-nonce');
    
    // window resize handler
    var onWindowResize = function() {
        var width = body.getWidth() - menu.getWidth() - 40;
        tabs.setWidth(width);
        
        /*
        var height = body.getHeight() - 60;
        var minHeight = 500;
        if (height < minHeight) {
            height = minHeight;
        }
        tabs.setHeight(height);
        */
    };
    
    onWindowResize();
    Ext.EventManager.onWindowResize(onWindowResize);
    Ext.get('collapse-menu').on('click', onWindowResize);
    
});