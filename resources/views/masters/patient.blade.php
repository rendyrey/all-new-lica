@extends('dashboard.main-layout')

@section('style')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('metronic_assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css"/>

@endsection
@section('content')
<div class="row">
<div class="col-md-8" id="master-list">
    <div class="card p-0 my-2">
        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
        </div>
        <div class="card-body p-4">
            <!--begin::Toolbar-->
           <div class="d-flex justify-content-between" data-kt-docs-table-toolbar="base">
               <input id="search" type="text" name="search" autocomplete="off" data-kt-docs-table-filter="search" class="form-control form-control-solid w-250px ps-15 mr-3" placeholder="Search Patient"/>
   
               <!--begin::Add customer-->
               <button type="button" class="btn btn-sm btn-primary" id="add-new">
                   Add Patient
               </button>
               <!--end::Add customer-->
           </div>
           <!--end::Toolbar-->
           <table id="{{$tableId}}" class="table table-striped table-row-bordered gy-5 gs-7">
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
</div>
<div class="col-md-4" id="master-new">
    <div class="card p-0 my-2">
        <div class="card-header">
            <h3 class="card-title">Add New {{ ucwords($masterData) }}</h3>
        </div>
        
        <div class="card-body p-4">
            <form action="" ref="theForm" id="form">
            <!--begin::Input group-->
            <div class="form-floating mb-7">
                <input type="text" name="name" v-model="name" class="form-control" id="name" placeholder="Patient Name"/>
                <label for="name" class="required">Patient Name</label>
            </div>
            <!--end::Input group-->

            <!--begin::Input group-->
            <div class="form-floating mb-7">
                <input type="email" name="email" v-model="email" class="form-control" id="email" placeholder="Password"/>
                <label for="email" class="required">Email</label>
            </div>
            <!--end::Input group-->

             <!--begin::Input group-->
             <div class="form-floating mb-5">
                <input type="text" name="phone" v-model="phone" class="form-control" id="phone" placeholder="Password"/>
                <label for="phone" class="required">Phone Number</label>
            </div>
            <!--end::Input group-->

             <!--begin::Input group-->
             <div class="form-floating mb-7">
                <input type="text" name="medrec" v-model="medrec" class="form-control" id="medrec" placeholder="Password"/>
                <label for="medrec" class="required">Medical Record</label>
            </div>
            <!--end::Input group-->

            <div class="form-floating mb-7">
                <input class="form-control" name="birthdate" v-model="birthdate" placeholder="Pick a date" id="birthdate"/>
                <label for="floatingPassword" class="required">Birth date</label>
            </div>

            <!--begin::Input group-->
            <div class="mb-2 px-2">
                <!--begin::Label-->
                <label class="required fw-bold fs-6 mb-5">Gender</label>
                <!--end::Label-->

                <!--begin::Input row-->
                <div class="d-flex flex-column fv-row">
                    <!--begin::Radio-->
                    <div class="form-check form-check-custom form-check-solid mb-5" v-for="gender in ['M','F']">
                        <!--begin::Input-->
                        <input class="form-check-input me-3" name="gender" v-model="the_gender" type="radio" v-bind:value="gender" v-bind:id="gender == 'M' ? 'male':'female'" />
                        <!--end::Input-->

                        <!--begin::Label-->
                        <label class="form-check-label" v-bind:for="gender == 'M' ? 'male':'female'">
                            <div class="fw-bolder text-gray-800">@{{gender == 'M' ? 'Male' : 'Female'}}</div>
                        </label>
                        <!--end::Label-->
                    </div>
                    <!--end::Radio-->
                </div>
                <!--end::Input row-->
            </div>
            <!--end::Input group-->

            <!--begin::basic autosize textarea-->
            <div class="d-flex flex-column p-2 mb-5">
                <label for="address" class="form-label required">Address</label>
                <textarea name="address" class="form-control" data-kt-autosize="true" id="address" v-model="address"></textarea>
            </div>
            <!--end::basic autosize textarea-->
            <div class="separator mb-5"></div>
            
            <button type="button" class="btn btn-success form-control" id="submit-btn" v-html="submit_btn_text">
            </button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@section('script')
    <script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    {{-- <script src="{{asset('js/master/'.$tableId.'.js')}}"></script> --}}
    <script src="{{asset('js/vue.js')}}"></script>
    <script src="{{asset('js/master/master-'.$masterData.'-page.js')}}"></script>
@endsection