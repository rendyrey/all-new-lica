var baseUrl = function(url) {
  return base + url;
}

var theFullDate = function(date) {
  const monthNames = [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
  ];
  let d = new Date(date);
  let day = d.getDate();
  let month = d.getMonth();
  let year = d.getFullYear();
  let theDate = day + ' ' + monthNames[month] + ' ' + year;

  return theDate;
}

var newFormId = '#new-pre-analytics';

var buttonActionIndex = 6;
var columnsDataTable = [
  { data: 'created_at', render: function(data, type, row) {
      return theFullDate(data);
    }
  },
  { data: 'transaction_id_label' },
  { data: 'no_lab' },
  { data: 'patient.medrec' },
  { data: 'patient.name' },
  { data: 'room.room' },
];

// Datatable Component
var selectedTransactionId;
var DatatablesServerSide = function () {
  // Shared variables
  var table;
  var dt;
  var filterPayment;
  var selectorName = '.pre-analytics-datatable-ajax';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          select: {
            style: 'single'
          },
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
                              <button class="btn btn-light-danger btn-sm px-2" data-kt-docs-table-filter="delete_row" onClick="deleteTransaction(`+row.id+`)">
                                  delete
                              </button>
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

      dt.on('select', function(e, data, type, indexes) {
        const selectedData = data.rows().data()[indexes];
        selectedTransactionId = selectedData.id;
        onSelectTransaction(selectedData);
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
      },
      refreshTableAjax: function(url) {
          dt.ajax.url(url).load();
      }
  }
}();

