var baseUrl = function(url) {
  return base + url;
}

var buttonActionIndex = 6;
var columnsDataTable = [
  { data: 'created_at' },
  { data: 'id' },
  { data: 'lab_no' },
  { data: 'medrec' },
  { data: 'patient.name' },
  { data: 'room.name' },
];

// Datatable Component
var DatatablesServerSide = function () {
  // Shared variables
  var table;
  var dt;
  var filterPayment;
  var selectorName = '.pre-analytics-datatable-ajax';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          responsive: true,
          searchDelay: 500,
          processing: true,
          serverSide: true,
          order: [],
          stateSave: false,
          ajax: {
              url: baseUrl('pre-analytics/datatable/')
          },
          columns: columnsDataTable,
          columnDefs: [
              {
                  responsivePriority: 1,
                  targets: buttonActionIndex,
                  data: null,
                  orderable: false,
                  className: 'text-end',
                  searchable: false,
                  render: function (data, type, row) {
                      return `
                          <a href="#" class="btn btn-light-info btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                          <i class="fas fa-ellipsis-v"></i>
                          </a>
                          <!--begin::Menu-->
                          <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                              <!--begin::Menu item-->
                              <div class="menu-item px-3">
                                  <button class="btn btn-light-primary form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="editData(`+row.id+`)">
                                  <i class="fas fa-edit"></i>Edit
                                  </button>
                              </div>
                              <!--end::Menu item-->

                              <!--begin::Menu item-->
                              <div class="menu-item px-3">
                                  <button class="btn btn-light-danger form-control btn-sm my-1 px-3" data-kt-docs-table-filter="delete_row" onClick="deleteData(`+row.id+`)">
                                  <i class="fas fa-trash"></i> Delete
                                  </button>
                              </div>
                              <!--end::Menu item-->
                          </div>
                          <!--end::Menu-->
                      `;
                  },
              },
          ],
          // Add data-filter attribute
          // createdRow: function (row, data, dataIndex) {
          //     $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
          // }
      });

      table = dt.$;

      // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
      dt.on('draw', function () {
          initToggleToolbar();
          toggleToolbars();
          KTMenu.createInstances();
      });
  }


  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = function () {
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-pre-analytics"]');
      filterSearch.addEventListener('keyup', function (e) {
          dt.search(e.target.value).draw();
      });
  }

  // Init toggle toolbar
  var initToggleToolbar = function () {
      // Toggle selected action toolbar
      // Select all checkboxes
      const container = document.querySelector(selectorName);
      const checkboxes = container.querySelectorAll('[type="checkbox"]');
      
      // Toggle delete selected toolbar
      checkboxes.forEach(c => {
          // Checkbox on click event
          c.addEventListener('click', function () {
              setTimeout(function () {
                  // toggleToolbars();
              }, 50);
          });
      });
  }
  

  // Toggle toolbars
  var toggleToolbars = function () {
      // Define variables
      const container = document.querySelector(selectorName);
      const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
      const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');

      // Select refreshed checkbox DOM elements
      const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

      // Detect checkboxes state & count
      let checkedState = false;
      let count = 0;

      // Count checked boxes
      allCheckboxes.forEach(c => {
          if (c.checked) {
              checkedState = true;
              count++;
          }
      });

      // Toggle toolbars
      if (checkedState) {
          selectedCount.innerHTML = count;
          toolbarSelected.classList.remove('d-none');
      } else {
          toolbarSelected.classList.add('d-none');
      }
  }

  // Public methods
  return {
      init: function () {
          initDatatable();
          handleSearchDatatable();
          // initToggleToolbar();
          // handleFilterDatatable();
          // handleDeleteRows();
          // handleResetForm();
      },
      refreshTable: function() {
          dt.ajax.reload();
      }
  }
}();

var room_class = '0';
var getRoomClass = function () {
  $(".select-room").on('change', function(e) {
    const roomId = $(this).val();
    $.ajax({
      url: baseUrl('master/room/edit/'+roomId),
      method: 'GET',
      success: function(res) {
        room_class = res.class;
        const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable');
        DatatableTestServerSide.refreshNewTable(newUrl);
      }
    })
  });
}

var DatatableTestServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var selectorName = '.test-datatable-ajax';
  
    // Private functions
    var initDatatable = function () {
        dt = $(selectorName).DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[0, 'asc']],
            stateSave: false,
            ajax: {
                url: baseUrl('pre-analytics/test/'+room_class+'/datatable')
            },
            columns: [
              { data: 'name', searchable: true },
              { data: 'price', render: function(data, type, row){
                  if (data != null && data != '') {
                    return 'Rp'+data.toLocaleString('ID');
                  }
                  return '';
                }
              },
              { data: 'type' }
            ],
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 3,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    searchable: false,
                    render: function (data, type, row) {
                        return ``;
                    },
                },
            ],
            // Add data-filter attribute
            // createdRow: function (row, data, dataIndex) {
            //     $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
            // }
        });
  
        table = dt.$;
  
        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
            initToggleToolbar();
            toggleToolbars();
            KTMenu.createInstances();
        });
    }
  
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-test"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }
  
    // Init toggle toolbar
    var initToggleToolbar = function () {
        // Toggle selected action toolbar
        // Select all checkboxes
        const container = document.querySelector(selectorName);
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        
        // Toggle delete selected toolbar
        checkboxes.forEach(c => {
            // Checkbox on click event
            c.addEventListener('click', function () {
                setTimeout(function () {
                    // toggleToolbars();
                }, 50);
            });
        });
    }
    
  
    // Toggle toolbars
    var toggleToolbars = function () {
        // Define variables
        const container = document.querySelector(selectorName);
        const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');
  
        // Select refreshed checkbox DOM elements
        const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');
  
        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;
  
        // Count checked boxes
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });
  
        // Toggle toolbars
        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarSelected.classList.add('d-none');
        }
    }
  
    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            // initToggleToolbar();
            // handleFilterDatatable();
            // handleDeleteRows();
            // handleResetForm();
        },
        refreshTable: function() {
            dt.ajax.reload();
        },
        refreshNewTable: function(url) {
          dt.ajax.url(url).load();
        }
    }
}();

var DateRangePicker = () => {
  var start = moment();
  var end = moment();

  function cb(start, end) {
      $("#daterange-picker").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
      const startDate = $("#daterange-picker").data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $("#daterange-picker").data('daterangepicker').endDate.format('YYYY-MM-DD');
      console.log(startDate);
      console.log(endDate);
  }

  $("#daterange-picker").daterangepicker({
      startDate: start,
      endDate: end,
      ranges: {
      "Today": [moment(), moment()],
      "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
      "Last 7 Days": [moment().subtract(6, "days"), moment()],
      "Last 30 Days": [moment().subtract(29, "days"), moment()],
      "This Month": [moment().startOf("month"), moment().endOf("month")],
      "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
      },
      locale:{
        format: 'DD MMMM YYYY'
      }
  }, cb);

  cb(start, end);
}

var Select2ServerSide = function (theData, searchKey = 'name') {
  var _componentSelect2 = function() {
      // Initialize
      $('.select-' + theData).select2({
          allowClear: true,
          ajax: {
              url: baseUrl('master/select-options/' + theData + '/' + searchKey),
              dataType: 'json',
              delay: 250,
              data: function (params) {
                  return {
                      query: params.term // search term
                  };
              },
              processResults: function (data, params) {

                  // parse the results into the format expected by Select2
                  // since we are using custom formatting functions we do not need to
                  // alter the remote JSON data, except to indicate that infinite
                  // scrolling can be used
                  // params.page = params.page || 1;

                  return {
                      results: $.map(data, function(item){
                          
                          return {
                              text: item.name,
                              id: item.id
                          }
                      })
                  };
              },
              cache: true
          },
          // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          // minimumInputLength: 0,
          // tags: true, // for create new tags
          language: {
              inputTooShort: function () {
                  return 'Input is too short';
              },
              errorLoading: function () {
                  return `There's error on our side`;
              },
              noResults: function () {
                  return 'There are no result based on your search';
              }
          }
          
      });

  }

  //
  // Return objects assigned to module
  //

  return {
      init: function() {
          _componentSelect2();
      }
  }
}

