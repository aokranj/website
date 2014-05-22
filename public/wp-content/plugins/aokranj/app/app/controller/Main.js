Ext.define('AO.controller.Main', {
    extend: 'Ext.app.Controller',
    
    views: [
        'Main'
    ],
    
    refs: [
        {ref: 'main', selector: 'ao-main'}
    ],
    
    init: function() {
        this.control({
            'ao-main': {
                afterrender: this.onAfterRender
            }
        });
    },
    
    onAfterRender: function() {
        this.onWindowResize();
        
        Ext.EventManager.onWindowResize(Ext.bind(this.onWindowResize, this));
        
        Ext.get('collapse-menu').on({
            click: this.onWindowResize,
            scope: this
        });
    },
    
    onWindowResize: function() {
        this.getMain().setWidth(Ext.getBody().getWidth() - Ext.get('adminmenuwrap').getWidth() - 40);
    }
    
});