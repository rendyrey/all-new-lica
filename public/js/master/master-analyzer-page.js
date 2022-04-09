"use strict";
var masterData = 'analyzer'; // required for the url

// required for the datatable columns
var responsiveButtonIndexColumn = 4;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'name', orderable: true },
    { data: 'group_id', render: function(data, type, row){
            // debugger;
            return row.group.name;
            // return row.name;
        }
    },
    {
        render: function (data, type, row) {
            let editBtn = 
                `<button type="button" class="btn btn-sm btn-primary btn-icon rounded-round" data-popup="tooltip" title="Edit data" data-placement="left" onClick="editData(`+row.id+`)">
                    <i class="icon-pencil5"></i>
                </button>`;
            let deleteBtn = 
                `<button type="button" class="btn btn-sm btn-danger btn-icon rounded-round" data-popup="tooltip" title="Delete data" data-placement="left" onClick="deleteData(`+row.id+`)">
                    <i class="icon-trash"></i>
                </button>`;
            return editBtn+ '&nbsp;&nbsp;' +deleteBtn;
        },
        responsivePriority: 1,
    },
    {
        render: function (data, type, row) {
            return '';
        }
    }
    
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal select[name='group_id']").html(
        `<option value='`+data.group_id+`'>`+ data.group.name +`</option>`
    );
    $("#modal_form_horizontal input[name='group_id']").val(data.group_id);
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    group_id: {
        required: true
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatableDataSources.init();
    Select2Selects('group', 'name').init();
    FormValidation.init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});
