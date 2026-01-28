// Todo field type plug-in code
(function ($, DataTable) {

    if ( ! DataTable.ext.editorFields ) {
        DataTable.ext.editorFields = {};
    }

    var Editor = DataTable.Editor;
    var _fieldTypes = DataTable.ext.editorFields;

    _fieldTypes.tagify = {
        create: function ( conf ) {
            var editor = this;
            conf._enabled = true;

            // Create the elements to use for the input
            conf._input = $('<input/>').attr( $.extend( {
                id: DataTable.Editor.safeId( conf.id ),
                type: 'text'
            }, conf.attr || {} ) );
            conf._input = $('<div/>').append(conf._input );
            $('input', conf._input).tagify();

            return conf._input;
        },

        get: function ( conf ) {
            var values = $('input', conf._input).data('tagify').value;
            var ret=[];
            values.forEach(function(elem){
                ret.push(elem.value);
            })
           return ret.join();
        },

        set: function ( conf, val ) {
            $('input', conf._input).data('tagify').removeAllTags();
            if (val) $('input', conf._input).data('tagify').addTags(val.split(','))
        },

        enable: function ( conf ) {
            conf._enabled = true;
            $('input', conf._input).removeClass( 'disabled' );
        },

        disable: function ( conf ) {
            conf._enabled = false;
            $('input', conf._input).addClass( 'disabled' );
        }
    };

})(jQuery, jQuery.fn.dataTable);
