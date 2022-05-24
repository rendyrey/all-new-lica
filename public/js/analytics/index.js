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

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

// On document ready
document.addEventListener('DOMContentLoaded', function () {
  DateRangePicker();
  DatatableAnalytics.init();
  // $(".select").select2();
  // DatatableTest.init();
});