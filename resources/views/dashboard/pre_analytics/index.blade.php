@extends('dashboard.main_layout')

@section('styles')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!--begin::Content-->
<div class="docs-content d-flex flex-column flex-column-fluid" id="kt_docs_content">
    <!--begin::Container-->
    <div class="px-2" id="kt_docs_content_container">
        <div class="row">
            <div class="col-lg-6">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-0">
                            <!--begin::Heading-->
                            <div class="d-flex justify-content-between">
                              <h1 class="anchor fw-bolder mb-5">
                                Pre Analytics
                              </h1>
                              <div>
                                <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-patient-modal">
                                  Add Patient
                                </button>
                              </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
                                <div class='row mb-5'>
                                  {{-- <div class="col-lg-6">
                                    <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker"/>
                                  </div> --}}
                                </div>
                                <div class="d-flex justify-content-between mb-5">
                                    <div class="col-lg-6">
                                        <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker"/>
                                      </div>
                                    <!--begin::Search-->
                                    <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <input type="text" data-kt-docs-table-filter="search-pre-analytics" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search Pre-Analytics" />
                                    </div>
                                    <!--end::Search-->
                                    <!--begin::Group actions-->
                                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
                                        <div class="fw-bolder me-5">
                                        <span class="me-2" data-kt-docs-table-select="selected_count"></span>Selected</div>
                                        <button type="button" class="btn btn-danger" data-kt-docs-table-select="delete_selected">Selection Action</button>
                                    </div>
                                    <!--end::Group actions-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Datatable-->
                                <table class="table gy-1 align-middle table-striped px-0 pre-analytics-datatable-ajax">
                                    <thead>
                                        <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="min-w-100px">Date</th>
                                            <th>Transaction ID</th>
                                            <th class="w-auto">Lab No</th>
                                            <th>Medrec</th>
                                            <th class="min-w-150px">Name</th>
                                            <th class="w-auto">Room</th>
                                            <th class="text-end min-w-100px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold"></tbody>
                                </table>
                                <!--end::Datatable-->
                            </div>
                            <!--end::CRUD-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Card Body-->
                </div>
                <!--end::Card-->
            </div>

            <div class="col-lg-6">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 py-lg-6 px-lg-6 text-gray-700">
                        <h1>Patient Details</h1>
                        <div class="separator mb-2"></div>
                        <!--begin::Section-->
                        <div class="row">
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                      <tr>
                                       <th class="py-1">Name</th>
                                       <td class="name-detail py-1 text-gray-600">-</th>
                                      </tr>
                                      <tr>
                                        <th class="py-1">Gender</th>
                                        <td class="gender-detail py-1 text-gray-600">-</th>
                                       </tr>
                                       <tr>
                                        <th class="py-1">Email</th>
                                        <td class="email-detail py-1 text-gray-600">-</th>
                                       </tr>
                                       <tr>
                                        <th class="py-1">Phone</th>
                                        <td class="phone-detail py-1 text-gray-600">-</th>
                                       </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                      <tr>
                                       <th class="py-1">Age</th>
                                       <td class="age-detail py-1 text-gray-600">-</th>
                                      </tr>
                                      <tr>
                                        <th class="py-1">Insurance</th>
                                        <td class="insurance-detail py-1 text-gray-600">-</th>
                                       </tr>
                                       <tr>
                                        <th class="py-1">Patient type</th>
                                        <td class="type-detail py-1 text-gray-600">-</th>
                                       </tr>
                                       <tr>
                                        <th class="py-1">Room</th>
                                        <td class="room-detail py-1 text-gray-600">-</th>
                                       </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                      <tr>
                                        <th class="py-1">Medical Record</th>
                                        <td class="medrec-detail py-1 text-gray-600">-</th>
                                      </tr>
                                      <tr>
                                        <th class="py-1">Physician</th>
                                        <td class="doctor-detail py-1 text-gray-600">-</th>
                                       </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="separator mb-2"></div>
                        <div class="row">
                            <div class="col-6 d-flex justify-content-between">
                                <span class="text-dark">Patient test</span>
                                <button class="btn btn-light-primary btn-sm">Edit test</button>
                            </div>
                            <div class="col-6 d-flex justify-content-between">
                                <span class="text-dark">Specimen</span>
                                <div>
                                    <button class="btn btn-light-primary btn-sm">No. Lab</button>
                                    <button class="btn btn-light-info btn-sm" id="draw-all-btn" value="" disabled>Draw all</button>
                                    <button class="btn btn-light-info btn-sm" id="undraw-all-btn" value="" disabled>Undraw all</button>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-6">
                                <table class="table table-striped transaction-test-table">
                                    <thead>
                                        <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                            <td class="px-0">Test name</td>
                                            <td class="px-0">Analyzer</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="table table-striped transaction-specimen-table">
                                    <thead>
                                        <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                            <td class="px-0">Specimen</td>
                                            <td class="px-0">Vol.</td>
                                            <td class="px-0">Draw</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
        
    </div>
    <!--end::Container-->


</div>
<!--end::Content-->

@include('dashboard.pre_analytics.add-patient-modal')
@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/pre_analytics/index.js')}}"></script>
@endsection