var room_class = '0';
var getRoomClass = function () {
  $(".select-room").on('change', function(e) {
    const roomId = $(this).val();
    if (roomId != null && roomId != '') {
      $.ajax({
        url: baseUrl('master/room/edit/'+roomId),
        method: 'GET',
        success: function(res) {
          room_class = res.class;
          const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable');
          DatatableTestServerSide.refreshNewTable(newUrl);
        }
      })
    } 
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
                url: baseUrl('pre-analytics/test/'+room_class+'/datatable'),
                method: 'POST'
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
                        const price = (row.price != null && row.price != '') ? row.price : '';
                        return `<i onClick="addTestList('`+row.unique_id+`','`+row.type+`','`+row.name+`', '`+price+`', event)" class="cursor-pointer bi bi-arrow-right-circle text-success" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Add `+row.name+` to test list"></i>`;
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

var checkSpecimenDrawStatus = function(transactionId){
  $.ajax({
    url: baseUrl('pre-analytics/specimen-test/is-all-drawn/'+transactionId),
    method: 'GET',
    success: function(res) {
      console.log(res);
      if (res.all_drawn) {
        $("#undraw-all-btn").show();
        $("#draw-all-btn").hide();    
      } else {
        $("#draw-all-btn").show();
        $("#undraw-all-btn").hide();
      }
    }
  });
}

var transactionTestTable;
var transactionSpecimenTable;
var onSelectTransaction = function (selectedData) {
  const patient = selectedData.patient;
  const room = selectedData.room;
  const transactionId = selectedData.id;
  checkSpecimenDrawStatus(transactionId);

  $("#draw-all-btn").prop('disabled', false);
  $("#undraw-all-btn").prop('disabled', false);
  $("#draw-all-btn").val(transactionId);
  $("#undraw-all-btn").val(transactionId);

  $(".name-detail").html(patient.name);
  $(".gender-detail").html((patient.gender == 'M' ? 'Male' : 'Female'));
  $(".email-detail").html(patient.email);
  $(".phone-detail").html(patient.phone);
  $(".age-detail").html(getAge(patient.birthdate));
  $(".insurance-detail").html(selectedData.insurance.name);
  // for check in button
  const autoCheckin = (room.auto_checkin == 1 || room.auto_checkin == '1' || room.auto_checkin == true);
  const hasCheckedIn = (selectedData.checkin_time != null && selectedData.checkin_time != '');
  if (hasCheckedIn) {
    $("#check-in-btn").html('No. Lab: ' + selectedData.no_lab);
    $("#check-in-btn").data('has-checked-in', true);
  } else {
    $("#check-in-btn").html('Check in');
    $("#check-in-btn").data('has-checked-in', false);
    $("#draw-all-btn").prop('disabled', true);
    $("#undraw-all-btn").prop('disabled', true);
  }
  $("#check-in-btn").data('transaction-id', transactionId);
  $("#check-in-btn").prop('disabled', false);
  $("#check-in-btn").data('auto-checkin', autoCheckin);

  $("#edit-test-btn").data('transaction-id', transactionId);
  $("#edit-test-btn").data('room-class', room.class);
  // end for check in button

  switch (selectedData.type) {
    case 'rawat_jalan':
       patientType = 'Rawat Jalan';
       break;
    case 'rawat_inap':
      patientType = 'Rawat Inap';
      break;
    case 'igd':
      patientType = 'IGD';
      break;
    case 'rujukan':
      patientType = 'Rujukan';
      break;
    default:
      patientType = '-';
  }

  $(".type-detail").html(patientType);
  $(".room-detail").html(selectedData.room.room);
  $(".medrec-detail").html(patient.medrec);
  $(".doctor-detail").html(selectedData.doctor.name);

  // Check if #it is a DataTable or not. If not, initialise:, if so, reload with new url
  if ( ! $.fn.DataTable.isDataTable( '.transaction-test-table' ) ) {
    transactionTestTable = $('.transaction-test-table').DataTable({
      responsive: true,
      searchDelay: 500,
      processing: true,
      serverSide: true,
      order: [],
      stateSave: false,
      ajax: {
          url: baseUrl('pre-analytics/transaction-test/'+transactionId+'/datatable/'),
          complete: function(data) {
            const testData = data.responseJSON.data;
            testData.forEach(function(item){
              $.ajax({
                url: baseUrl('pre-analytics/analyzer-test/'+item.test_id),
                type: 'GET',
                success: function(res) {
                  $("#select-analyzer-"+item.id).html(res);
                  $("#select-analyzer-"+item.id).select2({allowClear:true});
                  $("#select-analyzer-"+item.id).val(item.analyzer_id).trigger('change');
                  $("#select-analyzer-"+item.id).attr('onChange',"analyzerChange("+item.id+", event)");
                }
              })
            });
          }
      },
      columns: [
        { data: 'test.name' },
        { data: 'test_id', render: function(data, type, row) {
            const selectComponent = `
              <select id="select-analyzer-`+row.id+`" data-control="select2" data-placeholder="Select analyzer" class="select form-select form-select-sm form-select-solid my-0">
              </select>
            `;
           
            return selectComponent;
            
          }, defaultContent: ''
        }
      ]
    });
  } else {
    transactionTestTable.ajax.url(baseUrl('pre-analytics/transaction-test/'+transactionId+'/datatable/')).load();
  }

  if ( ! $.fn.DataTable.isDataTable( '.transaction-specimen-table' ) ) {
    transactionSpecimenTable = $('.transaction-specimen-table').DataTable({
      responsive: true,
      searchDelay: 500,
      processing: true,
      serverSide: true,
      order: [],
      stateSave: false,
      ajax: {
          url: baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable/')
      },
      columns: [
        { data: 'specimen.name', render: function(data, type, row){
            return data;
          } 
        },
        { data: 'volume', render: function(data, type, row) {
            return data + row.unit;
          }, defaultValue: ''
        },
        { data: 'test_ids', render: function(data, type, row) {
            const checked = row.draw.includes('1') ? 'checked' :'';
            const disabled = ((row.no_lab != null && row.no_lab != '') ? '' : 'disabled');
            const checkboxComponent = `
              <input class="specimen-checkbox" id="specimen-test-`+row.transaction_id+`-`+row.specimen_id+`" type="checkbox" value="`+row.test_ids+`" onChange="drawSpecimenChange(`+row.transaction_id+`,`+row.specimen_id+`,event)" `+checked+` `+disabled+`>
            `;
            return checkboxComponent;
          }, sortable: false, searchable: false
        }
      ]
    });
  } else {
    transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable/')).load();
  }

}

var deleteTransaction = function (id) {
  Swal.fire({
      title: 'Are you sure?',
      text: 'You will not be able to recover this data!',
      // type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: 'btn btn-secondary'
      }
  }).then(function(isConfirm){
      if(isConfirm.value) {
          $.ajax({
              url: baseUrl('pre-analytics/transaction/delete/'+id),
              method: 'DELETE',
              success: function(res) {
                  DatatablesServerSide.refreshTable();
                  transactionSpecimenTable.ajax.reload();
                  transactionTestTable.ajax.reload();
                  toastr.success(res.message, "Delete Success!");
              },
              error: function(request, status, error){
                  toastr.error(request.responseJSON.message);
              }
          })
      }
  });
}

function analyzerChange(transactionTestId, event){
  const analyzerId = event.target.value;
  $.ajax({
    url: baseUrl('pre-analytics/transaction-test/update-analyzer/'+transactionTestId),
    data: {
      analyzer_id: analyzerId
    },
    type: 'POST',
    success: function(res) {
      toastr.success(res.message, "Update analyzer success!");
    }
  })
}

function drawSpecimenChange(transactionId, specimenId, event) {
  console.log("TRANSACTIONID " + transactionId);
  $.ajax({
    url: baseUrl('pre-analytics/specimen-test/update-draw'),
    type: 'POST',
    data: {
      transaction_id: transactionId,
      specimen_id: specimenId,
      test_ids: event.target.value
    },
    success: function(res) {
      toastr.success(res.message, "Update draw status success!");
      transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
      checkSpecimenDrawStatus(transactionId);
    }
  })
}

var drawAllBtnComponent = function() {
  $("#draw-all-btn").on('click', function(e) {
    const transactionId = $(this).val();
    $.ajax({
      url: baseUrl('pre-analytics/specimen-test/draw-all/1'),
      type: 'POST',
      data: { transaction_id: transactionId },
      success: function(res){
        toastr.success(res.message, 'Success draw all specimen!');
        transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
        checkSpecimenDrawStatus(transactionId);
      }
    });
  });

  $("#undraw-all-btn").on('click', function(e) {
    const transactionId = $(this).val();
    $.ajax({
      url: baseUrl('pre-analytics/specimen-test/draw-all/0'),
      type: 'POST',
      data: { transaction_id: transactionId },
      success: function(res){
        toastr.success(res.message, 'Success undraw all specimen!');
        transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable/')).load();
        checkSpecimenDrawStatus(transactionId);
      }
    });
  });
}

function getAge(dateString) {
  var today = new Date();
  var birthDate = new Date(dateString);
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
  }

  return age;
}

var DateRangePicker = () => {
  var start = moment();
  var end = moment();

  function cb(start, end) {
      $("#daterange-picker").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
      const startDate = $("#daterange-picker").data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $("#daterange-picker").data('daterangepicker').endDate.format('YYYY-MM-DD');
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

  $("#daterange-picker").on('change', function () {
    const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
    const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
    const url = baseUrl('pre-analytics/datatable/'+startDate+'/'+endDate);
    DatatablesServerSide.refreshTableAjax(url);
  });
}

var Select2ServerSideModal = function (theData, searchKey = 'name', params) {
  var _componentSelect2 = function() {
      // Initialize
       $('.select-' + theData).select2({
          allowClear: true,
          ajax: {
              url: baseUrl('master/select-options/' + theData + '/' + searchKey + (params ? '/' + params : '')),
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
                        var additionalText = '';
                        if (theData == 'room') {
                            additionalText = `<i> Class `+item.class+`</i>`;
                        }      
                        return {
                            text: item.name + additionalText,
                            id: item.id
                        }
                    })
                };
              },
              cache: true
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
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
          },
          dropdownParent: $("#add-patient-modal .modal-body")
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

  $(newFormId + " .patient-form input").removeAttr('disabled');
  $(newFormId + " .patient-form input").removeAttr('readonly');
  $(newFormId + " .patient-form textarea").removeAttr('disabled');
  $(newFormId + " .patient-form textarea").removeAttr('readonly');

  $(".patient-form label").removeClass('text-muted');
   // Reset the patient form
   $(newFormId + ' .patient-form input[type="text"]').val('');
   $(newFormId + ' .patient-form textarea').val('');
   $(newFormId + ' .patient-form .invalid-feedback').remove();

  $("select[name='patient_id']").val('').trigger('change');
  $("select[name='patient_id']").prop('disabled', true);
  areAllFilled();

}

var cancelNewPatient = () => {
  $(".add-new-patient").removeClass('d-none');
  $(".cancel-new-patient").addClass('d-none');

  // disabled all the patient form
  $(newFormId + " .patient-form input").attr('disabled', true);
  $(newFormId + " .patient-form input").attr('readonly', true);
  $(newFormId + " .patient-form textarea").attr('disabled', true);
  $(newFormId + " .patient-form textarea").attr('readonly', true);

  // mute all label on patient form
  $(".patient-form label").addClass('text-muted');

  $("select[name='patient_id']").prop('disabled', false);

  // Reset the patient form
  $(newFormId + ' .patient-form input[type="text"]').val('');
  $(newFormId + ' .patient-form textarea').val('');
  $(newFormId + ' .patient-form .invalid-feedback').remove();
  areAllFilled();
}

var selectedTestIds = [];
var addTestList = function(unique_id, type, name, price, event) {
  selectedTestIds.push(unique_id);
  const isEven = (selectedTestIds.length % 2 == 0);
  const priceFormatted = (price != 'null' && price != '' && price != null) ? 'Rp'+price.toLocaleString('ID') : '';
  $("#selected-test tr:last").after(`
    <tr class="`+(isEven == true ? 'even':'odd')+`">
      <td>`+name+`</td>
      <td>`+priceFormatted+`</td>
      <td>`+type+`</td>
      <td class="text-end">
      <i onClick="removeTestList('`+unique_id+`', event)" class="cursor-pointer bi bi-arrow-left-circle text-danger" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Remove `+name+` to test list"></i>
      </td>
    </tr>
  `);
  event.target.closest('tr').remove();
  $("#selected-test-ids").val(selectedTestIds.join(','));
  const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable/withoutId/'+selectedTestIds.join(','));
  DatatableTestServerSide.refreshNewTable(newUrl);
}

var removeTestList = function(unique_id, event) {
  event.target.closest('tr').remove();
  let indexRemove = selectedTestIds.indexOf(unique_id);
  selectedTestIds.splice(indexRemove, 1);
  $("#selected-test-ids").val(selectedTestIds.join(','));
  if (selectedTestIds.length > 0) {
    const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable/withoutId/'+selectedTestIds.join(','));
    DatatableTestServerSide.refreshNewTable(newUrl);
  } else {
    const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable');
    DatatableTestServerSide.refreshNewTable(newUrl);
  }
}

var Stepper = () => {
   // Stepper lement
   var element = document.querySelector("#kt_stepper_example_basic");

   // Initialize Stepper
   var stepper = new KTStepper(element);
 
   // Handle next step
   stepper.on("kt.stepper.next", function (stepper) {
      $(newFormId).validate();
      if ($(newFormId).valid()) {
        stepper.goNext(); // go next step
        $(".stepper-number-2").addClass('text-white');
        $(".stepper-number-2").removeClass('text-primary');
        $(".stepper-icon-2").addClass('bg-primary');
      }
   });
 
   // Handle previous step
   stepper.on("kt.stepper.previous", function (stepper) {
       stepper.goPrevious(); // go previous step
        $(".stepper-number-2").removeClass('text-white');
        $(".stepper-number-2").addClass('text-primary');
        $(".stepper-icon-2").removeClass('bg-primary');
        $("#selected-test").html('<tr></tr>'); // remove body of table on selected test
        selectedTestIds = []; // set the selected test id to empty

        // refresh the test table because did the back button
        const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable');
        DatatableTestServerSide.refreshNewTable(newUrl);
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

// required for the form validation rules
var rulesFormValidation = {
  name: {
    required: true
  },
  email: {
    email: true
  },
  phone: {
    digits: true
  },
  birthdate: {
    required: true
  },
  gender: {
    required: true
  }
};
// Form Validation Component
var FormValidation = function() {
  //
  // Setup module components
  //

  // Validation config
  var _componentValidation = function() {
      if (!$().validate) {
          console.warn('Warning - validate.min.js is not loaded.');
          return;
      }

      // Initialize
      $('#form-create').validate({
          ignore: 'input[type=hidden], .select2-search__field, .ignore-this', // ignore hidden fields
          errorClass: 'fv-plugins-message-container invalid-feedback',
          successClass: 'validation-valid-label',
          validClass: 'validation-valid-label',
          highlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          unhighlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          // success: function(label) {
          //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
          // },

          // Different components require proper error label placement
          errorPlacement: function(error, element) {

              // Unstyled checkboxes, radios
              if (element.parents().hasClass('form-check')) {
                  error.appendTo( element.parents('.form-check').parent() );
              }

              // Input with icons and Select2
              else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                  error.appendTo( element.parent() );
              }

              // Input group, styled file input
              else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                  error.appendTo( element.parent().parent() );
              }

              // Other elements
              else {
                  error.insertAfter(element);
              }
          },
          rules: rulesFormValidation,
          messages: {
              custom: {
                  required: 'This is a custom error message'
              }
          },
          // submitHandler: function(form, event) {
          // }
      });

      $(newFormId).validate({
          ignore: 'input[type=hidden], .select2-search__field, .ignore-this', // ignore hidden fields
          errorClass: 'fv-plugins-message-container invalid-feedback',
          successClass: 'validation-valid-label',
          validClass: 'validation-valid-label',
          highlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          unhighlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          // success: function(label) {
          //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
          // },

          // Different components require proper error label placement
          errorPlacement: function(error, element) {

              // Unstyled checkboxes, radios
              if (element.parents().hasClass('form-check')) {
                  error.appendTo( element.parents('.form-check').parent() );
              }

              // Input with icons and Select2
              else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                  error.appendTo( element.parent() );
              }

              // Input group, styled file input
              else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                  error.appendTo( element.parent().parent() );
              }

              // Other elements
              else {
                  error.insertAfter(element);
              }
          },
          rules: rulesFormValidation,
          messages: {
              custom: {
                  required: 'This is a custom error message'
              }
          },
          // submitHandler: function(form, event) {
          // }
      });

      // Reset form
      // $('#reset').on('click', function() {
      //     validatorCreate.resetForm();
      // });
  };


  //
  // Return objects assigned to module
  //

  return {
      init: function() {
          _componentValidation();
      }
  }
}();

