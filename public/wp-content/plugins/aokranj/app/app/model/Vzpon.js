Ext.define('AO.model.Vzpon', {
    extend: 'Ext.data.Model',
    
    fields: [
        {name:'id',type:'integer'},
        {name:'user_id',type:'integer'},
        {name:'tip'},
        {name:'destinacija'},
        {name:'smer'},
        {name:'datum',type:'date'},
        {name:'ocena'},
        {name:'cas'},
        {name:'vrsta'},
        {name:'visina_smer'},
        {name:'visina_izstop'},
        {name:'pon_vrsta'},
        {name:'pon_nacin'},
        {name:'stil'},
        {name:'mesto'},
        {name:'partner'},
        {name:'opomba'}
    ],
    
    tip: function() {
        switch (this.get('tip')) {
            case 'ALP':  return 'alpinistična smer';
            case 'ŠP':   return 'športno plezalna smer';
            case 'SMUK': return 'smuk';
            case 'PR':   return 'pristop';
        }
        return null;
    },
    
    vrsta: function() {
        switch (this.get('vrsta')) {
            case 'K':  return 'kopna';
            case 'L':  return 'ledna (snežna)';
            case 'LK': return 'ledna kombinirana';
        }
        return null;
    },
    
    ponovitev: function() {
        switch (this.get('pon_vrsta')) {
            case 'Prv': return 'prvenstvena';
            case '1P':  return 'prva ponovitev';
            case '2P':  return 'druga ponovitev';
            case 'ZP':  return 'zimska ponovitev';
        }
        return null;
    },
    
    nacin: function() {
        switch (this.get('pon_nacin')) {
            case 'PP': return 'prosta ponovitev';
            case 'NP': return 'na pogled';
            case 'RP': return 'z rdečo piko';
        }
        return null;
    },
    
    stil: function() {
        switch (this.get('stil')) {
            case 'A':  return 'alpski';
            case 'K':  return 'kombinirani';
            case 'OS': return 'odpravarski';
        }
        return null;
    },
    
    mesto: function() {
        switch (this.get('mesto')) {
            case 'V': return 'vodstvo';
            case 'D': return 'drugi';
            case 'Ž': return 'žimarjenje';
            case 'I': return 'izmenjaje';
        }
        return null;
    },
    
    opomba: function() {
        switch (this.get('opomba')) {
            case 'AS':  return 'alpinistični smuk';
            case 'BV':  return 'balvan';
            case 'ZS':  return 'zaledeneli slap';
        }
        return null;
    }
    
});
