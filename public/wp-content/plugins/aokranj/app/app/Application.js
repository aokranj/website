Ext.define('AO.Application', {
    name: 'AO',
    extend: 'Ext.app.Application',
    appFolder: '/wp-content/plugins/aokranj/app/app',
    autoCreateViewport: false,
    
    requires: [
        'Ext.state.CookieProvider',
        'Ext.data.JsonStore',
        'Ext.grid.column.Date',
        'Ext.layout.container.Column',
        'Ext.form.field.ComboBox',
        'Ext.form.field.Date',
        'Ext.picker.Date',
        'Ext.chart.Chart',
        'Ext.chart.series.Pie'
    ],

    controllers: [
        'Main',
        'vzpon.Vzponi',
        'vzpon.Dodaj',
        'uporabnik.Nastavitve',
        'uporabnik.Statistika',
        'admin.PrenosPodatkov',
        'admin.Statistika'
    ],

    stores: [
        'Vzponi'
    ],
    
    init: function() {
        Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
    },
    
    launch: function() {
        Ext.getBody().removeCls('x-body');
        
        Ext.create('AO.view.Main', {
            renderTo: 'aokranj'
        });
    }
    
});
