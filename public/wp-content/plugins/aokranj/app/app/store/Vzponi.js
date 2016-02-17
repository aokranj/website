Ext.define('AO.store.Vzponi', {
    extend: 'Ext.data.Store',
    
    alias: 'store.ao-vzponi',
    model: 'AO.model.Vzpon',
    storeId: 'Vzponi',
    autoLoad: false,
    
    sorters: [{
        property: 'datum',
        direction: 'DESC'
    }],
    
    proxy: {
        type: 'ajax',
        url: '/wp-admin/admin-ajax.php?action=vzponi',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
    
});
