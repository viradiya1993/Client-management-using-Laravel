@extends('layouts.super-admin')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('super-admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection
@push('head-script')
    <link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/iconpicker/css/fontawesome-iconpicker.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel">

                <div class="vtabs customvtab p-t-10">
                    @include('sections.front_setting_new_theme_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="white-box">
                                        {!! Form::open(['id'=>'editSettings','class'=>'ajax-form']) !!}
                                        <h3>@lang('modules.frontCms.priceDetail')</h3>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-12 col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <label for="price_title">@lang('modules.frontCms.priceTitle')</label>
                                                    <input type="tel" class="form-control" id="price_title" name="price_title"
                                                           value="{{ $frontDetail->price_title }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label for="price_description">@lang('modules.frontCms.priceDescription')</label>
                                                    <textarea class="form-control" id="price_description" rows="5"
                                                              name="price_description">{{ $frontDetail->price_description }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" id="save-form"
                                                class="btn btn-success waves-effect waves-light m-r-10">
                                            @lang('app.update')
                                        </button>

                                        {!! Form::close() !!}
                                        <hr>
                                    </div>
                                </div>
                            </div>    <!-- .row -->

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('footer-script')
  <script>

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('super-admin.price-setting-update')}}',
            container: '#editSettings',
            type: "POST",
            data: $('#editSettings').serialize()
        })
    });

</script>
@endpush
