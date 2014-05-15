Ext.define('AO.view.Tabs', {
    extend: 'Ext.tab.Panel',
    alias: 'widget.ao-tabs',

    activeTab: 0,
    plain: true,
    border: false,
    bodyCls: 'ao-tabs',

    items: [{
        xtype: 'panel',
        tooltip: 'Vzponi',
        glyph: 'xe600@aokranj',
        border: false,
        
        items: [{
            xtype: 'container',
            html: '<h2>Vzponi</h2>'
        },{
            xtype: 'panel',
            layout: 'column',
            cls: 'ao-koledarcek',
            title: 'Test',
            collapsible: true,
            collapsed: true,
            border: false,
            bodyPadding: '20 0 0 0',
            margin: '0 0 20 0',
            items: [{
                xtype: 'datepicker',
                width: 260
            },{
                xtype: 'chart',
                width: 240,
                height: 240,
                animate: true,
                margin: '0 0 0 20',
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
            xtype: 'ao-vzponi'
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
        height: 400,
        items: [{
            xtype: 'container',
            html: '<h2>Nastavitve</h2>'
        }]
    }]
});
