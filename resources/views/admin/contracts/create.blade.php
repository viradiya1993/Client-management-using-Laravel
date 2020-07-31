@extends('layouts.app')
@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush
@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.contracts.index') }}">{{ $pageTitle }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="panel panel-inverse">
            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('app.add') @lang('app.menu.contract')</div>

            <p class="text-muted m-b-30 font-13"></p>

            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
            {!! Form::open(['id'=>'createContract','class'=>'ajax-form','method'=>'POST']) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company_name">@lang('app.client')</label>
                            <div>
                                <select class="select2 form-control" data-placeholder="@lang('app.client')" name="client" id="clientID">
                                    @foreach($clients as $client)
                                        <option
                                                value="{{ $client->id }}">{{ ucwords($client->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="subject">@lang('app.subject')</label>
                            <input type="text" class="form-control" id="subject" name="subject">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="subject">@lang('app.amount') ({{ $global->currency->currency_symbol }})</label>
                            <input type="number" class="form-control" id="amount" name="amount">
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.contracts.contractType')
                                <a href="javascript:;"
                                    id="createContractType"
                                    class="btn btn-sm btn-outline btn-success">
                                    <i class="fa fa-plus"></i> @lang('modules.contracts.addContractType')
                                </a>
                            </label>
                            <div>
                                <select class="select2 form-control" data-placeholder="@lang('app.client')" id="contractType" name="contract_type">
                                    @foreach($contractType as $type)
                                        <option
                                                value="{{ $type->id }}">{{ ucwords($type->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('modules.timeLogs.startDate')</label>
                            <input id="start_date" name="start_date" type="text"
                                    class="form-control"
                                    value="{{ \Carbon\Carbon::today()->format($global->date_format) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('modules.timeLogs.endDate')</label>
                            <input id="end_date" name="end_date" type="text"
                                    class="form-control"
                                    value="{{ \Carbon\Carbon::today()->format($global->date_format) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('modules.contracts.notes')</label>
                            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                    <button type="submit" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                        @lang('app.save')
                    </button>
                    <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
        </div>
    </div>
    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script>
        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });
        jQuery('#start_date, #end_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart:'{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });
        $('#save-form').click(function () {
            $.easyAjax({
                url: '{{route('admin.contracts.store')}}',
                container: '#createContract',
                type: "POST",
                redirect: true,
                data: $('#createContract').serialize()
            })
        });
        $('#createContractType').click(function(){
            var url = '{{ route('admin.contract-type.create-contract-type')}}';
            $('#modelHeading').html("@lang('modules.contracts.manageContractType')");
            $.ajaxModal('#taskCategoryModal', url);
        })
    </script>
@endpush

