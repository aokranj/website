Ext.define('AO.view.Tabs', {
    extend: 'Ext.tab.Panel',
    alias: 'widget.ao-tabs',

    activeTab: 0,
    tabPosition: 'left',
    plain: true,
    border: false,
    bodyCls: 'x-tabs-left',

    items: [{
        xtype: 'panel',
        tooltip: 'Vzponi',
        glyph: 'xe600@aokranj',
        border: false,
        padding: '20',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        items: [{
            xtype: 'container',
            autoScroll: true,
            width: 300,
            margin: '0 10 0 0',
            items: [{
                xtype: 'container',
                margin: '-10 0 8 0',
                html: '<h2>Vzponi</h2>'
            },{
                xtype: 'datepicker',
                width: 280
            },{
                xtype: 'chart',
                width: 260,
                height: 260,
                animate: true,
                margin: '20 0 0 10',
                store: {
                    type: 'json',
                    fields: ['name', 'data'],
                    data: [
                        { 'name': 'metric one',   'data': 10 },
                        { 'name': 'metric two',   'data':  7 },
                        { 'name': 'metric three', 'data':  5 },
                        { 'name': 'metric four',  'data':  2 },
                        { 'name': 'metric five',  'data': 27 }
                    ]
                },
                theme: 'Base:gradients',
                series: [{
                    type: 'pie',
                    angleField: 'data',
                    showInLegend: true,
                    highlight: {
                        segment: {
                            margin: 10
                        }
                    },
                    label: {
                        field: 'name',
                        display: 'rotate',
                        contrast: true,
                        font: '14px Arial'
                    }
                }]
            }]
        },{
            xtype: 'ao-vzponi',
            flex: 1
        }]
    },{
        xtype: 'ao-dodaj-vzpon',
        tooltip: 'Dodaj vzpon',
        glyph: 'xe60f@aokranj'
    },{
        xtype: 'panel',
        tooltip: 'Nastavitve',
        glyph: 'xe605@aokranj',
        border: false,
        padding: '20',
        items: [{
            xtype: 'container',
            margin: '-10 0 8 0',
            html: '<h2>Nastavitve</h2>'
        }]
    }]
});
