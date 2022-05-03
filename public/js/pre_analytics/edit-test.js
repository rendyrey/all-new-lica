var editTestTransactionId = '0';
var editTestRoomClass = '0';
var selectedEditTestIds = [];
var editTestBtn = function() {
  $("#edit-test-btn").on('click', function(){
    editTestTransactionId = $(this).data('transaction-id');
    editTestRoomClass = $(this).data('room-class');
    $.ajax({
      url: baseUrl('pre-analytics/edit-test/selected-test/'+editTestRoomClass+'/'+editTestTransactionId),
      type: 'POST',
      success: function(res){
        $("#selected-edit-test-ids").val(res.selected_test_ids);
        selectedEditTestIds = res.selected_test_ids.split(',');
        populateSelectedTest(res.data);
      }
    })
    
    $("#edit-test-modal").modal('show');
    const datatableNewUrl =  baseUrl('pre-analytics/edit-test/'+editTestRoomClass+'/'+editTestTransactionId+'/datatable');
    DatatableEditTestServerSide.refreshNewTable(datatableNewUrl);
  });
}

var addEditTestList = function(unique_id, type, name, price, roomClass, event) {
  $.ajax({
    url: baseUrl('pre-analytics/edit-test/add'),
    type: 'POST',
    data: {
      unique_id: unique_id,
      type: type,
      transaction_id: editTestTransactionId,
      room_class: roomClass
    },
    success: function(res) {
      event.target.closest('tr').remove();
      $("#selected-edit-test-ids").val(selectedEditTestIds.join(','));
      transactionTestTable.ajax.reload();
      transactionSpecimenTable.ajax.reload();
      DatatableEditTestServerSide.refreshTable();
      populateSelectedTest(res.data);

      toastr.success(res.message, "Add test successful!");
    }
  })
 
}

var removeEditTestList = function(transaction_test_id, event) {
  $.ajax({
    url: baseUrl('pre-analytics/edit-test/'+transaction_test_id+'/delete'),
    type: 'DELETE',
    success: function(res) {
      event.target.closest('tr').remove();
      DatatableEditTestServerSide.refreshTable();
      transactionTestTable.ajax.reload();
      transactionSpecimenTable.ajax.reload();
    }
  })
}

var populateSelectedTest = function (tests) {
  $("#selected-edit-test").html("<tr></tr>");
  tests.forEach(function(item,index) {
    const isEven = (index % 2 == 0);
    const price = item.price;
    const priceFormatted = (price != null && price != '' && price != 'null') ? 'Rp'+price.toLocaleString('ID') : '';
    const removeButton = !item.draw ? `<i onClick="removeEditTestList('`+item.transaction_test_id+`', event)" class="cursor-pointer bi bi-arrow-left-circle text-danger" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Remove `+item.name+` to test list"></i>` : '';
    $("#selected-edit-test tr:last").after(`
    <tr class="`+(isEven == true ? 'even':'odd')+`">
      <td>`+item.name+`</td>
      <td>`+priceFormatted+`</td>
      <td>`+item.type+`</td>
      <td class="text-end">
      `+removeButton+`
      </td>
    </tr>
  `);
  });
}

var DatatableEditTestServerSide = function () {
  // Shared variables
  var table;
  var dt;
  var filterPayment;
  var selectorName = '.edit-test-datatable-ajax';

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
              url: baseUrl('pre-analytics/edit-test/'+editTestRoomClass+'/'+editTestTransactionId+'/datatable'),
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
                      return `<i onClick="addEditTestList('`+row.unique_id+`','`+row.type+`','`+row.name+`', '`+price+`', '`+editTestRoomClass+`', event)" class="cursor-pointer bi bi-arrow-right-circle text-success" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Add `+row.name+` to test list"></i>`;
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
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-edit-test"]');
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

document.addEventListener('DOMContentLoaded', function () {
  editTestBtn();
  DatatableEditTestServerSide.init();
});