Ext.define('AO.view.Main', {
    extend: 'Ext.tab.Panel',
    alias: 'widget.ao-main',
    
    plain: true,
    border: false,
    bodyCls: 'ao-main',
    bodyPadding: '20 0 0 0',
    
    stateful: true,
    stateId: 'AO.view.Main',

    items: [{
        xtype: 'ao-vzponi'
    },{
        xtype: 'ao-vzpon-dodaj'
    },{
        xtype: 'ao-uporabnik-nastavitve'
    }],

    applyState: function(state) {
        if (state) {
            this.setActiveTab(state.activeTab);
            delete state['activeTab'];
            
            Ext.apply(this, state);
        }
    },
    
    getState: function() {
        var state = {
            activeTab: this.getActiveTabIndex()
        };
        return state;
    },
    
    getActiveTabIndex: function() {
        return Ext.isNumber(this.activeTab) ? this.activeTab : this.items.indexOf(this.activeTab);
    }

});
