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

var DatatableDataSources = function() {
    var dt;
    // Basic Datatable examples
    var _componentDatatableDataSources = function() {
        if (!$().DataTable) {
            console.warn('Warning - datatables.min.js is not loaded.');
            return;
        }

        // Setting datatable defaults
        $.extend( $.fn.dataTable.defaults, {
            autoWidth: false,
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
            }
        });

        // AJAX sourced data
        dt = $('.datatable-ajax').DataTable({
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
    };

    // Select2 for length menu styling
    var _componentSelect2 = function() {
        if (!$().select2) {
            console.warn('Warning - select2.min.js is not loaded.');
            return;
        }

        // Initialize
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            dropdownAutoWidth: true,
            width: 'auto'
        });
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentDatatableDataSources();
            _componentSelect2();
        },
        refreshTable: function() {
            dt.ajax.reload();
        }
    }
}();

var DateTimePickers = function() {
    // Pickadate picker
    var _componentPickadate = function() {
        if (!$().pickadate) {
            console.warn('Warning - picker.js and/or picker.date.js is not loaded.');
            return;
        }
        // Dropdown selectors
        $('.pickadate-selectors').pickadate({
            selectYears: true,
            selectMonths: true,
            max: true,
            selectYears: 100,
            format: 'd mmmm yyyy',
            formatSubmit: 'yyyy-mm-dd'
        });
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentPickadate();
        }
    }
}();

var InputsCheckboxesRadios = function () {
    // Uniform
    var _componentUniform = function() {
        if (!$().uniform) {
            console.warn('Warning - uniform.min.js is not loaded.');
            return;
        }
        // Default initialization
        $('.form-check-input-styled').uniform();
    };

    //
    // Return objects assigned to module
    //

    return {
        initComponents: function() {
            _componentUniform();
        }
    }
}();

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
        var validator = $('.form-validate-jquery').validate({
            ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
            errorClass: 'validation-invalid-label',
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
            rules: {
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                phone: {
                    digits: true,
                    minlength: 6,
                    required: true
                },
                medrec: {
                    required: true,
                },
                birthdate: {
                    required: true
                },
                gender: {
                    required: true
                },
                address: {
                    required: true
                }

            },
            messages: {
                custom: {
                    required: 'This is a custom error message'
                }
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                $("#submit-btn").prop('disabled', true); // disabled button

                let formData = $(".form-validate-jquery").serialize()
                let theForm = $(".form-validate-jquery");
                $.ajax({
                    url: '/master/patient/create',
                    method: 'POST',
                    data: formData,
                    success: function(res) {
                        if (res.status) {
                            $.jGrowl(res.message, {
                                header: 'Well done!',
                                theme: 'bg-success'
                            });
                            DatatableDataSources.refreshTable();
                        } else {
                            $.jGrowl(res.message, {
                                header: 'Well done!',
                                theme: 'bg-danger'
                            });
                        }

                        $("#submit-btn").prop('disabled', false); // re-enable button
                        theForm.trigger('reset');
                    },
                    error: function (request, status, error) {
                        $.jGrowl('Something error happen on our side. Data unsuccessfully saved', {
                            header: 'Oh snap!',
                            theme: 'bg-danger'
                        });

                        $("#submit-btn").prop('disabled', false); // re-enable button
                    }
                })
                
            }
        });

        // Reset form
        $('#reset').on('click', function() {
            validator.resetForm();
        });
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

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatableDataSources.init();
    DateTimePickers.init();
    InputsCheckboxesRadios.initComponents();
    FormValidation.init();
});





