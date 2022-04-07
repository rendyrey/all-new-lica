@extends('dashboard.main_layout')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <!-- card header -->
            <div class="card-header header-elements-sm-inline">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <!-- /card header -->

            <!-- card body -->
            {{-- <div class="card-body">
            </div> --}}
            <!-- /card body -->
            
            <table class="table datatable-ajax">
                <thead>
                    <th>No</th>
                    <th>Name</th>
                    <th>Medrec</th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                    <th>Phone</th>
                </thead>
            </table>


        </div>
    </div>
    <div class="col-lg-4" id="master-snew">
        <div class="card">
             <!-- card header -->
             <div class="card-header header-elements-sm-inline">
                <h5 class="card-title">Add new patient</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <!-- /card header -->
            <!-- card body -->
            <div class="card-body">
                {!! Form::open(['class'=>'form form-horizontal form-validate-jquery']) !!}
                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Patient Name <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Email <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::email('email', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Phone Number <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::text('phone', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                
                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Medical Record <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::text('medrec', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Birthdate <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::text('birthdate', null, ['class' => 'form-control pickadate-selectors']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Gender <span class="text-danger">*</span>
                    </label>
                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            {{ Form::radio('gender','M', null, ['class' => 'form-check-input-styled', 'data-fouc']) }}
                            Male
                        </label>
                    </div>

                    <div class="form-check form-check-inline">
                        <label class="form-check-label">
                            {{ Form::radio('gender','F', null, ['class' => 'form-check-input-styled', 'data-fouc']) }}
                            Female
                        </label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">Adress</label>
                    <div class="col-lg-9">
                        {{ Form::textarea('address', null, ['class' => 'form-control', 'cols' => 3, 'rows' => 3]) }}
                    </div>
                </div>

                <div class="row">
                    {{ Form::button('Add patient', ['class' => 'form-control btn-success', 'id' => 'submit-btn','type' => 'submit']) }}
                </div>


                
                {!! Form::close() !!}
            </div>
            <!-- /card body -->
        </div>
    </div>
</diV>
@endsection

@section('additional-script')
<script src="{{asset('limitless_assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
<script src="{{asset('limitless_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
{{-- <script src="{{asset('js/vue.js')}}"></script> --}}

<!-- picker date -->
<script src="{{asset('limitless_assets/js/plugins/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('limitless_assets/js/plugins/pickers/pickadate/picker.date.js')}}"></script>
<!-- /picker date -->

<!-- uniform (for radios button) -->
<script src="{{asset('limitless_assets/js/plugins/forms/styling/uniform.min.js')}}"></script>
<!-- /uniform -->

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<!-- JGROWL (like toast) -->
<script src="{{asset('limitless_assets/js/plugins/notifications/jgrowl.min.js')}}"></script>
<!-- /JGROWL -->
<script src="{{asset('js/master/master-'.$masterData.'-page.js')}}"></script>
@endsection