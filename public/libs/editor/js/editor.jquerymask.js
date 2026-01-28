(function( factory ){
    if ( typeof define === 'function' && define.amd ) {
        // AMD
        define( ['jquery', 'datatables', 'datatables-editor'], factory );
    }
    else if ( typeof exports === 'object' ) {
        // Node / CommonJS
        module.exports = function ($, dt) {
            if ( ! $ ) { $ = require('jquery'); }
            factory( $, dt || $.fn.dataTable || require('datatables') );
        };
    }
    else if ( jQuery ) {
        // Browser standard
        factory( jQuery, jQuery.fn.dataTable );
    }
}(function( $, DataTable ) {
    'use strict';


    if ( ! DataTable.ext.editorFields ) {
        DataTable.ext.editorFields = {};
    }

    var _fieldTypes = DataTable.Editor ?
        DataTable.Editor.fieldTypes :
        DataTable.ext.editorFields;


    _fieldTypes.mask = {
        create: function ( conf ) {
            conf._input = $('<input/>').attr( $.extend( {
                id: DataTable.Editor.safeId( conf.id ),
                type: 'text'
            }, conf.attr || {} ) );

            conf._input.mask( conf.mask, $.extend( {
                placeholder: conf.placeholder || conf.mask.replace(/[09#AS]/g, '_')
            }, conf.maskOptions ) );

            return conf._input[0];
        },

        get: function ( conf ) {
            return conf._input.cleanVal();
        },

        set: function ( conf, val ) {
            conf._input
                .val( val )
                .trigger( 'change' )
                .trigger( 'input.mask' )
                .trigger( 'keyup.mask' );
        },

        enable: function ( conf ) {
            conf._input.prop( 'disabled', false );
        },

        disable: function ( conf ) {
            conf._input.prop( 'disabled', true );
        },

        canReturnSubmit: function ( conf, node ) {
            return true;
        }
    };

}));
