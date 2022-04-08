
var baseUrl = '/all-new-lica/public';
var jGrowlSuccess = function (message) {
    $.jGrowl(message, {
        header: 'Well done!',
        theme: 'bg-success'
    });
}

var jGrowlError = function () {
    $.jGrowl('Something error happen on our side. Data unsuccessfully saved', {
        header: 'Oh snap!',
        theme: 'bg-danger'
    });
}

var createData = function () {
    $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-create").serialize();
    let theForm = $("#form-create");
    $.ajax({
        url: baseUrl+'/master/'+masterData+'/create',
        method: 'POST',
        data: formData,
        success: function(res) {
            jGrowlSuccess(res.message);
            
            DatatableDataSources.refreshTable();

            $("#submit-btn").prop('disabled', false); // re-enable submit button
            theForm.trigger('reset'); // reset form after successfully create data
            $(".uniform-choice span").removeClass('checked'); // uncheck all the radio buttons
        },
        error: function (request, status, error) {
            jGrowlError();

            $("#submit-btn").prop('disabled', false); // re-enable button
        }
    })
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

var editData = function (id) {
    $.ajax({
        url: baseUrl+'/master/'+masterData+'/edit/'+id,
        method: 'GET',
        success: function(res){
            $("#modal_form_horizontal").modal('show');
            $("#modal_form_horizontal input[name='id']").val(res.id);
            $("#modal_form_horizontal input[name='name']").val(res.name);
            $("#modal_form_horizontal input[name='email']").val(res.email);
            $("#modal_form_horizontal input[name='phone']").val(res.phone);
            $("#modal_form_horizontal input[name='medrec']").val(res.medrec);
            $("#modal_form_horizontal input[name='birthdate']").val(theFullDate(res.birthdate));
            $("#modal_form_horizontal input[name='birthdate_submit']").val(res.birthdate);
            $("#modal_form_horizontal input[name='gender'][value='"+res.gender+"']").trigger('click');
            $("#modal_form_horizontal textarea[name='address']").val(res.address);
        },
        error: function(res) {

        }
    })
}

var updateData = function () {
    let theForm = $("#form-edit");
    let formData = $("#form-edit").serialize();
    console.log(formData);
    $.ajax({
        url: baseUrl+'/master/'+masterData+'/update/',
        data: formData,
        method: 'PUT',
        success: function(res) {
            $("#modal_form_horizontal").modal('hide');
            theForm.trigger('reset');
            $(".uniform-choice span").removeClass('checked'); // uncheck all the radio buttons

            DatatableDataSources.refreshTable();
            jGrowlSuccess(res.message);
        },
        error: function(res) {
            jGrowlError();
        }
    });
}
    // Defaults
var swalInit = swal.mixin({
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-primary',
    cancelButtonClass: 'btn btn-light'
});

var deleteData = function (id) {
    swalInit({
        title: 'Are you sure?',
        text: 'You will not be able to recover this imaginary file!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then(function(isConfirm){
        
        if(isConfirm.value) {
            $.ajax({
                url: baseUrl+'/master/'+masterData+'/delete/'+id,
                method: 'DELETE',
                success: function(res) {
                    DatatableDataSources.refreshTable();
                    jGrowlSuccess(res.message);
                }
            })
        }
    });
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});