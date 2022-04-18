"use strict";
var masterData = 'test'; // required for the url
var withModel = ['group','specimen']; // required for the datatable if the model of the datatable has eager load or relationship

// required for the datatable columns
var buttonActionIndex = 10;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'name' },
    { data: 'initial' },
    { data: 'unit' },
    { data: 'volume' },
    { data: 'range_type' },
    { data: 'group', name: 'group.name', render: function(data, type, row){
            return data.name;
        } 
    },
    { data: 'sub_group' },
    { data: 'specimen', name: 'specimen.name', render: function(data, type, row){
            return data.name;
        }
    },
    { data: 'sequence' }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='initial']").val(data.initial);
    $("#modal_form_horizontal input[name='unit']").val(data.unit);
    $("#modal_form_horizontal input[name='volume']").val(data.volume);
    $("#modal_form_horizontal input[name='range_type']").val(data.range_type);
    $("#modal_form_horizontal select[name='group_id']").html(
        `<option value='`+data.group_id+`'>`+data.group.name+`</option>`
    )
    $("#modal_form_horizontal input[name='sub_group']").val(data.sub_group);
    $("#modal_form_horizontal select[name='specimen_id']").html(
        `<option value='`+data.specimen_id+`'>`+data.specimen.name+`</option>`
    )
    $("#modal_form_horizontal input[name='sequence']").val(data.sequence);
    $("#modal_form_horizontal select[name='range_type']").val(data.range_type).trigger('change');

    if ($("#modal_form_horizontal select[name='range_type']").val() == 'description') {
        $("#normal-notes-edit").removeClass('d-none');
        ckeditor.setData(data.normal_notes);
    }
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);

}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    initial: {
        required: true,
    },
    volume: {
        required: true,
        number: true
    },
    group_id: {
        required: true,
    },
    specimen_id: {
        required: true,
    },
    sequence: {
        required: true,
        digits: true
    },
    range_type: {
        required: true,
    },
    normal_notes: {
        required: true
    }
};

// this is for open select2 when pressing tab in keyboard
$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
    $(this).closest(".select2-container").siblings('select:enabled').select2('open');
});

var ckeditor;

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2ServerSide('group').init();
    Select2ServerSide('specimen').init();
    ClassicEditor
        .create(document.querySelector('#editor-full'), {
            toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ]
        })
        .then(editor => {
            console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });
    
        ClassicEditor
        .create(document.querySelector('#editor-full-edit'), {
            toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ]
        })
        .then(editor => {
            console.log(editor);
            ckeditor = editor;
        })
        .catch(error => {
            console.error(error);
        });

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $(".range-type").on('change', function (e) {
        if ($(this).val() == 'description') {
            $("#normal-notes").removeClass('d-none');
        } else {
            $("#normal-notes").addClass('d-none');
        }
    });
    
    $(".range-type-edit").on('change', function (e) {
        if ($(this).val() == 'description') {
            $("#normal-notes-edit").removeClass('d-none');
        } else {
            $("#normal-notes-edit").addClass('d-none');
        }
    })
});
