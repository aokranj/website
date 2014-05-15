Ext.onReady(function(){
    
    var dodajVzpon = new AO.controller.DodajVzpon();
    dodajVzpon.init();
    
    var body = Ext.getBody();
    var menu = Ext.get('adminmenuwrap');
    
    var tabs = Ext.create('AO.view.Tabs', {
        renderTo: 'aokranj'
    });
    
    AO.nonce = Ext.get('aokranj').getAttribute('data-nonce');
    
    var onWindowResize = function() {
        var width = body.getWidth() - menu.getWidth() - 40;
        var height = body.getHeight() - 60;
        var minHeight = 500;
        if (height < minHeight) {
            height = minHeight;
        }
        
        tabs.setWidth(width);
        tabs.setHeight(height);
    };
    
    onWindowResize();
    Ext.EventManager.onWindowResize(onWindowResize);
    Ext.get('collapse-menu').on('click', onWindowResize);
    
});