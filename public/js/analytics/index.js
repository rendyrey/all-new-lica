var baseUrl = function(url) {
  return base + url;
}

var getAge = function(dateString) {
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
      },
      alwaysShowCalendars: true
  }, cb);

  cb(start, end);

  $("#daterange-picker").on('change', function () {
    const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
    const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
    const url = baseUrl('analytics/datatable/'+startDate+'/'+endDate);
    DatatableAnalytics.refreshTableAjax(url);
  });
}

var refreshPatientDetails = (data, transactionId) => {
  $(".name-detail").html(data.patient.name);
  $(".gender-detail").html(data.patient.gender);
  $(".email-detail").html(data.patient.email);
  $(".age-detail").html(getAge(data.patient.birthdate));
  $(".insurance-detail").html(data.insurance.name);
  
  // $('#verify-all-btn').data('transaction-id', transactionId);
  // $('#unverify-all-btn').data('transaction-id', transactionId);
  // $('#validate-all-btn').data('transaction-id', transactionId);
  // $('#unvalidate-all-btn').data('transaction-id', transactionId);
  $(".test-data-action").removeClass('d-none');
  $(".test-data-action").data('transaction-id', transactionId);
  $("#memo-result-btn").data('text', data.memo_result);
  $("#go-to-post-analytics-btn").data('transaction-id', transactionId);

  $.ajax({
    url: baseUrl('analytics/check-action-btn-test-status/'+transactionId),
    type: 'get',
    success: function(res) {
      if (res.unver_and_val_all) {
        $('#unverify-all-btn').removeAttr('disabled');
        $('#validate-all-btn').removeAttr('disabled');
      } else {
        $('#unverify-all-btn').attr('disabled', 'disabled');
        $('#validate-all-btn').attr('disabled', 'disabled');
      }

      if (res.unval_all) {
        $('#unvalidate-all-btn').removeAttr('disabled');
      } else {
        $('#unvalidate-all-btn').attr('disabled', 'disabled');
      }
    }
  });

  let patientType = '-';
  switch (data.type) {
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
  $(".doctor-detail").html(data.doctor.name);
  $(".note-detail").html(data.note);

  refreshPatientDatatables(transactionId);
}

var refreshPatientDatatables = (transactionId) => {
  $.ajax({
    url: baseUrl('analytics/datatable-test/'+transactionId),
    type: 'get',
    success: function(data) {
        $("#transaction-test-table-body").html(data.html);
        data.data.forEach(function(item) {
          // $(".select-result-label").select2({allowClear: true});
          $("#select-result-label-"+item.id).select2({allowClear: true});
          $("#select-result-label-"+item.id).val(item.result_label).trigger('change');
          $("#select-result-label-"+item.id).attr('onChange',"resultLabelChange("+item.id+",event)");
        });
    }
  });
  // alert(transactionId);
}

function resultLabelChange(transactionTestId, e) {
  const component = e.target;
  const value = e.target.value;
  // alert(value)
  // const transactionTestId = component.data('transaction-test-id');
  $.ajax({
      url: baseUrl('analytics/update-result-label/'+transactionTestId),
      type: 'put',
      data: { result: value },
      success: function(res) {
          toastr.success("Success update result label");
          switch (res.label) {
            case 1: // normal
                label = '<span class="badge badge-sm badge-circle badge-success">N</span>';
                break;
            case 2: // low
                label = '<span class="badge badge-sm badge-circle badge-warning">L</span>';
                break;
            case 3: // high
                label = '<span class="badge badge-sm badge-circle badge-warning">H</span>';
                break;
            case 4: // abnormal
                label = '<span class="badge badge-sm badge-circle badge-warning">A</span>';
                break;
            case 5: // critical
                label = '<span class="badge badge-sm badge-circle badge-danger">C</span>';
                break;
            default:
                label = '';
          }
        $("#verify-checkbox-id-"+transactionTestId+"").data('result-status', res.label);
        $("#label-info-"+transactionTestId).html(label);
      },
      error: function(request, status, error) {
          toastr.error(request.responseJSON.message);
          component.focus();
      }
  })
}

var onSelectTransaction = (transactionId) => {
  $.ajax({
    url: baseUrl('analytics/transaction/'+transactionId),
    type: 'get',
    success: function(res) {
      refreshPatientDetails(res.data, transactionId);
    }
  });

  // DatatableTest.init(transactionId);
  // const newUrl = baseUrl('analytics/datatable-test/'+transactionId);
  // DatatableTest.refreshTableAjax(newUrl);
}

var verifyAllBtn = () => {
  $("#cancel-modal-btn").on('click', function() {
    $("#critical-modal").modal('hide');
  });

  $("#report-modal-btn").on('click', function(e) {
    const reportTo = $("#critical-modal input[name='report_to']").val();
    if (reportTo == '') {
      alert("You need to insert report to field");
      return;
    }
  
    const reportBy = $("#critical-modal input[name='report_by']").val();
    const criticalTestIds = $("#critical-modal input[name='transaction_test_ids']").val();
    const transactionId = $("#critical-modal input[name='transaction_id']").val();
    
    $.ajax({
      url: baseUrl('analytics/report-critical-tests'),
      type: 'put',
      data: {
        report_to: reportTo,
        report_by: reportBy,
        transaction_test_ids: criticalTestIds,
        transaction_id: transactionId
      },
      success: function(res) {
        toastr.success("Success reporting critical tests");
        $("#critical-modal").modal('hide');
        $("#critical-modal input[name='report_to']").val('');
      }
    });

    $.ajax({
      url: baseUrl('analytics/verify-all/'+transactionId),
      type: 'put',
      success: function(res) {
        onSelectTransaction(transactionId);
      }
    });

    e.preventDefault();
  });

  $("#verify-all-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/check-critical-test/'+transactionId),
      type: 'get',
      success: function(res) {
        // console.log(res.exists);
        if (res.exists) {
          let criticalTests = '';
          let criticalTestIds = [];
          res.data.forEach((item) => {
            criticalTests += '<li>'+item.test.name+'  <i>value: </i>'+(item.result_number || item.res_label)+'</li>';
            criticalTestIds.push(item.id);
          });

          $("#critical-tests").html(criticalTests);
          $("#critical-modal input[name='transaction_test_ids']").val(criticalTestIds.join(','));
          $("#critical-modal input[name='transaction_id']").val(transactionId);
          $("#critical-modal").modal('show');
        } else {
           $.ajax({
            url: baseUrl('analytics/verify-all/'+transactionId),
            type: 'put',
            success: function(res) {
              toastr.success("Success verify all test");
              onSelectTransaction(transactionId);
            }
          });
        }
      }
    });

    // $.ajax({
    //   url: baseUrl('analytics/verify-all/'+transactionId),
    //   type: 'put',
    //   success: function(res) {
    //     onSelectTransaction(transactionId);
    //   }
    // })
  });
}

