Ext.define('AO.view.DodajVzpon', {
    extend: 'Ext.form.Panel',
    alias: 'widget.ao-dodaj-vzpon',

    border: false,
    
    url: '/wp-admin/admin-ajax.php?action=dodaj_vzpon',
    method: 'POST',
    
    fieldDefaults: {
        labelWidth: 140,
        width: 400,
        margin: '10'
    },

    items: [{
        xtype: 'container',
        html: '<h2>Dodaj vzpon</h2>'
    },{
        xtype: 'combo',
        fieldLabel: 'Tip',
        name: 'tip',
        displayField: 'ime',
        valueField: 'tip',
        queryMode: 'local',
        lastQuery: '',
        emptyText: 'Izberite tip vzpona',
        margin: '10 10 30 10',
        editable: false,
        store: {
            type: 'json',
            fields: ['tip', 'ime'],
            data: [
                {tip: 'ALP',  ime: 'alpinistična smer'},
                {tip: 'ŠP',   ime: 'športno plezalna smer'},
                {tip: 'SMUK', ime: 'smuk'},
                {tip: 'PR',   ime: 'pristop'}
            ]
        }
    },{
        xtype: 'panel',
        layout: 'column',
        margin: '0 0 24 0',
        border: false,
        items: [{
            xtype: 'container',
            width: 460,
            maxWidth: 460,
            columnWidth: 460,
            items: [{
                xtype: 'datefield',
                fieldLabel: 'Datum',
                name: 'datum',
                format: 'j.n.Y',
                allowBlank: false,
                listeners: {
                    focus: function() {
                        this.expand();
                    }
                }
            },{
                xtype: 'textfield',
                fieldLabel: 'Destinacija',
                name: 'destinacija',
                allowBlank: false
            },{
                xtype: 'textfield',
                fieldLabel: 'Smer',
                name: 'smer',
                allowBlank: false
            },{
                xtype: 'textfield',
                fieldLabel: 'Ocena',
                name: 'ocena'
            },{
                xtype: 'textfield',
                fieldLabel: 'Soplezalec',
                name: 'partner'
            },{
                xtype: 'textfield',
                fieldLabel: 'Čas',
                name: 'cas'
            },{
                xtype: 'textfield',
                fieldLabel: 'Višina',
                name: 'visina_smer',
                allowBlank: false,
                width: 280,
                beforeSubTpl: '<span class="x-form-right-label">m</span>'
            },{
                xtype: 'textfield',
                fieldLabel: 'Nadm. viš. izstopa',
                name: 'visina_izstop',
                width: 280,
                beforeSubTpl: '<span class="x-form-right-label">m</span>'
            }]
        },{
            xtype: 'container',
            width: 460,
            maxWidth: 460,
            columnWidth: 460,
            items: [{
                xtype: 'combo',
                fieldLabel: 'Vrsta',
                name: 'vrsta',
                displayField: 'ime',
                valueField: 'vrsta',
                emptyText: '-- Izberi vrsto vzpona --',
                queryMode: 'local',
                lastQuery: '',
                allowBlank: true,
                editable: false,
                store: {
                    type: 'json',
                    fields: ['vrsta', 'ime'],
                    data: [
                        {vrsta: 'K',  ime: 'kopna'},
                        {vrsta: 'L',  ime: 'ledna (snežna)'},
                        {vrsta: 'LK', ime: 'ledna kombinirana'}
                    ]
                }
            },{
                xtype: 'combo',
                fieldLabel: 'Vrsta ponovitve',
                name: 'pon_vrsta',
                displayField: 'ime',
                valueField: 'ponovitev',
                emptyText: '-- Ni ponovitev --',
                queryMode: 'local',
                lastQuery: '',
                allowBlank: true,
                editable: false,
                store: {
                    type: 'json',
                    fields: ['ponovitev', 'ime'],
                    data: [
                        {ponovitev: false, ime: '-- Ni ponovitev --'},
                        {ponovitev: 'Prv', ime: 'prvenstvena'},
                        {ponovitev: '1P',  ime: 'prva ponovitev'},
                        {ponovitev: '2P',  ime: 'druga ponovitev'},
                        {ponovitev: 'ZP',  ime: 'zimska ponovitev'}
                    ]
                },
                listeners: {
                    beforeselect: function(combo, record) {
                        if (record.get(combo.valueField) === false) {
                            combo.collapse();
                            combo.reset();
                            return false;
                        }
                    }
                }
            },{
                xtype: 'combo',
                fieldLabel: 'Način ponovitve',
                name: 'pon_nacin',
                displayField: 'ime',
                valueField: 'nacin',
                queryMode: 'local',
                emptyText: '-- Ni ponovitev --',
                lastQuery: '',
                allowBlank: true,
                editable: false,
                store: {
                    type: 'json',
                    fields: ['nacin', 'ime'],
                    data: [
                        {ponovitev: false, ime: '-- Ni ponovitev --'},
                        {nacin: 'PP',      ime: 'prosta ponovitev'},
                        {nacin: 'NP',      ime: 'na pogled'},
                        {nacin: 'RP',      ime: 'z rdečo piko'}
                    ]
                },
                listeners: {
                    beforeselect: function(combo, record) {
                        if (record.get(combo.valueField) === false) {
                            combo.collapse();
                            combo.reset();
                            return false;
                        }
                    }
                }
            },{
                xtype: 'combo',
                fieldLabel: 'Stil',
                name: 'stil',
                displayField: 'ime',
                valueField: 'stil',
                emptyText: '-- Izberite stil vzpona --',
                queryMode: 'local',
                lastQuery: '',
                allowBlank: true,
                editable: false,
                store: {
                    type: 'json',
                    fields: ['stil', 'ime'],
                    data: [
                        {stil: false, ime: '-- Izberite stil vzpona --'},
                        {stil: 'A',   ime: 'alpski'},
                        {stil: 'K',   ime: 'kombinirani'},
                        {stil: 'OS',  ime: 'odpravarski'}
                    ]
                },
                listeners: {
                    beforeselect: function(combo, record) {
                        if (record.get(combo.valueField) === false) {
                            combo.collapse();
                            combo.reset();
                            return false;
                        }
                    }
                }
            },{
                xtype: 'combo',
                fieldLabel: 'Mesto',
                name: 'mesto',
                displayField: 'ime',
                valueField: 'mesto',
                emptyText: '-- Izberite vaše mesto pri vzponu --',
                queryMode: 'local',
                lastQuery: '',
                allowBlank: true,
                editable: false,
                store: {
                    type: 'json',
                    fields: ['mesto', 'ime'],
                    data: [
                        {mesto: false, ime: '-- Izberite vaše mesto pri vzponu --'},
                        {mesto: 'V',   ime: 'vodstvo'},
                        {mesto: 'D',   ime: 'drugi'},
                        {mesto: 'Ž',   ime: 'žimarjenje'},
                        {mesto: 'I',   ime: 'izmenjaje'}
                    ]
                },
                listeners: {
                    beforeselect: function(combo, record) {
                        if (record.get(combo.valueField) === false) {
                            combo.collapse();
                            combo.reset();
                            return false;
                        }
                    }
                }
            },{
                xtype: 'combo',
                fieldLabel: 'Opomba',
                name: 'opomba',
                displayField: 'ime',
                valueField: 'opomba',
                emptyText: '-- Brez opombe --',
                queryMode: 'local',
                lastQuery: '',
                allowBlank: true,
                editable: false,
                store: {
                    type: 'json',
                    fields: ['opomba', 'ime'],
                    data: [
                        {opomba: false, ime: '-- Brez opombe--'}
                    ]
                },
                listeners: {
                    beforeselect: function(combo, record) {
                        if (record.get(combo.valueField) === false) {
                            combo.collapse();
                            combo.reset();
                            return false;
                        }
                    }
                }
            }]
        }]
    },{
        xtype: 'toolbar',
        dock: 'bottom',
        ui: 'footer',
        items: [{
            text: 'Dodaj vzpon',
            action: 'save',
            formBind: true
        }]
    }],

    initComponent: function() {
        this.callParent();
        
        this.on({
            afterrender: this.onAfterRender,
            scope: this
        });
    },
    
    onAfterRender: function() {
        this.changeForm('ALP');
        
        this.down('field[name=tip]').on({
            change: this.onTipChange,
            scope: this
        });
    },
    
    onTipChange: function(combo, newValue, oldValue) {
        this.changeForm(newValue);
    },
    
    changeForm: function(tip) {
        switch (tip) {
            case 'ALP':
                this.down('field[name=partner]').enable().show();
                this.down('field[name=cas]').enable().show();
                this.down('field[name=visina_izstop]').enable().show();
                this.down('field[name=vrsta]').enable().show();
                this.down('field[name=pon_vrsta]').enable().show();
                this.down('field[name=pon_nacin]').enable().show();
                this.down('field[name=stil]').enable().show();
                this.down('field[name=mesto]').enable().show();
                this.down('field[name=opomba]').getStore().loadData([
                    {opomba: false, ime: '-- Brez opombe --'},
                    {opomba: 'ZS',  ime: 'zaledeneli slap'}
                ]);
                break;
            case 'ŠP':
                this.down('field[name=partner]').disable().hide();
                this.down('field[name=cas]').disable().hide();
                this.down('field[name=visina_izstop]').disable().hide();
                this.down('field[name=vrsta]').disable().hide();
                this.down('field[name=pon_vrsta]').enable().show();
                this.down('field[name=pon_nacin]').enable().show();
                this.down('field[name=stil]').disable().hide();
                this.down('field[name=mesto]').disable().hide();
                this.down('field[name=opomba]').getStore().loadData([
                    {opomba: false, ime: '-- Brez opombe --'},
                    {opomba: 'BV',  ime: 'balvan'}
                ]);
                break;
            case 'SMUK':
                this.down('field[name=partner]').enable().show();
                this.down('field[name=cas]').disable().hide();
                this.down('field[name=visina_izstop]').disable().hide();
                this.down('field[name=vrsta]').disable().hide();
                this.down('field[name=pon_vrsta]').disable().hide();
                this.down('field[name=pon_nacin]').disable().hide();
                this.down('field[name=stil]').disable().hide();
                this.down('field[name=mesto]').disable().hide();
                this.down('field[name=opomba]').getStore().loadData([
                    {opomba: false, ime: '-- Brez opombe --'},
                    {opomba: 'AS',  ime: 'alpinistični smuk'}
                ]);
                break;
            case 'PR':
                this.down('field[name=partner]').enable().show();
                this.down('field[name=cas]').disable().hide();
                this.down('field[name=visina_izstop]').disable().hide();
                this.down('field[name=vrsta]').disable().hide();
                this.down('field[name=pon_vrsta]').disable().hide();
                this.down('field[name=pon_nacin]').disable().hide();
                this.down('field[name=stil]').disable().hide();
                this.down('field[name=mesto]').disable().hide();
                this.down('field[name=opomba]').getStore().loadData([]);
                this.down('field[name=opomba]').getStore().loadData([
                    {opomba: false, ime: '-- Brez opombe --'}
                ]);
                break;
        }
    }

});
