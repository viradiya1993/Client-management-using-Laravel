@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right">
            <a href="{{ route('admin.payments.create') }}" class="btn btn-outline btn-success btn-sm">@lang('modules.payments.addPayment') <i class="fa fa-plus" aria-hidden="true"></i></a>

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                @section('filter-section')
                <div class="row" id="ticket-filters">

                    <form action="" id="filter-form">
                        <div class="col-md-12">
                            <h5 >@lang('app.selectDateRange')</h5>
                            <div class="input-daterange input-group" id="date-range">
                                <input type="text" class="form-control" autocomplete="off" id="start-date" placeholder="@lang('app.startDate')"
                                       value=""/>
                                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                                <input type="text" class="form-control" autocomplete="off" id="end-date" placeholder="@lang('app.endDate')"
                                       value=""/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h5 >@lang('app.status')</h5>
                            <div class="form-group">
                                {{--<label class="control-label">@lang('app.status')</label>--}}
                                <select class="form-control" name="status" id="status" data-style="form-control">
                                    <option value="all">@lang('app.all')</option>
                                    <option value="complete">@lang('app.completed')</option>
                                    <option value="pending">@lang('app.pending')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h5 >@lang('app.project')</h5>
                            <div class="form-group">
                                <select class="form-control select2" name="project" id="project" data-style="form-control">
                                    <option value="all">@lang('modules.client.all')</option>
                                    @forelse($projects as $project)
                                        <option value="{{$project->id}}">{{ $project->project_name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label col-xs-12">&nbsp;</label>
                                <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </form>
                </div>
                @endsection

{{--                <div class="row">--}}
{{--                    <div class="col-md-6 pull-right">--}}
{{--                        {!! Form::open(['id'=>'importExcel','class'=>'ajax-form','method'=>'POST']) !!}--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="col-md-12 text-right">--}}
{{--                                <div class="checkbox checkbox-info">--}}
{{--                                    <input id="calculate-task-progress" name="currency_character" value="true"--}}
{{--                                           type="checkbox">--}}
{{--                                    <label for="calculate-task-progress">@lang('modules.payments.firstCharacter')</label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">--}}
{{--                                    <div class="form-control" data-trigger="fileinput">--}}
{{--                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>--}}
{{--                                        <span class="fileinput-filename"></span>--}}
{{--                                    </div>--}}
{{--                                    <span class="input-group-addon btn btn-default btn-file">--}}
{{--                                        <span class="fileinput-new"><i class="fa fa-file-excel-o text-success"></i> @lang('modules.payments.import')</span>--}}
{{--                                            <span class="fileinput-exists">@lang('app.change')</span>--}}
{{--                                            <input type="file" name="import_file" id="import_file">--}}
{{--                                            </span>--}}
{{--                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">@lang('app.remove')</a>--}}
{{--                                    <a href="javascript:;" id="import-excel" class="input-group-addon btn btn-success fileinput-exists text-white" data-dismiss="fileinput">@lang('app.submit')</a>--}}
{{--                                </div>--}}

{{--                                <a href="{{ route('admin.payments.downloadSample') }}" class="btn btn-success"><i class="fa fa-download"></i> @lang('app.sampleFile')</a>--}}

{{--                            </div>--}}
{{--                        </div>--}}

{{--                        {!! Form::close() !!}--}}

{{--                    </div>--}}
{{--                </div>--}}
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>
    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#payments-table').on('preXhr.dt', function (e, settings, data) {
        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var status = $('#status').val();
        var project = $('#project').val();
        var client = $('#client').val();

        data['startDate'] = startDate;
        data['endDate'] = endDate;
        data['status'] = status;
        data['project'] = project;
        data['client'] = client;
    });
    
    var table;
    $(function() {
        jQuery('#date-range').datepicker({
            toggleActive: true,
            language: '{{ $global->locale }}',
            autoclose: true,
            weekStart:'{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });
        loadTable();

        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('payment-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted payment record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.payments.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                loadTable();
                            }
                        }
                    });
                }
            });
        });

        $('#import-excel').click(function () {
            $.easyAjax({
                url: '{{route('admin.payments.importExcel')}}',
                container: '#importExcel',
                type: "POST",
                redirect: true,
                file: (document.getElementById("import_file").files.length == 0) ? false : true
            })
        });


    });

    function loadTable(){
        window.LaravelDataTables["payments-table"].draw();
    }

    $('.toggle-filter').click(function () {
        $('#ticket-filters').toggle('slide');
    })

    $('#apply-filters').click(function () {
        loadTable();
    });

    $('#reset-filters').click(function () {
        $('#filter-form')[0].reset();
        $('.select2').val('all');
       $('#project').select2();
        loadTable();
    })

    function exportData(){

        var startDate = $('#start-date').val();

        if (startDate == '') {
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if (endDate == '') {
            endDate = null;
        }

        var status = $('#status').val();

        var project = $('#project').val();

        var url = '{{ route('admin.payments.export', [':startDate', ':endDate', ':status', ':project']) }}';
        url = url.replace(':startDate', startDate);
        url = url.replace(':endDate', endDate);
        url = url.replace(':status', status);
        url = url.replace(':project', project);

        window.location.href = url;
    }


</script>
@endpush
