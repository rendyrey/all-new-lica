"use strict";

// Class definition
var DatatablesServerSide = function () {
    // Shared variables
    var dt;

    // Private functions
    var initDatatable = function () {
        dt = $("#master-patient-table").DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [[1, 'desc']],
            ajax: {
                url: '/master/datatable/patient'
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', orderable: true },
                { data: 'medrec' },
                { data: 'gender' },
                { data: 'birthdate' },
                { data: 'phone' }
            ]
        });
    }

    var refreshTable = function () {
        dt.ajax.reload();
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }

    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
        },
        refreshTable: function () {
            refreshTable()
        }
    }
}();


var addNewButton = function () {
    $('#add-new').on('click',function(){
        $("#master-new").removeClass('d-none');
    });
}

var birthDatePicker = function () {
    $("#birthdate").flatpickr();
}


// On document ready
KTUtil.onDOMContentLoaded(function () {
    DatatablesServerSide.init();
    addNewButton();
    birthDatePicker();
});
