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
<link rel="stylesheet" href="{{ asset('plugins/bower_components/clockpicker/dist/jquery-clockpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">@lang('modules.frontCms.updateTitle')</div>

                <div class="vtabs customvtab m-t-10">
                    @include('sections.front_setting_new_theme_menu')

                    <div class="tab-content">
                        <div id="vhome3" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0"> @lang("modules.frontSettings.title")</h3>

                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                {!! Form::open(['id'=>'editSettings','class'=>'ajax-form','method'=>'PUT']) !!}
                                                <h4>@lang('modules.frontCms.frontDetail')</h4>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="company_name" class="d-block">@lang('modules.frontCms.primaryColor')</label>
                                                            <input type="text" name="primary_color" class="gradient-colorpicker form-control" autocomplete="off" value="{{ $frontDetail->primary_color }}" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="company_name">@lang('modules.frontCms.headerTitle')</label>
                                                            <input type="text" class="form-control" id="header_title" name="header_title"
                                                                   value="{{ $frontDetail->header_title }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="address">@lang('modules.frontCms.headerDescription')</label>
                                                            <textarea class="form-control" id="header_description" rows="5"
                                                                      name="header_description">{{ $frontDetail->header_description }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6 col-xs-12">
                                                        <div class="form-group">
                                                            <label for="exampleInputPassword1">@lang('modules.frontCms.mainImage')</label>
                                                            <div class="col-md-12">
                                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                    <div class="fileinput-new thumbnail"
                                                                         style="width: 200px; height: 150px;">
                                                                         <img src="{{ $frontDetail->image_url }}" alt=""/>
                                                                    </div>
                                                                    <div class="fileinput-preview fileinput-exists thumbnail"
                                                                         style="max-width: 200px; max-height: 150px;"></div>
                                                                    <div>
                                <span class="btn btn-info btn-file">
                                    <span class="fileinput-new"> @lang('app.selectImage') </span>
                                    <span class="fileinput-exists"> @lang('app.change') </span>
                                    <input type="file" name="image" id="image"> </span>
                                                                        <a href="javascript:;" class="btn btn-danger fileinput-exists"
                                                                           data-dismiss="fileinput"> @lang('app.remove') </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="alert alert-info"><i class="fa fa-info-circle"></i> @lang('messages.headerImageSizeMessage')</div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-info  col-md-10">
                                                                <input id="get_started_show" name="get_started_show" value="yes"
                                                                       @if($frontDetail->get_started_show == "yes") checked
                                                                       @endif
                                                                       type="checkbox">
                                                                <label for="get_started_show">@lang('modules.frontCms.getStartedButtonShow')</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-info  col-md-10">
                                                                <input id="sign_in_show" name="sign_in_show" value="yes"
                                                                       @if($frontDetail->sign_in_show == "yes") checked
                                                                       @endif
                                                                       type="checkbox">
                                                                <label for="sign_in_show">@lang('modules.frontCms.singInButtonShow')</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>





                                                <h4 id="social-links">@lang('modules.frontCms.socialLinks')</h4>
                                                <hr>
                                                <span class="text-danger">@lang('modules.frontCms.socialLinksNote')</span><br><br>
                                                <div class="row">
                                                    @foreach(json_decode($frontDetail->social_links) as $link)

                                                        <div class="col-sm-12 col-md-3 col-xs-12">
                                                            <div class="form-group">
                                                                <label for="{{ $link->name }}">
                                                                    @lang('modules.frontCms.'.$link->name)
                                                                </label>
                                                                <input
                                                                        class="form-control"
                                                                        id="{{ $link->name }}"
                                                                        name="social_links[{{ $link->name }}]"
                                                                        type="url"
                                                                        value="{{ $link->link }}"
                                                                        placeholder="@lang('modules.frontCms.enter'.ucfirst($link->name).'Link')">
                                                            </div>
                                                        </div>

                                                    @endforeach
                                                </div>

                                                <button type="submit" id="save-form"
                                                        class="btn btn-success waves-effect waves-light m-r-10">
                                                    @lang('app.update')
                                                </button>

                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- .row -->
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
    <!-- .row -->



@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ asset('plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
<script>
    // Colorpicker
    $(".colorpicker").asColorPicker();
    $(".complex-colorpicker").asColorPicker({
        mode: 'complex'
    });
    $(".gradient-colorpicker").asColorPicker(
        // {
        //     mode: 'gradient'
        // }
    );
    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('super-admin.front-settings.update', $frontDetail->id)}}',
            container: '#editSettings',
            type: "POST",
            redirect: true,
            file: true,
        })
    });

</script>
@endpush
