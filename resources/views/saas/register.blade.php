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
    <link href="{{ asset('front/plugin/froiden-helper/helper.css') }}" rel="stylesheet">
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

    </style>
</head>

<body id="home">


<!-- Topbar -->
@include('sections.saas.saas_header')
<!-- END Topbar -->

<!-- Header -->
<!-- END Header -->


<section class="sp-100 login-section" id="section-contact">
    <div class="container">
        <div class="login-box mt-5 shadow bg-white form-section">
            <h4 class="mb-0">
                @lang('app.signup')
            </h4>
            {!! Form::open(['id'=>'register', 'method'=>'POST']) !!}
            <div class="row">
                <div id="alert" class="col-lg-12 col-12">

                </div>
                <div class="col-12" id="form-box">
                    <div class="form-group mb-4">
                        <label for="company_name">{{ __('modules.client.companyName') }}</label>
                        <input type="text" name="company_name" id="company_name" placeholder="{{ __('modules.client.companyName') }}" class="form-control">
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">{{ __('app.yourEmailAddress') }}</label>
                        <input type="email" name="email" id="email" placeholder="{{ __('app.yourEmailAddress') }}" class="form-control">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password">{{__('modules.client.password')}}</label>
                        <input type="password" class="form-control " id="password" name="password" placeholder="{{__('modules.client.password')}}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password_confirmation">{{__('app.confirmPassword')}}</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{__('app.confirmPassword')}}">
                    </div>
                    @if(!is_null($global->google_recaptcha_key))
                        <div class="form-group mb-4">
                            <div class="g-recaptcha" data-sitekey="{{ $global->google_recaptcha_key }}"></div>
                        </div>
                    @endif
                    <button type="button" class="btn btn-lg btn-custom mt-2" id="save-form">
                        @lang('app.signup')
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>

<!-- END Main container -->

<!-- Cta -->
{{--@include('saas.sections.cta')--}}
<!-- End Cta -->

<!-- Footer -->
@include('sections.saas.saas_footer')
<!-- END Footer -->



<!-- Scripts -->
<script src="{{ asset('saas/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('saas/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('saas/vendor/slick/slick.min.js') }}"></script>
<script src="{{ asset('saas/vendor/wowjs/wow.min.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('saas/js/main.js') }}"></script>
<script src="{{ asset('front/plugin/froiden-helper/helper.js') }}"></script>
<!-- Global Required JS -->

<script>
    $('#save-form').click(function () {


        $.easyAjax({
            url: '{{route('front.signup.store')}}',
            container: '.form-section',
            type: "POST",
            data: $('#register').serialize(),
            messagePosition: "inline",
            success: function (response) {
                if (response.status == 'success') {
                    $('#form-box').remove();
                } else if (response.status == 'fail') {
                    @if(!is_null($global->google_recaptcha_key))
                    grecaptcha.reset();
                    @endif

                }
            }
        })
    });
</script>

</body>
</html>