var createNewData = function() {
  let formData = $(newFormId).serialize();
  let theForm = $(newFormId);
  $.ajax({
    url: baseUrl('pre-analytics/create'),
    method: 'POST',
    data: formData,
    success: function(res) {
        toastr.success(res.message, "Create Success!"); // give notification
        $("#add-patient-modal").modal('hide'); // hide the modal
        $(newFormId).trigger('reset'); // reset the form
        $(newFormId + ' .patient-right-form select').val('').trigger('change'); // reset the select form manually
        $("select[name='patient_id']").val('').trigger('change'); // reset the patient select manually
        $("#selected-test").html('<tr></tr>'); // remove body of table
        selectedTestIds = []; // set the selected test id to empty
        $("#back-btn").trigger('click'); // click back manually on stepper
        DatatablesServerSide.refreshTable();
        $("#select-room").prop('disabled', true);
    },
    error: function (request, status, error) {
        toastr.error(request.responseJSON.message);
    }
  })
}

var checkInBtn = function () {
  $("#check-in-btn").on('click', function(e) {
    if ($(this).data('has-checked-in')) {
      return;
    }
    const transactionId = $(this).data('transaction-id');

    if ($(this).data('auto-checkin')) {
      $.ajax({
        url: baseUrl('pre-analytics/check-in/0'),
        type: 'POST',
        data: {
          transaction_id: transactionId
        },
        success: function(res) {
          toastr.success(res.message, 'Patient successfully checked-in!');
          $("#check-in-btn").html('No. Lab: ' + res.no_lab);

          DatatablesServerSide.refreshTable();
          transactionSpecimenTable.ajax.reload();
          transactionTestTable.ajax.reload();
          $(".draw-btn").prop('disabled', false);
          
        }
      })
    } else {
      Swal.fire({
        title: 'Set No. Lab',
        html: `<input type="text" id="no-lab" class="swal2-input" placeholder="No. Lab" minLength="3" maxLength="3">`,
        buttonsStyling: false,
        confirmButtonText: 'Check In',
        allowEnterKey: true,
        focusConfirm: false,
        preConfirm: () => {
          const noLab = Swal.getPopup().querySelector('#no-lab').value;
          if (!noLab) {
            Swal.showValidationMessage(`Please enter No. Lab`)
          }
          let isnum = /^\d+$/.test(noLab);
          if (!isnum) {
            Swal.showValidationMessage(`Please enter a Valid No. Lab`)
          }
          return { noLab: noLab }
        },
        customClass: {
            confirmButton: "btn btn-primary",
            title: "font-weight-bold"
        }
      }).then((result) => {
        $.ajax({
          url: baseUrl('pre-analytics/check-in/1'),
          type: 'POST',
          data: {
            transaction_id: transactionId,
            no_lab: result.value.noLab
          },
          success: function(res) {
            toastr.success(res.message, 'Patient successfully checked-in!');
            $("#check-in-btn").html('No. Lab: ' + res.no_lab);
            DatatablesServerSide.refreshTable();
            transactionSpecimenTable.ajax.reload();
            transactionTestTable.ajax.reload();
            $(".draw-btn").prop('disabled', false);
          },
          error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
          }
        })
      });
    }
  });
}


