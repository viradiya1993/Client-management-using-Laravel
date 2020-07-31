@extends('layouts.member-app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12 text-right">
            <a href="javascript:;" id="show-add-form" class="btn btn-success btn-sm btn-outline"><i class="fa fa-clock-o"></i> @lang('modules.timeLogs.logTime')</a>
            <ol class="breadcrumb">
                <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
                <li class="active">{{ __($pageTitle) }}</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    @section('filter-section')
        <div class="row m-b-10">
            {!! Form::open(['id'=>'storePayments','class'=>'ajax-form','method'=>'POST']) !!}
            <div class="col-md-12">
                <div class="example">
                    <h5 class="box-title m-t-30">@lang('app.selectDateRange')</h5>
                    <div class="input-daterange input-group" id="date-range">
                        <input type="text" class="form-control" id="start-date" placeholder="@lang('app.startDate')" value="{{ \Carbon\Carbon::today()->subDays(7)->format($global->date_format) }}" />
                        <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                        <input type="text" class="form-control" id="end-date" placeholder="@lang('app.endDate')" value="{{ \Carbon\Carbon::today()->format($global->date_format) }}" />
                    </div>
                </div>
                </div>

            <div class="col-md-12">
                <h5 class="box-title">@if($logTimeFor->log_time_for == 'task')
                        @lang('app.selectTask')
                    @else
                        @lang('app.selectProject')
                    @endif</h5>
                <div class="form-group" >
                    <div class="row">
                        <div class="col-md-12">
                                @if($logTimeFor->log_time_for == 'task')
                                    <select class="select2 form-control"  data-placeholder="@lang('app.selectTask')" id="task_id">
                                        <option value=""></option>
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}">{{ ucwords($task->heading) }}</option>
                                        @endforeach

                                    </select>
                                @else
                                    <select class="select2 form-control" data-placeholder="@lang('app.selectProject')" id="project_id">
                                        <option value=""></option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                        @endforeach
                                    </select>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <h5 class="box-title">@lang('modules.employees.title')</h5>
                    <select class="form-control select2" name="employee" id="employee" data-style="form-control">
                        <option value="all">@lang('modules.client.all')</option>
                        @forelse($employees as $employee)
                            <option value="{{$employee->id}}">{{ ucfirst($employee->name) }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <button type="button" id="filter-results" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                    <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                </div>

            </div>
            {!! Form::close() !!}

        </div>
    @endsection

    <div class="row">
        <div class="col-md-12" >
            <div class="white-box">

                <div class="row">
                    <div class="col-md-12 hide" id="hideShowTimeLogForm">
                        {!! Form::open(['id'=>'logTime','class'=>'ajax-form','method'=>'POST']) !!}

                        <div class="form-body">
                            <div class="row m-t-30">
                                <div class="col-md-3 ">
                                    <div class="form-group">

                                        <label>@if($logTimeFor->log_time_for == 'task')
                                                @lang('app.selectTask')
                                            @else
                                                @lang('app.selectProject')
                                            @endif
                                        </label>
                                        @if($logTimeFor->log_time_for == 'task')
                                            <select class="select2 form-control" name="task_id" data-placeholder="@lang('app.selectTask')" id="task_id2">
                                                <option value=""></option>
                                                @foreach($timeLogTasks as $task)
                                                    <option value="{{ $task->id }}">{{ ucwords($task->heading) }}</option>
                                                @endforeach

                                            </select>
                                        @else
                                            <select class="select2 form-control" name="project_id" data-placeholder="@lang('app.selectProject')" id="project_id2">
                                                <option value=""></option>
                                                @foreach($timeLogProjects as $project)
                                                    <option value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 " id="employeeBox">
                                    <div class="form-group">
                                        <label>@lang('modules.timeLogs.employeeName')</label>
                                        <select class="form-control" name="user_id"
                                                id="user_id" data-style="form-control">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label>@lang('modules.timeLogs.startDate')</label>
                                        <input id="start_date" name="start_date" type="text"
                                               class="form-control"
                                               value="{{ \Carbon\Carbon::today()->format($global->date_format) }}">
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label>@lang('modules.timeLogs.endDate')</label>
                                        <input id="end_date" name="end_date" type="text"
                                               class="form-control"
                                               value="{{ \Carbon\Carbon::today()->format($global->date_format) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group bootstrap-timepicker timepicker">
                                        <label>@lang('modules.timeLogs.startTime')</label>
                                        <input type="text" name="start_time" id="start_time"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group bootstrap-timepicker timepicker">
                                        <label>@lang('modules.timeLogs.endTime')</label>
                                        <input type="text" name="end_time" id="end_time"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="">@lang('modules.timeLogs.totalHours')</label>

                                    <p id="total_time" class="form-control-static">0 Hrs</p>
                                </div>
                            </div>

                            <div class="row m-t-20">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="memo">@lang('modules.timeLogs.memo')</label>
                                        <input type="text" name="memo" id="memo"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions m-t-30">
                            <button type="button" id="save-form" class="btn btn-success"><i
                                        class="fa fa-check"></i> @lang('app.save')</button>
                        </div>
                        {!! Form::close() !!}

                        <hr>
                    </div>
                </div>

                <div class="table-responsive m-t-30">
                    <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="timelog-table">
                        <thead>
                        <tr>
                            <th>@lang('app.id')</th>
                            <th>@if($logTimeFor->log_time_for == 'task')
                                    @lang('app.task')
                                @else
                                    @lang('app.project')
                                @endif</th>
                            <th>@lang('modules.timeLogs.startTime')</th>
                            <th>@lang('modules.timeLogs.endTime')</th>
                            <th>@lang('modules.timeLogs.totalHours')</th>
                            <th>@lang('modules.timeLogs.memo')</th>
                            <th>@lang('modules.timeLogs.whoLogged')</th>
                            <th>@lang('app.action')</th>
                        </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>

    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="editTimeLogModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
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
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/timepicker/bootstrap-timepicker.min.js') }}"></script>

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<script>
   
    $('#reset-filters').click(function () {
        @if($logTimeFor->log_time_for == 'task')
            $("#task_id").select2("val", "");
         @else
           $("#project_id").select2("val", "");
        @endif
        $("#employee").select2("val", "all");
            showTable();
    })

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    jQuery('#date-range').datepicker({
        toggleActive: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    var table;

    function showTable(){

        var employee = $('#employee').val();
        var startDate = $('#start-date').val();

        if(startDate == ''){
            startDate = null;
        }

        var endDate = $('#end-date').val();

        if(endDate == ''){
            endDate = null;
        }
        var projectID;

        @if($logTimeFor->log_time_for == 'task')
            projectID = $('#task_id').val();
        @else
            projectID = $('#project_id').val();
        @endif

        if(projectID == ''){
            projectID = 0;
        }

        var url = '{{ route('member.all-time-logs.data', [':projectId', ':employee']) }}';
        url = url.replace(':projectId', projectID);
        url = url.replace(':employee', employee);

        table = $('#timelog-table').dataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                "url": url,
                "type": "POST",
                data: function (d) {
                    d.startDate = startDate;
                    d.endDate = endDate;
                    d._token = '{{ csrf_token() }}';
                }
            },
            deferRender: true,
            language: {
                "url": "<?php echo __("app.datatable") ?>"
            },
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            "order": [[ 0, "desc" ]],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'project_name', name: 'project_name' },
                { data: 'start_time', name: 'start_time' },
                { data: 'end_time', name: 'end_time' },
                { data: 'total_hours', name: 'total_hours' },
                { data: 'memo', name: 'memo' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', "searchable": false }
            ]
        });
    }

    $('#filter-results').click(function () {
        showTable();
    });


    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('time-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted time log!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('member.all-time-logs.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
//                                    swal("Deleted!", response.message, "success");
                            table._fnDraw();
                        }
                    }
                });
            }
        });
    });

    showTable();


    $('#start_time, #end_time').timepicker({
        @if($global->time_format == 'H:i')
        showMeridian: false
        @endif
    }).on('hide.timepicker', function (e) {
        calculateTime();
    });

    jQuery('#start_date, #end_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    }).on('hide', function (e) {
        calculateTime();
    });

    function calculateTime() {
        var format = '{{ $global->date_picker_format }}';
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var startTime = $("#start_time").val();
        var endTime = $("#end_time").val();

        startDate = moment(startDate, format.toUpperCase()).format('YYYY-MM-DD');
        endDate = moment(endDate, format.toUpperCase()).format('YYYY-MM-DD');

        var timeStart = new Date(startDate + " " + startTime);
        var timeEnd = new Date(endDate + " " + endTime);

        var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds

        var minutes = diff % 60;
        var hours = (diff - minutes) / 60;

        if (hours < 0 || minutes < 0) {
            var numberOfDaysToAdd = 1;
            timeEnd.setDate(timeEnd.getDate() + numberOfDaysToAdd);
            var dd = timeEnd.getDate();

            if (dd < 10) {
                dd = "0" + dd;
            }

            var mm = timeEnd.getMonth() + 1;

            if (mm < 10) {
                mm = "0" + mm;
            }

            var y = timeEnd.getFullYear();

//            $('#end_date').val(mm + '/' + dd + '/' + y);
            calculateTime();
        } else {
            $('#total_time').html(hours + "Hrs " + minutes + "Mins");
        }

//        console.log(hours+" "+minutes);
    }

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('member.all-time-logs.store')}}',
            container: '#logTime',
            type: "POST",
            data: $('#logTime').serialize(),
            success: function (data) {
                if (data.status == 'success') {
                    showTable();
                    $('#hideShowTimeLogForm').toggleClass('hide', 'show');
                }
            }
        })
    });

    $('#show-add-form').click(function () {
        $('#hideShowTimeLogForm').toggleClass('hide', 'show');
    });

    $('#project_id2').change(function () {
        var id = $(this).val();
        var url = '{{route('member.all-time-logs.members', ':id')}}';
        url = url.replace(':id', id);
        // $('employeeBox').show();
        $.easyAjax({
            url: url,
            type: "GET",
            redirect: true,
            success: function (data) {
                $('#user_id').html(data.html);
            }
        })
    });

    $('#task_id2').change(function () {
        var id = $(this).val();
        var url = '{{route('member.all-time-logs.task-members', ':id')}}';
        url = url.replace(':id', id);
        // $('employeeBox').show();
        $.easyAjax({
            url: url,
            type: "GET",
            redirect: true,
            success: function (data) {
                $('#user_id').html(data.html);
            }
        })
    });


</script>
@endpush