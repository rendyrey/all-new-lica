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
            
            <table class="table datatable-ajax datatable-responsive">
                <thead>
                    <th>No</th>
                    <th>Package Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Class</th>
                    <th>Action</th>
                    <th></th>
                </thead>
            </table>


        </div>
    </div>
    <div class="col-lg-4" id="master-snew">
        <div class="card">
             <!-- card header -->
             <div class="card-header header-elements-sm-inline">
                <h5 class="card-title">Add new {{ ucwords($masterData) }}</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <!-- /card header -->
            <!-- card body -->
            <div class="card-body">
                {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-create']) !!}
                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Package <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::select('package_id', [], null, ['class' => 'form-control select-package select2', 'id' => 'first-input', 'data-placeholder' => 'Select Package']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Type <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::select('type', array_replace(Helper::priceType(),[''=>'']), null, ['class' => 'form-control form-select2 select2', 'data-placeholder' => 'Select Type']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Price <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        <div class="input-group">
                            <span class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </span>
                            {{ Form::text('price', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                   
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Class <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::text('class', null, ['class' => 'form-control']) }}
                    </div>
                </div>

                <div class="row">
                    {{ Form::button('Add '. $masterData, ['class' => 'form-control btn-success', 'id' => 'submit-btn','type' => 'submit']) }}
                </div>
                
                {!! Form::close() !!}
            </div>
            <!-- /card body -->
        </div>
    </div>
</diV>

<!-- Horizontal form modal -->
<div id="modal_form_horizontal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Horizontal form</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-edit', 'method' => 'put']) !!}
            {{ Form::hidden('id') }}
            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Package <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    {{ Form::select('package_id', [], null,['class' => 'form-control select-package select2', 'data-placeholder' => 'Select Package']) }}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Type <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    {{ Form::select('type', array_replace(Helper::priceType(),[''=>'']), null, ['class' => 'form-control form-select2 select2', 'data-placeholder' => 'Select Type']) }}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Price <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </span>
                        {{ Form::text('price', null, ['class' => 'form-control']) }}
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Class <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    {{ Form::text('class', null, ['class' => 'form-control']) }}
                </div>
            </div>

            <div class="row">
                {{ Form::button('Update ' . $masterData, ['class' => 'form-control btn-success', 'id' => 'submit-btn','type' => 'submit']) }}
            </div>
            
            {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->
@endsection

@section('additional-script')
<script src="{{asset('limitless_assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
<script src="{{asset('limitless_assets/js/plugins/tables/datatables/extensions/responsive.min.js')}}"></script>
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

<!-- sweetAlert -->
<script src="{{asset('limitless_assets/js/plugins/notifications/sweet_alert.min.js')}}"></script>
<!-- /sweetAlert -->

<!-- select2 -->
<script src="{{asset('limitless_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
<!-- /select2 -->

<script src="{{asset('js/master/master-'.$masterData.'-page.js')}}"></script>
<script src="{{asset('js/master/global.js')}}"></script>
@endsection