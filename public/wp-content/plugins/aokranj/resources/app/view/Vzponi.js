Ext.define('AO.view.Vzponi', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.ao-vzponi',
    
    store: {
        type: 'json',
        storeId: 'vzponi',
        model: 'AO.model.Vzpon',
        autoLoad: true,
        sorters: [{
            property: 'datum',
            direction: 'DESC'
        }],
        proxy: {
            type: 'ajax',
            url: '/wp-admin/admin-ajax.php?action=vzponi'
        }
    },
    
    columns: [{
        text: 'Destinacija',
        dataIndex: 'destinacija',
        flex: 2
    },{
        text: 'Smer',
        dataIndex: 'smer',
        flex: 2
    },{
        text: 'Soplezalec',
        dataIndex: 'partner',
        flex: 2
    },{
        text: 'Ocena',
        dataIndex: 'ocena',
        flex: 1
    },{
        xtype: 'datecolumn',
        format: 'j.n.Y',
        text: 'Datum',
        dataIndex: 'datum',
        flex: 1
    },{
        text: 'Tip',
        dataIndex: 'tip',
        flex: 2,
        renderer: function(value, metaData, vzpon) {
            return vzpon.tip();
        }
    },{
        text: 'Čas',
        dataIndex: 'cas', flex: 1
    },{
        text: 'Višina',
        dataIndex: 'visina_smer',
        flex: 1
    },{
        text: 'Nadm. viš. izstopa',
        dataIndex: 'visina_izstop',
        flex: 1,
        hidden: true
    },{
        text: 'Vrsta',
        dataIndex: 'vrsta',
        flex: 1,
        hidden: true,
        renderer: function(value, metaData, vzpon) {
            return vzpon.vrsta();
        }
    },{
        text: 'Vrsta ponovitve',
        dataIndex: 'pon_vrsta',
        flex: 1,
        hidden: true,
        renderer: function(value, metaData, vzpon) {
            return vzpon.ponovitev();
        }
    },{
        text: 'Način ponovitve',
        dataIndex: 'pon_nacin',
        flex: 1,
        hidden: true,
        renderer: function(value, metaData, vzpon) {
            return vzpon.nacin();
        }
    },{
        text: 'Stil',
        dataIndex: 'stil',
        flex: 1,
        hidden: true,
        renderer: function(value, metaData, vzpon) {
            return vzpon.stil();
        }
    },{
        text: 'Mesto',
        dataIndex: 'mesto',
        flex: 1,
        hidden: true,
        renderer: function(value, metaData, vzpon) {
            return vzpon.mesto();
        }
    },{
        text: 'Opomba',
        dataIndex: 'opomba',
        flex: 1,
        hidden: true,
        renderer: function(value, metaData, vzpon) {
            return vzpon.opomba();
        }
    }],
    
    listeners: {
        itemclick: function(grid, record, item, index, e) {
            console.log(record.tip());
            console.log(record.vrsta());
            console.log(record.ponovitev());
            console.log(record.nacin());
            console.log(record.stil());
            console.log(record.mesto());
            console.log(record.opomba());
        }
    }

});
