var buttonActionIndex = 4;
var analyticsColumnDatatable = [
  { data: 'no_lab' },
  { data: 'patient.medrec' },
  { data: 'room.room' },
  { data: 'patient.name' },
  { data: null, render: function(data, type, row) {
    return "action";
  }, defaultContent: '', responsivePriority: 1},
];

var labelType = function (transactionTestId, tabIndex) {
  var input = `
    <div id="result-html-`+transactionTestId+`">
      <select tabindex="`+tabIndex+`" id="select-result-label-`+transactionTestId+`" data-control="select2" data-placeholder="Select label" class="select form-select form-select-sm form-select-solid my-0 me-4">
      </select>
    </div>
  `;
  return input;
}

var descriptionType = function () {
  var input = `
    <div id="result-html-`+transactionTestId+`">
      <select tabindex="`+tabIndex+`" id="select-result-label-`+transactionTestId+`" data-control="select2" data-placeholder="Select label" class="select form-select form-select-sm form-select-solid my-0 me-4">
      </select>
    </div>
  `;
  return input;
}

var numberType = function (transactionTestId, tabIndex) {
  var input = `
    <div id="result-html-`+transactionTestId+`">
      <select tabindex="`+tabIndex+`" id="select-result-label-`+transactionTestId+`" data-control="select2" data-placeholder="Select label" class="select form-select form-select-sm form-select-solid my-0 me-4">
      </select>
    </div>
  `;

  return input;
}

var freeFormatType = function (transactionTestId, tabIndex) {
  var input = `
    
  `;
}

var transactionTestColumnTable = [
  { data: 'test.name' },
  { data: 'test.range_type', render: function(data, type, row) {
      switch (data) {
        case 'description':
          return 'description';
        case 'number':
          return numberType(row.tt_id, row.DT_RowIndex);
        case 'label':
          return labelType(row.tt_id, row.DT_RowIndex);
        default:
          return 'invalid';
      }
    }
  },
  { data: 'test.name' },
  { data: 'test.name' },
  { data: 'test.name' },
  { data: 'test.name' }
];

var DatatableAnalytics = function () {
  // Shared variables
  var table;
  var dt;
  var selectorName = '.analytics-datatable-ajax';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          "createdRow": function( row, data, dataIndex){
            console.log("ANJAY", data);
            if(data.is_critical){
              $(row).addClass('bg-danger');
            }
          },
          paging: false,
          scrollY: '400px',
          scrollX: '100%',
          select: {
            style: 'single'
          },
          order: [[0, 'desc']],
          responsive: true,
          searchDelay: 500,
          processing: true,
          serverSide: true,
          stateSave: false,
          ajax: {
              url: baseUrl('analytics/datatable')
          },
          columns: analyticsColumnDatatable,
          columnDefs: [
              {
                  responsivePriority: 1,
                  targets: buttonActionIndex,
                  data: null,
                  orderable: false,
                  className: 'text-end',
                  searchable: false,
                  render: function(data, type, row) {
                      return `
                              <button class="btn btn-light-danger btn-sm px-2" data-kt-docs-table-filter="delete_row" onClick="deleteTransaction(`+row.id+`)">
                                <i class="bi bi-trash-fill pe-0"></i>
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
      dt.on('draw', function (e, data, type, indexes) {
          // debugger;
          console.log(dt.rows().data().toArray());
          console.log(dt.rows().data());
          initToggleToolbar();
          toggleToolbars();
          KTMenu.createInstances();
      });

      dt.on('select', function(e, data, type, indexes) {
        const selectedData = data.rows().data()[indexes];
        selectedTransactionId = selectedData.t_id;
        onSelectTransaction(selectedTransactionId);
      });
  }


  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = function () {
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-analytics"]');
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

var DatatableTest = function () {
  // Shared variables
  var table;
  var dt;
  var selectorName = '.transaction-test-table';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          paging: false,
          info: false,
          scrollY: '400px',
          scrollX: '100%',
          // order: [[0, 'desc']],
          order: false,
          responsive: true,
          // searchDelay: 500,
          processing: false,
          serverSide: false,
          stateSave: false
          // ajax: {
          //     url: baseUrl('analytics/datatable-test/0'),
          //     type: 'get',
          //     complete: function(data) {
          //       const testData = data.responseJSON.data;
          //       testData.forEach(function(item){
          //         $.ajax({
          //           url: baseUrl('analytics/result-label/'+item.test_id),
          //           type: 'get',
          //           success: function(res) {
          //             if (res.message) {
          //               $("#result-html-"+item.tt_id).html(res.message);
          //             } else {
          //               $("#select-result-label-"+item.tt_id).html(res);
          //               $("#select-result-label-"+item.tt_id).select2({allowClear:true});
          //               $("#select-result-label-"+item.tt_id).val(item.result_label).trigger('change');
          //               // $("#result-test-"+item.tt_id).attr('onChange',"analyzerChange("+item.id+","+item.transaction_id+",event)");

          //             }
          //           }
          //         })
          //       });
          //       console.log(data.responseJSON.data);
          //     }
          // },
          // columns: transactionTestColumnTable
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
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-analytics"]');
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
          // handleSearchDatatable();
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
      },
      destroy: function () {
          dt.destroy();
      }
  }
}();