"use strict";
var masterData = 'interfacing'; // required for the url
var withModel = ['test','analyzer']; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 3;
var columnsDataTable = [
    { data: null, render: function(data, type, row) {
        return row.test.name;
      } 
    },
    { data: null, render: function(data, type, row) {
        return row.analyzer.name;
      } 
    },
    { data: 'code' }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal form").trigger('reset');
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal select[name='test_id']").html(
      `<option value='`+data.test_id+`' selected>`+data.test.name+`</option>`
    );
    $("#modal_form_horizontal select[name='analyzer_id']").html(
      `<option value='`+data.analyzer_id+`' selected>`+data.analyzer.name+`</option>`
    );
    $("#modal_form_horizontal input[name='code']").val(data.code);
}

// required for the form validation rules
var rulesFormValidation = {
    test_id: {
        required: true
    },
    analyzer_id: {
        required: true,
    },
    code: {
        required: true,
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2ServerSide('analyzer').init();
    Select2ServerSide('test').init();
    
    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});
