Ext.Loader.setPath({
    'Ext': '/wp-content/plugins/aokranj/ext/src',
    'AO': '/wp-content/plugins/aokranj/app'
});

Ext.application({
    name: 'AO',
    extend: 'AO.Application',
    autoCreateViewport: false,
    appFolder: '/wp-content/plugins/aokranj/app'
});
