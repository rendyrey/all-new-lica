<div class="modal fade" tabindex="-1" id="add-patient-modal">
  <div class="modal-dialog modal-xl ">
      <div class="modal-content">
          <div class="modal-header">
              <h2 class="modal-title">Add New Data</h2>

              <!--begin::Close-->
              <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                  <span class="svg-icon svg-icon-2x"></span>
              </div>
              <!--end::Close-->
          </div>

          <div class="modal-body">
              <!--begin::Stepper-->
              <div class="stepper stepper-pills" id="kt_stepper_example_basic">
                <!--begin::Nav-->
                <div class="stepper-nav flex-center flex-wrap mb-2 mt-0">
                    <!--begin::Step 1-->
                    <div class="stepper-item current" data-kt-stepper-element="nav">
                        <!--begin::Line-->
                        <div class="stepper-line w-40px"></div>
                        <!--end::Line-->

                        <!--begin::Icon-->
                        <div class="stepper-icon w-30px h-30px">
                            <i class="stepper-check fas fa-check"></i>
                            <span class="stepper-number fs-6">1</span>
                        </div>
                        <!--end::Icon-->

                        <!--begin::Label-->
                        <div class="stepper-label">
                            <h5 class="stepper-title fs-7">
                                Add Patient
                            </h5>

                            <div class="stepper-desc fs-7">
                                Step 1
                            </div>
                        </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Step 1-->

                    <!--begin::Step 2-->
                    <div class="stepper-item" data-kt-stepper-element="nav">
                        <!--begin::Line-->
                        <div class="stepper-line w-40px"></div>
                        <!--end::Line-->

                        <!--begin::Icon-->
                        <div class="stepper-icon w-30px h-30px">
                            <i class="stepper-check fas fa-check"></i>
                            <span class="stepper-number fs-6">2</span>
                        </div>
                        <!--begin::Icon-->

                        <!--begin::Label-->
                        <div class="stepper-label">
                            <h3 class="stepper-title fs-7">
                                Step 2
                            </h3>

                            <div class="stepper-desc fs-7">
                                Description
                            </div>
                        </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Step 2-->
                </div>
                <!--end::Nav-->

                <!--begin::Form-->
                {{-- <form class="form mx-auto" novalidate="novalidate" id="kt_stepper_example_basic_form"> --}}
                  {!! Form::open(['url' => 'pre-analytic/new', 'class' => '', 'id' => 'new-pre-analytics']) !!}
                    <!--begin::Group-->
                    <div class="mb-5">
                        <!--begin::Step 1-->
                        <div class="flex-column current" data-kt-stepper-element="content">
                          <div class="mb-4 row">
                            <div class="col-md-1 my-auto">
                              <label class="form-label fs-5">Search</label>
                            </div>
                            <div class="col-md-11">
                              <div class="input-group input-group-solid flex-nowrap">
                                <span class="input-group-text"><i class="bi bi-person-circle fs-4"></i></span>
                                <div class="overflow-hidden flex-grow-1">
                                  {{ Form::select('patient_id', [], null, ['class' => 'form-select form-select-solid rounded-0 border-start select-two select-patient', 'data-control' => 'select2', 'data-placeholder' => 'Select patient']) }}
                                </div>
                                <span class="input-group-text bg-danger text-white cursor-pointer cancel-new-patient d-none" onClick="cancelNewPatient()">Cancel</span>
                                <span class="input-group-text bg-primary text-white cursor-pointer add-new-patient" onClick="addNewPatient()">Add new Patient</span>
                              </div>
                            </div>
                          </div>
                          <div class="row form-step-1">
                            <div class="col-md-6 patient-form">
                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Patient Name</label></div>
                                <div class="col-md-9">
                                  {{ Form::text('name', null, ['class' => 'form-control form-control-solid form-control-sm req-input', 'id' => 'first-input', 'disabled', 'readonly']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Email</label></div>
                                <div class="col-md-9">
                                  {{ Form::text('email', null, ['class' => 'form-control form-control-solid form-control-sm req-input', 'disabled', 'readonly']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Phone</label></div>
                                <div class="col-md-9">
                                  {{ Form::text('phone', null, ['class' => 'form-control form-control-solid form-control-sm req-input', 'disabled', 'readonly']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Medical Record</label></div>
                                <div class="col-md-9">
                                  {{ Form::text('medrec', null, ['class' => 'form-control form-control-solid form-control-sm req-input', 'disabled', 'readonly']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Birthdate</label></div>
                                <div class="col-md-9">
                                  {{ Form::text('birthdate', null, ['class' => 'form-control form-control-solid form-control-sm birthdate req-input', 'disabled', 'readonly']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Gender</label></div>
                                <div class="col-md-9">
                                  <div class="row">
                                    <div class="col-3">
                                        <div class="form-check form-check-custom form-check-solid me-10">
                                            {{ Form::radio('gender', 'M', null, ['class' => 'form-check-input h-15px w-15px', 'id' => 'radio-male', 'disabled', 'readonly']) }}
                                            <label class="form-check-label mr-1" for="radio-male">
                                                Male
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-check form-check-custom form-check-solid me-10">
                                            {{ Form::radio('gender', 'F', null, ['class' => 'form-check-input h-15px w-15px', 'id' => 'radio-female', 'disabled', 'readonly']) }}
                                            <label class="form-check-label" for="radio-female">
                                                Female
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Address</label></div>
                                <div class="col-md-9">
                                  {{ Form::textarea('address', null, ['class' => 'h-80px form-control form-control-solid form-control-sm', 'disabled', 'readonly']) }}
                                </div>
                              </div>
                              <!-- End Input -->
                            </div>

                            <div class="col-md-6 patient-right-form">
                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Insurance</label></div>
                                <div class="col-md-9">
                                  {{ Form::select('insurance_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-insurance req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select insurance']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Type</label></div>
                                <div class="col-md-9">
                                  {{ Form::select('type', array_replace(Helper::roomType(),['' => '']), null, ['class' => 'form-select form-select-sm form-select-solid select-two req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select type', 'data-hide-search' => 'true', 'id' => 'select-type']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Room</label></div>
                                <div class="col-md-9">
                                  {{ Form::select('room_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-room req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select room']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Physican/Doctor</label></div>
                                <div class="col-md-9">
                                  {{ Form::select('doctor_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-doctor req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select doctor']) }}
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Cito</label></div>
                                <div class="col-md-9">
                                  <div class="form-check form-check-custom form-check-solid">
                                    {{ Form::checkbox('cito', true, false, ['class' => 'form-check-input', 'id' => 'cito']) }}
                                    <label class="form-check-label" for="cito">
                                      CITO
                                    </label>
                                  </div>
                                </div>
                              </div>

                              <div class="fv-row row mb-4">
                                <div class="col-md-3"><label class="form-label fs-7">Diagnosis</label></div>
                                <div class="col-md-9">
                                  {{ Form::textarea('diagnosis', null, ['class' => 'h-80px form-control form-control-solid form-control-sm']) }}
                                </div>
                              </div>
                              <!-- End Input -->
                            </div>
                          </div>
                        </div>
                        <!--begin::Step 1-->

                        <!--begin::Step 2-->
                        <div class="flex-column" data-kt-stepper-element="content">
                          @include('dashboard.pre_analytics.test-table')
                        </div>
                        <!--begin::Step 2-->
                    </div>
                    <!--end::Group-->

                    <!--begin::Actions-->
                    <div class="d-flex flex-center">
                        <!--begin::Wrapper-->
                        <div class="me-2">
                            <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                                Back
                            </button>
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Wrapper-->
                        <div>
                            <button type="button" class="btn btn-primary" data-kt-stepper-action="submit">
                                <span class="indicator-label">
                                    Submit
                                </span>
                                <span class="indicator-progress">
                                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>

                            <button type="button" class="btn btn-primary" data-kt-stepper-action="next" id="continue-btn" disabled>
                                Continue
                            </button>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
              </div>
              <!--end::Stepper-->
          </div>
      </div>
  </div>
</div>