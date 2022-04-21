"use strict";
var masterData = 'price'; // required for the url
var withModel = ['test','package']; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 5;
var columnsDataTable = [
    { data: 'test.name', render: function (data, type, row) {
        if (row.test_id != null && row.test_id != '') {
          return row.test.name;
        }
        return '-';
      }, defaultContent: ''
    },
    { data: 'package.name', render: function (data, type, row) {
        if (row.package_id != null && row.package_id != '') {
          return row.package.name;
        }
        return '-';
      }, defaultContent: ''
    },
    { data: 'price', render: function (data, type, row) {
        return 'Rp' + data.toLocaleString('ID');
      }
    },
    { data: 'type' },
    { data: 'class' }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal form").trigger('reset');
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    if (data.type == 'test') {
      $("#test-type").trigger('click');
      $("#modal_form_horizontal select[name='test_id']").html(
        `<option value='`+data.test_id+`' selected>`+data.test.name+`</option>`
      );
    } else {
      $("#package-type").trigger('click');
      $("#modal_form_horizontal select[name='package_id']").html(
        `<option value='`+data.package_id+`' selected>`+data.package.name+`</option>`
      );
    }
    $("#modal_form_horizontal input[name='class']").val(data.class);
    $("#modal_form_horizontal input[name='price']").val(data.price);
}

// required for the form validation rules
var rulesFormValidation = {
    "class_price[0][class]": {
        required: true
    },
    "class_price[0][price]": {
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

    $('#form-create input[name="type"]').on('change', function(e) {
      const type = $(this).val();

      if (type == 'test') {
        $("#test-list").removeClass('d-none');
        $("#package-list").addClass('d-none');
      } else {
        $("#test-list").addClass('d-none');
        $("#package-list").removeClass('d-none');
      }
    });

    $('#form-edit input[name="type"]').on('change', function(e) {
      const type = $(this).val();

      if (type == 'test') {
        $("#test-list-edit").removeClass('d-none');
        $("#package-list-edit").addClass('d-none');
      } else {
        $("#test-list-edit").addClass('d-none');
        $("#package-list-edit").removeClass('d-none');
      }
    });

    $('#class_price').repeater({
      initEmpty: false,
  
      defaultValues: {
          'text-input': 'foo'
      },
  
      show: function () {
          $(this).slideDown();
      },
  
      hide: function (deleteElement) {
          $(this).slideUp(deleteElement);
      }
  });
});
