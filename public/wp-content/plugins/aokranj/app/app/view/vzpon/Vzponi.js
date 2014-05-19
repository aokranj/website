Ext.define('AO.view.vzpon.Vzponi', {
    extend: 'Ext.container.Container',
    alias: 'widget.ao-vzponi',
    
    title: 'Vzponi',
    glyph: 'xe600@aokranj',
    border: false,

    items: [{
        xtype: 'ao-vzpon-orodja',
        hidden: true
    },{
        xtype: 'ao-vzpon-tabela'
    }]

});
