Ext.define('AO.view.vzpon.Tabela', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.ao-vzpon-tabela',
    
    loadMask: true,
    
    initComponent: function() {
        this.store = Ext.create('AO.store.Vzponi', {
            storeId: 'ao-vzponi-tabela',
            pageSize: 20,
            remoteSort: true
        });
        
        this.tbar = Ext.create('Ext.PagingToolbar', {
            store: this.store,
            displayInfo: true,
            emptyMsg: 'Ni vzponov',
            firstText: 'Prva stran',
            lastText: 'Zadnja stran',
            nextText: 'Naprej',
            prevText: 'Nazaj',
            refreshText: 'Osveži',
            beforePageText: 'Stran',
            afterPageText: 'od',
            displayMsg: 'Vzponi {0} - {1} od {2}'
        });
        
        this.callParent();
        
        this.store.loadPage(1);
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
        flex: 1,
        renderer: function(value) {
            if (value.length && value.search('m') === -1) {
                value += 'm';
            }
            return value;
        }
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
        }
    }

});
