Ext.define('AO.view.vzpon.Orodja', {
    extend: 'Ext.container.Container',
    alias: 'widget.ao-vzpon-orodja',
    
    layout: 'column',
    border: false,
    bodyPadding: '20 0 0 0',
    margin: '0 0 20 0',
    
    items: [{
        xtype: 'datepicker'
    },{
        xtype: 'chart',
        width: 280,
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

});
