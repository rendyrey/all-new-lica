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
  let month = d.getMonth() + 1;
  let year = d.getFullYear();
  let theDate = day + '/' + ('0'+month).slice(-2)  + '/' + year;

  return theDate;
}

var newFormId = '#new-pre-analytics';

var buttonActionIndex = 7;
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
  { data: null, render: function(data, type, row) {
    let cito = '';
    if (row.cito != null && row.cito != 0) {
      cito = '<i class="bi bi-exclamation-triangle-fill text-warning" data-toggle="tooltip" data-placement="top" title="CITO"></i>';
    }
    let analytic = '';
    if (row.status == 1) {
      analytic = '<i class="ms-2 bi bi-check-square-fill text-success" data-toggle="tooltip" data-placement="top" title="Moved to analytic"></i>'
    }
    return "<div>"+cito+analytic+"</div>";
  }, defaultContent: '', responsivePriority: 1},
];

// Datatable Component
var selectedTransactionId;
var PostAnalyticDatatable = function () {
  // Shared variables
  var table;
  var dt;
  var filterPayment;
  var selectorName = '.post-analytics-datatable-ajax';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
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
              url: baseUrl('post-analytics/datatable/')
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