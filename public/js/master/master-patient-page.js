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

var initVue = function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var vue = Vue.createApp({
        data() {
            return {
                name: '',
                email: '',
                phone: '',
                medrec: '',
                birthdate: '',
                gender: '',
                the_gender: '',
                address: '',
                disable_submit_btn: false,
                submit_btn_text: 'Add',
                submit_btn_loading: '<i class="p-0 fa fa-circle-notch fa-spin"></i>'
            }
        },
        methods: {
            submit: function () {
                this.disable_submit_btn = true;
                this.submit_btn_text = this.submit_btn_loading;
                $.ajax({
                    url: '/master/patient/create',
                    method: 'POST',
                    context: this,
                    data: {
                        name: this.name,
                        email: this.email,
                        phone: this.phone,
                        medrec: this.medrec,
                        birthdate: this.birthdate,
                        gender: this.the_gender,
                        address: this.address
                    },
                    success: function(res) {
                        this.formReset();
                        DatatablesServerSide.refreshTable();
                        document.getElementById('submit-btn').disabled = false;
                        this.submit_btn_text = 'Add';
                    }
                })
            },
            formReset: function () {
                this.name = '';
                this.email = '';
                this.phone = '';
                this.medrec = '';
                this.birthdate = '';
                this.gender = '';
                this.address = '';
            }
        }
    }).mount('#master-new');

    const form = document.getElementById('form');
    var validator = FormValidation.formValidation(
        form,
        {
            fields: {
                'name': {
                    validators: {
                        notEmpty: {
                            message: 'Name is required'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.form-floating',
                    eleInvalidClass: '',
                    eleValidClass: ''
                })
            }
        }
    )

    const submitButton = document.getElementById('submit-btn');
    submitButton.addEventListener('click', function (e) {
        e.preventDefault();
        // validate form before submit
        if (validator) {
            validator.validate().then(function (status){
                if (status == 'Valid') {
                    submitButton.disabled = true;
                    vue.submit()
                }
            });
        }
    });
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
    DatatablesServerSide.init();
    initVue();

    addNewButton();
    birthDatePicker();
});





