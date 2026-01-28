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
 
 
_fieldTypes.datetime = {
    create: function ( conf ) {
        var that = this;
 
        conf._input = $(
                /*
                '<div class="input-group date" id="'+conf.id+'">'+
                    '<input type="text" class="form-control" />'+
                    '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>'+
                    '</span>'+
                '</div>'
                */
                '<input type="text" class="form-control" id="'+conf.id+'"/>'
            )
            .attr( $.extend( {}, conf.attr ) )
            .datetimepicker( $.extend( {}, conf.opts ) );
  
        return conf._input[0];
    },
 
    get: function ( conf ) {
        //return conf._input.children('input').val();
        return conf._input.val();
    },
 
    set: function ( conf, val ) {
        var picker = conf._input.data("DateTimePicker");
 
        if ( picker.setDate ) {
            picker.setDate( val );
        }
        else {
            picker.date( val );
        }
    },
 
    disable: function ( conf ) {
        conf._input.data("DateTimePicker").disable();
    },
 
    enable: function ( conf ) {
        conf._input.data("DateTimePicker").enable();
    },
 
    // Non-standard Editor methods - custom to this plug-in. Return the jquery
    // object for the datetimepicker instance so methods can be called directly
    inst: function ( conf ) {
        return conf._input.data("DateTimePicker");
    }
};
 
 
}));