"use strict";
var masterData = 'package'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 3;
var columnsDataTable = [
    { data: 'name' },
    { data: 'general_code' },
    { data: null, render: function(data, type, row){
            let tests = [];
            if (row.package_tests.length > 0) {
                row.package_tests.forEach(function(item) {
                    tests.push(`<span class="badge badge-secondary">`+item.test.name+`</span>`);
                });
                return tests.join(" ");
            }
        }, searchable: false
    }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);
    $("#modal_form_horizontal select[name='test_ids[]']").html('');
    data.package_tests.forEach(function(item) {
        $("#modal_form_horizontal select[name='test_ids[]']").append(
            `<option value='`+item.test_id+`' selected>`+ item.test.name +`</option>`
        );
    });
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    "test_ids[]": {
        required: true,
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2ServerSide('test').init();
    Select2ServerSide('package').init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $("#from-another-packages").on('change', function(e) {
        if ($(this).is(':checked')) {
            $("#package-list").removeClass('d-none');
        } else {
            $("#package-list").addClass('d-none');
        }
    })

    $("#select-package-list").on('change', function(e) {
        var packageList = $(this).val();
        if (packageList != '') {
            $.ajax({
                url: baseUrl('master/test-packages/'+packageList.toString()),
                method: 'GET',
                success: function(res){
                    var selectedTestIds = [];
                    var options = '';
                    res.forEach(function(item){
                        // select distinct test id
                        if (selectedTestIds.includes(item.test_id) == false) {
                            selectedTestIds.push(item.test_id);
                            options += `<option value='`+item.test_id+`' selected>`+ item.test.name +`</option>`;
                        }
                    })
                    $("#form-create select[name='test_ids[]']").html(options);
                }
            })
        }
        
    });
});
