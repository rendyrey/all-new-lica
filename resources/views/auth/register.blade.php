<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic - Bootstrap 5 HTML, VueJS, React, Angular & Laravel Admin Dashboard Theme
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
	<!--begin::Head-->
	<head>
        <base href="{{url('metronic_assets')}}">
		<title>All New LICA</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
		<meta property="og:url" content="https://keenthemes.com/metronic" />
		<meta property="og:site_name" content="Keenthemes | Metronic" />
		<link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
		<link rel="shortcut icon" href="{{asset('metronic_assets/media/logos/favicon.ico')}}" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="{{asset('metronic_assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('metronic_assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="bg-body">
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Authentication - Sign-up -->
			<div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(metronic_assets/media/illustrations/sketchy-1/14.png">
				<!--begin::Content-->
				<div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
					<!--begin::Wrapper-->
					<div class="w-lg-600px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
						<!--begin::Form-->
						{{-- <form class="form w-100" novalidate="novalidate" id="sign-up-form"> --}}
                            {!! Form::open(['route' => 'register', 'class' => 'form w-100', 'id' => 'sign-up-form']) !!}
							<!--begin::Heading-->
							<div class="mb-10 text-center">
								<!--begin::Title-->
								<h1 class="text-dark mb-3">Create an LICA Account</h1>
								<!--end::Title-->
							</div>
							<!--end::Heading-->
							<!--begin::Input group-->
							<div class="row fv-row mb-7">
								<!--begin::Col-->
								<div class="col-xl">
									<label class="form-label fw-bolder text-dark fs-6">Name</label>
									{{-- <input class="form-control form-control-lg form-control-solid" type="text" placeholder="" name="name" autocomplete="off" /> --}}
                                    {{ Form::text('name', null, ['class' => 'form-control form-control-lg form-control-solid', 'placeholder' => 'E.g. John Peter'])}}
								</div>
								<!--end::Col-->
							</div>
							<!--end::Input group-->
							<!--begin::Input group-->
							<div class="fv-row mb-7">
								<label class="form-label fw-bolder text-dark fs-6">Email</label>
								{{-- <input class="form-control form-control-lg form-control-solid" type="email" placeholder="" name="email" autocomplete="off" /> --}}
                                {{ Form::email('email', null, ['class' => 'form-control form-control-lg form-control-solid', 'placeholder' => 'E.g. johnpeter@mail.com'])}}
							</div>
							<!--end::Input group-->
                            <!--begin::Input group-->
							<div class="fv-row mb-7">
								<label class="form-label fw-bolder text-dark fs-6">Username</label>
								{{-- <input class="form-control form-control-lg form-control-solid" type="text" placeholder="" name="username" autocomplete="off" /> --}}
                                {{ Form::text('username', null, ['class' => 'form-control form-control-lg form-control-solid', 'placeholder' => 'E.g. johnpeter '])}}
							</div>
							<!--end::Input group-->
							<!--begin::Input group-->
							<div class="fv-row mb-7">
								<!--begin::Wrapper-->
								<div class="mb-1">
									<!--begin::Label-->
									<label class="form-label fw-bolder text-dark fs-6">Password</label>
									<!--end::Label-->
									<!--begin::Input wrapper-->
									<div class="position-relative mb-3">
										{{-- <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="password" autocomplete="off" /> --}}
                                        {{ Form::password('password', ['class' => 'form-control form-control-lg form-control-slid'])}}
									</div>
									<!--end::Input wrapper-->
								</div>
								<!--end::Wrapper-->
							</div>
							<!--end::Input group=-->
							<!--begin::Input group-->
							<div class="fv-row mb-5">
								<label class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
								{{-- <input class="form-control form-control-lg form-control-solid" type="password" placeholder="" name="confirm-password" autocomplete="off" /> --}}
                                {{ Form::password('password_confirmation', ['class' => 'form-control form-control-lg form-control-solid'])}}
							</div>
							<!--end::Input group-->
							<!--begin::Actions-->
							<div class="text-center">
								<button type="button" id="sign-up-submit" class="btn btn-lg btn-primary">
									<span class="indicator-label">Submit</span>
									<span class="indicator-progress">Please wait...
									<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
								</button>
							</div>
							<!--end::Actions-->
						</form>
						<!--end::Form-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Authentication - Sign-up-->
		</div>
		<!--end::Root-->
		<!--end::Main-->
		<!--begin::Javascript-->
		{{-- <script>var hostUrl = "/";</script> --}}
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="{{asset('metronic_assets/plugins/global/plugins.bundle.js')}}"></script>
		<script src="{{asset('metronic_assets/js/scripts.bundle.js')}}"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Page Custom Javascript(used by this page)-->
		{{-- <script src="{{asset('metronic_assets/js/custom/authentication/sign-up/general.js')}}"></script> --}}
		<!--end::Page Custom Javascript-->
		<!--end::Javascript-->
        <script src="{{asset('metronic_assets/js/sign-up.js')}}"></script>
	</body>
	<!--end::Body-->
</html>