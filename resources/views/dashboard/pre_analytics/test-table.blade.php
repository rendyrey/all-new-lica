  <!--begin::CRUD-->
  <div class="col-md-6">
  <div class="py-5">
    <!--begin::Wrapper-->
    <div class="d-flex flex-stack flex-wrap mb-5">
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
            <input type="text" data-kt-docs-table-filter="search-test" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search test" />
        </div>
        <!--end::Search-->
        <!--begin::Toolbar-->
        {{-- <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
            <!--begin::Add customer-->
            <button type="button" class="btn btn-sm btn btn-light-primary btn-hover-rise" data-bs-toggle="tooltip" title="Add new {{ $masterData }}">
            Add {{ ucwords($masterData) }}</button>
            <!--end::Add customer-->
        </div> --}}
        <!--end::Toolbar-->
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
    <table class="table gy-1 align-middle table-striped px-0 test-datatable-ajax">
        <thead>
            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                <th class="min-w-200px">Test Name</th>
                <th class="min-w-150px">Price</th>
                <th>Type</th>
                <th class="text-end min-w-70px">Actions</th>
            </tr>
        </thead>
        <tbody class="fw-bold"></tbody>
    </table>
    <!--end::Datatable-->
</div>
<!--end::CRUD-->
</div>