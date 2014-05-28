Ext.define('AO.view.Main', {
    extend: 'Ext.tab.Panel',
    alias: 'widget.ao-main',
    
    plain: true,
    border: false,
    bodyCls: 'ao-main',
    bodyPadding: '20 0 0 0',
    
    stateful: true,
    stateId: 'AO.view.Main',

    initComponent: function() {
        this.callParent();
        
        this.add([{
            xtype: 'ao-vzponi'
        },{
            xtype: 'ao-vzpon-dodaj'
        },{
            xtype: 'ao-uporabnik-nastavitve',
            hidden: true
        },{
            xtype: 'ao-uporabnik-statistika',
            hidden: true
        }]);
        
        if (AO.User.user_level > 7) {
            this.add({
                xtype: 'ao-admin-prenospodatkov'
            },{
                xtype: 'ao-admin-statistika',
                hidden: true
            });
        }
        
        this.getTabBar().insert(0, {
            xtype: 'tbtext',
            cls: 'ao-tab-text',
            text: 'Živjo ' + AO.User.display_name + ' >',
            margin: '0 4 0 0'
        });
        
        if (AO.User.user_level > 7) {
            this.getTabBar().insert(5, {
                xtype: 'tbtext',
                cls: 'ao-tab-text',
                text: 'AO Kranj Administracija >',
                margin: '0 4'
            });
        }
    },
    
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