var addNewPatient = () => {
  $(".add-new-patient").addClass('d-none');
  $(".cancel-new-patient").removeClass('d-none');

  // $("#new-pre-analytics .patient-form input").removeAttr('disabled');
  // $("#new-pre-analytics .patient-form input").removeAttr('readonly');
  // $("#new-pre-analytics .patient-form textarea").removeAttr('disabled');
  // $("#new-pre-analytics .patient-form textarea").removeAttr('readonly');

  $(".patient-form label").removeClass('text-muted');

  $("select[name='patient_id']").val('').trigger('change');
  $("select[name='patient_id']").prop('disabled', true);

}

var cancelNewPatient = () => {
  $(".add-new-patient").removeClass('d-none');
  $(".cancel-new-patient").addClass('d-none');

  $("#new-pre-analytics .patient-form input").attr('disabled', true);
  $("#new-pre-analytics .patient-form input").attr('readonly', true);
  $("#new-pre-analytics .patient-form textarea").attr('disabled', true);
  $("#new-pre-analytics .patient-form textarea").attr('readonly', true);

  $(".patient-form label").addClass('text-muted');

  $("select[name='patient_id']").prop('disabled', false);
}

var Stepper = () => {
   // Stepper lement
   var element = document.querySelector("#kt_stepper_example_basic");

   // Initialize Stepper
   var stepper = new KTStepper(element);
 
   // Handle next step
   stepper.on("kt.stepper.next", function (stepper) {
       stepper.goNext(); // go next step
   });
 
   // Handle previous step
   stepper.on("kt.stepper.previous", function (stepper) {
       stepper.goPrevious(); // go previous step
   });
}

var datepicker;
var birthdate = () => {
  datepicker = $(".birthdate").flatpickr({
    altInput: true,
    altFormat: 'j F Y',
    dateFormat: 'Y-m-d'
  });
}

var automaticFillPatientForm = function() {
  $(".select-patient").on('change', function (e) {
    var patientId = $(this).val();
    if (patientId != '' && patientId != null) {
      $.ajax({
        url: baseUrl('master/patient/edit/'+patientId),
        method: 'GET',
        success: function(res) {
          $(".patient-form input[name='name']").val(res.name);
          $(".patient-form input[name='email']").val(res.email);
          $(".patient-form input[name='phone']").val(res.phone);
          $(".patient-form input[name='medrec']").val(res.medrec);
          datepicker.setDate(res.birthdate);
          $(".patient-form input[name='gender']").prop('disabled', false);
          $(".patient-form input[name='gender']").prop('readonly', false);
          $(".patient-form input[name='gender'][value='"+res.gender+"']").trigger('click');
          $(".patient-form input[name='gender']").prop('disabled', true);
          $(".patient-form input[name='gender']").prop('readonly', true);
          $(".patient-form textarea[name='address']").val(res.address);
          areAllFilled();
        }
      });
    } else {
      $(".patient-form input.req-input").val('');
      $(".patient-form textarea").val('');
      areAllFilled();
    }
    
  });
}

var areAllFilled = function() {
  var filled = true;
  $('.patient-right-form select.req-input').each(function() {
      if($(this).val() == '' || $(this).val() == null) {
        filled = false;
      }
  });

  $(".patient-form input.req-input").each(function() {
    console.log($(this).val());
    if($(this).val() == '' || $(this).val() == null) {
      filled = false;
    }
  });
  
  if (filled == true) {
    $("#continue-btn").prop('disabled', false);
  } else {
    $("#continue-btn").prop('disabled', true);
  }
}



// On document ready
document.addEventListener('DOMContentLoaded', function () {
  DatatablesServerSide.init();
  DatatableTestServerSide.init();
  getRoomClass();
  automaticFillPatientForm();
  DateRangePicker();
  Select2ServerSide('patient').init();
  Select2ServerSide('insurance').init();
  Select2ServerSide('room','room').init();
  Select2ServerSide('doctor').init();
  Stepper();
  birthdate();

  $(".patient-form label").addClass('text-muted');

  $(".patient-right-form select.req-input").on('change', function(e) {
    areAllFilled();
  });

  $(".patient-form input.req-input").on('change keyup input', function(e) {
    areAllFilled();
  });

  
});