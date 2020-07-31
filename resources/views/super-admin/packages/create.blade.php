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
                <li><a href="{{ route('super-admin.packages.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <style>
        .m-b-10{
            margin-bottom: 10px;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('app.add') @lang('app.package') @lang('app.info')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'createClient','class'=>'ajax-form','method'=>'POST']) !!}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('app.name')</label>
                                            <input type="text" id="name" name="name" value="" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('app.max') @lang('app.menu.employees')</label>
                                            <input type="number" name="max_employees" id="max_employees" value=""  class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('app.annual') @lang('app.price') ({{ $global->currency->currency_symbol }})</label>
                                            <input type="number" name="annual_price" id="annual_price" value=""  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 ">
                                        <div class="form-group">
                                            <label class="control-label required">@lang('app.monthly') @lang('app.price') ({{ $global->currency->currency_symbol }})</label>
                                            <input type="number" name="monthly_price" id="monthly_price"  value=""   class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.package.stripeAnnualPlanId')</label>
                                            <input type="text" id="stripe_annual_plan_id" name="stripe_annual_plan_id" value="" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.package.stripeMonthlyPlanId')</label>
                                            <input type="text" name="stripe_monthly_plan_id" id="stripe_monthly_plan_id" value=""  class="form-control">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.package.razorpayAnnualPlanId')</label>
                                            <input type="text" id="razorpay_annual_plan_id" name="razorpay_annual_plan_id" value="" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('modules.package.razorpayMonthlyPlanId')</label>
                                            <input type="text" name="razorpay_monthly_plan_id" id="razorpay_monthly_plan_id" value=""  class="form-control">
                                        </div>
                                    </div>

                                </div>

                                <h3 class="box-title">@lang('app.select') @lang('app.module')</h3>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-info  col-md-10">
                                                <input id="select_all_permission"

                                                        class="select_all_permission" type="checkbox">
                                                <label for="select_all_permission">@lang('modules.permission.selectAll')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row form-group module-in-package">
                                            @foreach($modules as $module)
                                                <div class="col-md-2">
                                                    <div class="checkbox checkbox-inline checkbox-info m-b-10">
                                                        <input id="{{ $module->module_name }}" name="module_in_package[{{ $module->id }}]" value="{{ $module->module_name }}" class="module_checkbox"
                                                               type="checkbox">
                                                        <label for="{{ $module->module_name }}">{{ ucfirst($module->module_name) }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.description')</label>
                                            <textarea name="description"  id="description"  rows="5" value="" class="form-control"></textarea>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="form-actions">
                                <button type="submit" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>

                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('super-admin.packages.store')}}',
            container: '#createClient',
            type: "POST",
            redirect: true,
            data: $('#createClient').serialize()
        })
    });

    $('.select_all_permission').change(function () {
        if($(this).is(':checked')){
            $('.module_checkbox').prop('checked', true);
        } else {
            $('.module_checkbox').prop('checked', false);
        }
    });
</script>
@endpush