var validateAllBtn = () => {
  $("#validate-all-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/validate-all/'+transactionId),
      type: 'put',
      success: function(res) {
        toastr.success("Success validate all test");
        onSelectTransaction(transactionId);
      }
    })
  });
}

var unverifyAllBtn = () => {
  $("#unverify-all-btn").on('click', function() {
    const transationId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/unverify-all/'+transationId),
      type: 'put',
      success: function(res) {
        toastr.success("Success unverify all test");
        onSelectTransaction(transationId);
      }
    });
  });
}

var unvalidateAllBtn = () => {
  $("#unvalidate-all-btn").on('click', function() {
    const transationId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/unvalidate-all/'+transationId),
      type: 'put',
      success: function(res) {
        toastr.success("Success unvalidate all test");
        onSelectTransaction(transationId);
      }
    })
  });
}

var memoTestModal = (transactionTestId, transactionId, text) => {
  Swal.fire({
    title: 'Test Memo',
    text: 'Please input a memo',
    input: 'text',
    customClass: 'w-600px',
    inputAttributes: {
      autocapitalize: 'off'
    },
    inputValue: text,
    showCancelButton: true,
    confirmButtonText: 'Submit',
    showLoaderOnConfirm: true,
    preConfirm: (reason) => {
      if (reason == '') {
        Swal.showValidationMessage(`Please enter a memo`)
      }
      return { reason: reason }
    },
    allowOutsideClick: false
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: baseUrl('analytics/update-test-memo'),
        type: 'put',
        data: {
          transaction_test_id: transactionTestId,
          memo: result.value.reason
        },
        success: function(res) {
          toastr.success("Update test memo success!");
          onSelectTransaction(transactionId);
        }
      });
    } else {
      // event.target.checked = true;
    }
  });
}

var parameterDataModal = (transactionId, text) => {
  Swal.fire({
    title: 'Add patient memo result',
    text: 'Please input a memo',
    input: 'textarea',
    customClass: 'w-600px',
    inputAttributes: {
      autocapitalize: 'off'
    },
    inputValue: text,
    showCancelButton: true,
    confirmButtonText: 'Submit',
    showLoaderOnConfirm: true,
    preConfirm: (reason) => {
      if (reason == '') {
        Swal.showValidationMessage(`Please enter a memo`)
      }
      return { reason: reason }
    },
    allowOutsideClick: false
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: baseUrl('analytics/update-memo-result'),
        type: 'put',
        data: {
          transaction_id: transactionId,
          memo_result: result.value.reason
        },
        success: function(res) {
          toastr.success("Update memo result success!");
          onSelectTransaction(transactionId);
        }
      });
    } else {
      // event.target.checked = true;
    }
  });
}

var memoResultBtn = () => {
  $("#memo-result-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    const text = $(this).data('text');
    parameterDataModal(transactionId, text);
  });
}

var goToPostAnalyticBtn = () => {
  $("#go-to-post-analytics-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/go-to-post-analytics/'+transactionId),
      type: 'put',
      success: function(res) {
        alert();
      },
      error: function(request, status, error) {
        alert(request.responseJSON.message);       
      }
    })
  });
}

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

// On document ready
document.addEventListener('DOMContentLoaded', function () {
  DateRangePicker();
  verifyAllBtn();
  unverifyAllBtn();
  validateAllBtn();
  unvalidateAllBtn();
  memoResultBtn();
  goToPostAnalyticBtn();
  DatatableAnalytics.init();

  $(".transaction-test-table").DataTable({
    "scrollY": "500px",
    "scrollCollapse": true,
    "paging": false,
    // "dom": "<'table-responsive'tr>",
    "sort": false,
    autoWidth: false,
    "columnDefs": [
      { "width": "220px", "targets": 0 },
      { "width": "42px", "targets": -1},
      { "width": "42px", "targets": -2}
    ]
  });
  // $(".select").select2();
  // DatatableTest.init();
});