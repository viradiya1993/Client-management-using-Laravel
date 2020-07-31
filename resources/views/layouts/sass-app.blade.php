<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">


    <title> {{ __($pageTitle) }} | {{ ucwords($setting->company_name)}}</title>

    <!-- Bootstrap CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/animate-css/animate.min.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/vendor/slick/slick-theme.css') }}">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/fonts/flaticon/flaticon.css') }}">
    <link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('saas/css/main.css') }}">
    <!-- Template Font Family  -->
    <link type="text/css" rel="stylesheet" media="all"
          href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap">
    <link type="text/css" rel="stylesheet" media="all"
          href="{{ asset('saas/vendor/material-design-iconic-font/css/material-design-iconic-font.min.css') }}">

    <script src="https://www.google.com/recaptcha/api.js"></script>
    <style>
        :root {
            --main-color: {{ $frontDetail->primary_color }};
        }
        .help-block {
            color: #8a1f11 !important;
        }
        .js-cookie-consent{
            position: fixed;
            bottom: 0;
            z-index: 1000;
            width: 100%;
        }
    </style>
    @stack('head-script')

</head>

<body id="home">


<!-- Topbar -->
@include('sections.saas.saas_header')
<!-- END Topbar -->

<!-- Header -->
@yield('header-section')
<!-- END Header -->
@if(\Illuminate\Support\Facades\Route::currentRouteName() != 'front.home' && \Illuminate\Support\Facades\Route::currentRouteName() != 'front.get-email-verification')
<section class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="text-uppercase mb-4">{{ ucfirst($pageTitle) }}</h2>
                <ul class="breadcrumb mb-0 justify-content-center">
                    <li class="breadcrumb-item"><a href="#">@lang('app.home')</a></li>
                    <li class="breadcrumb-item active">{{ ucfirst($pageTitle) }}</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endif
@yield('content')


<!-- Cta -->
@include('saas.section.cta')
<!-- End Cta -->

<!-- Footer -->
@include('sections.saas.saas_footer')
<!-- END Footer -->



<!-- Scripts -->
<script src="{{ asset('saas/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('saas/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('saas/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('saas/vendor/wowjs/wow.min.js') }}"></script>
<script src="{{ asset('saas/js/main.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<!-- Global Required JS -->

@stack('footer-script')
</body>
</html>