$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

// this is for document dynamically binding element event handlers
$(document).on('keyup', function (e) {
  var target = $(e.target);
  if (target.is('#no-lab')) {
    if (e.key === 'Enter') {
      $(".swal2-confirm").trigger('click');
    }
  }
});

$(document).on('click', function(e) {
  var target = $(e.target);
  if (target.is('.swal2-confirm')) {
  }
});
// end for document dynamically binding element event handlers


// On document ready
document.addEventListener('DOMContentLoaded', function () {
  DatatablesServerSide.init();
  DatatableTestServerSide.init();
  FormValidation.init();
  getRoomClass();
  automaticFillPatientForm();
  DateRangePicker();
  Select2ServerSideModal('patient').init();
  Select2ServerSideModal('insurance').init();
  Select2ServerSideModal('room','room', '').init();
  Select2ServerSideModal('doctor').init();
  Stepper();
  birthdate();
  drawAllBtnComponent();
  checkInBtn();

  $("#select-type").on('change', function () {
    if ($(this).val()) {
      const roomType = $(this).val();
      $(".select-room").prop('disabled', false);
      $("#select-room").select2('destroy');
      $("#select-room").val('').trigger('change');
      Select2ServerSideModal('room','room', roomType).init();
    }
  });

  $(".patient-form label").addClass('text-muted');

  $(".patient-right-form select.req-input").on('change', function(e) {
    areAllFilled();
  });

  $(".patient-form input.req-input").on('change keyup input', function(e) {
    areAllFilled();
  });

  $(newFormId).on('submit', function(e) {
    e.preventDefault();
    if (selectedTestIds.length == 0) {
      Swal.fire({
        text: "Please select at least 1 test!",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: {
            confirmButton: "btn btn-primary"
        }
      });
      return false;
    }

    if ($(this).valid()) {
      createNewData();
    }
  });
});