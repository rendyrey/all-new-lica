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
            <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-0">
                            <!--begin::Heading-->
                            <div class="d-flex justify-content-between">
                              <h1 class="anchor fw-bolder mb-5">
                                Analytics
                              </h1>
                            </div>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
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
                                        <input type="text" data-kt-docs-table-filter="search-analytics" class="form-control form-control-sm form-control-solid ps-15" placeholder="Search Patient" />
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
                                <table class="table gy-1 align-middle table-striped px-0 analytics-datatable-ajax">
                                    <thead>
                                        <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start">Lab No</th>
                                            <th>Medrec</th>
                                            <th class="text-start">Room</th>
                                            <th class="text-start">Name</th>
                                            <th class="text-end min-w-50px">Action</th>
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

            <div class="col-lg-8">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 py-lg-6 px-lg-6 text-gray-700">
                        <div class="d-flex justify-content-between mb-4">
                            <h1>Patient Details</h1>
                            <button class="btn btn-light-primary btn-sm patient-details-btn d-none" id="edit-patient-details-btn" data-transaction-id="">Edit patient details</button>
                        </div>
                        <div class="separator mb-2"></div>
                        <!--begin::Section-->
                        <div class="row">
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                      <tr>
                                       <th class="py-1 w-10px">Name</th>
                                       <td class="name-detail py-1 text-gray-600 w-auto">-</th>
                                      </tr>
                                      <tr>
                                        <th class="py-1">Gender</th>
                                        <td class="gender-detail py-1 text-gray-600">-</th>
                                       </tr>
                                       <tr>
                                        <th class="py-1">Email</th>
                                        <td class="email-detail py-1 text-gray-600">-</th>
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
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                      <tr>
                                        <th class="py-1">Physician</th>
                                        <td class="doctor-detail py-1 text-gray-600">-</th>
                                      </tr>
                                      <tr>
                                        <th class="py-1">Note</th>
                                        <td class="note-detail py-1 text-gray-600">-</th>
                                      </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="separator"></div>

                            <div class="row mt-2">
                              <div class="col-7">
                                <div class="d-flex justify-content-between">
                                    @php
                                        $icon = '<i class="bi bi-pencil test-data-action d-none" style="cursor:pointer" data-transaction-id="" data-text="" id="memo-result-btn"></i>';
                                    @endphp
                                    <p>Parameter Data &nbsp;&nbsp; {!! $icon !!}</p>
                                    <div>
                                        <button class="btn btn-light-primary btn-sm mb-1 test-data-action d-none" id="verify-all-btn" data-transaction-id="">Ver All</button>
                                        <button class="btn btn-light-danger btn-sm mb-1 test-data-action d-none" id="unverify-all-btn" data-transaction-id="">Unver All</button>
                                        {{-- <br> --}}
                                        <button class="btn btn-light-primary btn-sm test-data-action d-none" id="validate-all-btn" data-transaction-id="">Val All</button>
                                        <button class="btn btn-light-danger btn-sm mb-1 test-data-action d-none" id="unvalidate-all-btn" data-transaction-id="">Unval All</button>
                                    </div>
                                </div>
                                <table class="table table-striped transaction-test-table">
                                {{-- <table class="table table-striped transaction-test-table w-100" style="display:block;height:400px;overflow-y:scroll"> --}}
                                    <thead>
                                    {{-- <thead style="position:sticky;top:0;z-index:1;background:#fff;width:100%"> --}}
                                        <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                            <td class="px-0">Test</td>
                                            <td class="px-0">Result</td>
                                            <td class="px-0">Norm</td>
                                            <td class="px-0"><i class="bi bi-info-circle"></i></td>
                                            <td class="px-0">Verf</td>
                                            <td class="px-0">Val</td>
                                        </tr>
                                    </thead>
                                    <tbody id="transaction-test-table-body">
                                        
                                    </tbody>
                                </table>
                              </div>
                              <div class="col-5">
                                <button class="btn btn-light-success btn-sm test-data-action d-none" id="go-to-post-analytics-btn" data-transaction-id="">Finish Transaction</button>
                              </div>
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

@include('dashboard.analytics.critical-modal')

@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/analytics/datatable.js')}}"></script>
<script src="{{asset('js/analytics/index.js')}}"></script>
@endsection