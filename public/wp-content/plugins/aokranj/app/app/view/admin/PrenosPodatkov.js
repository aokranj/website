Ext.define('AO.view.admin.PrenosPodatkov', {
    extend: 'Ext.form.Panel',
    alias: 'widget.ao-admin-prenospodatkov',
    
    requires: [
        'Ext.form.FieldSet'
    ],

    title: 'Prenos podatkov',
    glyph: 'xe602@aokranj',
    border: false,
    bodyPadding: '0 6 20 6',
    
    url: '/wp-admin/admin-ajax.php?action=prenos_podatkov',
    method: 'POST',
    
    buttonAlign: 'left',
    buttons: [{
        text: 'Začni',
        action: 'submit'
    }],
    
    fieldDefaults: {
        labelWidth: 120,
        margin: '12 0'
    },
    
    items: [{
        xtype: 'textfield',
        fieldLabel: 'DocumentRoot',
        name: 'DocumentRoot',
        width: 400
    },{
        xtype: 'progressbar',
        width: 400
    }]

});
