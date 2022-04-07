@extends('layouts.main')
@section('body')
<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Authentication - Sign-in -->
			<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(metronic_assets/media/illustrations/sketchy-1/14.png">
				<!--begin::Content-->
				<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
					<!--begin::Logo-->
					{{-- <a href="../../demo1/dist/index.html" class="mb-12">
						<img alt="Logo" src="assets/media/logos/logo-1.svg" class="h-40px" />
					</a> --}}
					<!--end::Logo-->
					<!--begin::Wrapper-->
					<div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
						{{-- {{dd(session())}} --}}
						@if (Session::get('error'))
							<!--begin::Alert-->
							<div class="alert alert-danger">
								<!--begin::Wrapper-->
								<div class="d-flex flex-column">
									<!--begin::Title-->
									<h4 class="mb-1 text-danger">Credential Is Invalid</h4>
									<!--end::Title-->
									<!--begin::Content-->
									<span>{{ Session::get('error') }}</span>
									<!--end::Content-->
								</div>
								<!--end::Wrapper-->
							</div>
							<!--end::Alert-->
						@endif
						<!--begin::Form-->
						{{-- <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="../../demo1/dist/index.html" action="#"> --}}
                            {!! Form::open(['route' => 'login', 'method' => 'POST']) !!}
							<!--begin::Heading-->
							<div class="text-center mb-10">
								<!--begin::Title-->
								<h1 class="text-dark mb-3">Sign In to LICA</h1>
								<!--end::Title-->
								<!--begin::Link-->
								<div class="text-gray-400 fw-bold fs-4">New Here?
								<a href="{{url('register')}}" class="link-primary fw-bolder">Create an Account</a></div>
								<!--end::Link-->
							</div>
							<!--begin::Heading-->
							<!--begin::Input group-->
							<div class="fv-row mb-10">
								<!--begin::Label-->
								<label class="form-label fs-6 fw-bolder text-dark">Email or Username</label>
								<!--end::Label-->
								<!--begin::Input-->
								{{-- <input class="form-control form-control-lg form-control-solid" type="text" name="email" autocomplete="off" /> --}}
                                {{ Form::text('email', null, ['class' => 'form-control form-control-lg form-control-solid', 'autocomplete' => 'off'])}}
								<!--end::Input-->
							</div>
							<!--end::Input group-->
							<!--begin::Input group-->
							<div class="fv-row mb-10">
								<!--begin::Wrapper-->
								<div class="d-flex flex-stack mb-2">
									<!--begin::Label-->
									<label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
									<!--end::Label-->
									
								</div>
								<!--end::Wrapper-->
								<!--begin::Input-->
								{{-- <input class="form-control form-control-lg form-control-solid" type="password" name="password" autocomplete="off" /> --}}
                                {{ Form::password('password', ['class' => 'form-control form-control-lg form-control-solid mb-6']) }}
								<!--end::Input-->
								<!--begin::Link-->
								@if (Route::has('password.request'))
									<a href="{{ route('password.request') }}" class="link-primary fs-6 fw-bolder">Forgot Password ?</a>                                    
								@endif
							<!--end::Link-->
							</div>
							<!--end::Input group-->
                            <div class="fv-row">
                                <div class="col-md-12">
                                    {{ Form::submit('Login', ['class' => 'btn btn-lg btn-block btn-primary']) }}
                                </div>
                            </div>
						</form>
						<!--end::Form-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Authentication - Sign-in-->
		</div>
		<!--end::Root-->
		<!--end::Main-->
@endsection