@extends('layouts.member-app')

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
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('member.products.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.update') @lang('app.menu.products')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('app.update') @lang('app.menu.products')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'updateProduct','class'=>'ajax-form']) !!}
                        <input name="_method" value="PUT" type="hidden">
                        <div class="form-body">
                            <h3 class="box-title">@lang('app.menu.products') @lang('app.details')</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.name')</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ $product->name }}">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.price')</label>
                                        <input type="text" id="price" name="price" class="form-control" value="{{ $product->price }}">
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.invoices.tax')</label>
                                        <select name="tax" id="tax" class="select2 form-control">
                                            <option value="">@lang('app.zero') @lang('modules.invoices.tax') </option>
                                            @foreach($taxes as $tax)
                                                <option @if($product->tax_id == $tax->id) selected @endif value="{{ $tax->id }}">{{ $tax->tax_name }} ({{ $tax->rate_percent }}%)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">

                                        <div class="checkbox checkbox-info">
                                            <input id="purchase_allow" name="purchase_allow" value="no"
                                                   type="checkbox" @if($product->allow_purchase == 1) checked @endif>
                                            <label for="purchase_allow">@lang('app.purchaseAllow')</label>
                                        </div>
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
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('member.products.update', [$product->id])}}',
                container: '#updateProduct',
                type: "POST",
                redirect: true,
                data: $('#updateProduct').serialize()
            })
        });
    </script>
@endpush

