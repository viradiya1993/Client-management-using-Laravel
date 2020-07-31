<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-plus"></i> @lang('modules.tasks.newTask')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'storeTask','class'=>'ajax-form','method'=>'POST']) !!}

        <div class="form-body">
            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">@lang('app.project')</label>
                        <select class="select2 form-control" data-placeholder="@lang("app.selectProject")" id="task_project_id" name="task_project_id">
                            <option value=""></option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" @if(isset($projectId) && $projectId == $project->id) selected @endif>{{ ucwords($project->project_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">@lang('app.title')</label>
                        <input type="text" id="heading" name="heading" class="form-control" >
                    </div>
                </div>
                <!--/span-->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">@lang('app.description')</label>
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">

                        <div class="checkbox checkbox-info">
                            <input id="dependent-task" name="dependent" value="yes"
                                   type="checkbox">
                            <label for="dependent-task">@lang('modules.tasks.dependent')</label>
                        </div>
                    </div>
                </div>

                <div class="row" id="dependent-fields" style="display: none">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">@lang('modules.tasks.dependentTask')</label>
                            <select class="select2 form-control" data-placeholder="@lang('modules.tasks.chooseTask')" name="dependent_task_id" id="dependent_task_id" >
                                <option value=""></option>
                                @foreach($allTasks as $allTask)
                                    <option value="{{ $allTask->id }}">{{ $allTask->heading }} (@lang('app.dueDate'): {{ $allTask->due_date->format($global->date_format) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">@lang('app.startDate')</label>
                        <input type="text" name="start_date" id="start_date2" class="form-control" autocomplete="off">
                    </div>
                </div>
                <!--/span-->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">@lang('app.dueDate')</label>
                        <input type="text" name="due_date" autocomplete="off" id="due_date2" class="form-control">
                    </div>
                </div>
                <!--/span-->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">@lang('modules.tasks.assignTo')</label>
                        <select class="select2 select2-multiple" multiple="multiple" data-placeholder="@lang('modules.tasks.chooseAssignee')" name="user_id[]" id="task_user_id" >
                            <option value=""></option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">

                        <div class="checkbox checkbox-info">
                            <input id="repeat-task" name="repeat" value="yes"
                                   type="checkbox">
                            <label for="repeat-task">@lang('modules.events.repeat')</label>
                        </div>
                    </div>
                </div>

                <div class="row" id="repeat-fields" style="display: none">
                    <div class="col-xs-12 col-md-12">
                        <div class="col-xs-6 col-md-3 ">
                            <div class="form-group">
                                <label>@lang('modules.events.repeatEvery')</label>
                                <input type="number" min="1" value="1" name="repeat_count" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <select name="repeat_type" id="" class="form-control">
                                    <option value="day">@lang('app.day')</option>
                                    <option value="week">@lang('app.week')</option>
                                    <option value="month">@lang('app.month')</option>
                                    <option value="year">@lang('app.year')</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-6 col-md-3">
                            <div class="form-group">
                                <label>@lang('modules.events.cycles') <a class="mytooltip" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.cyclesToolTip')</span></span></span></a></label>
                                <input type="number" name="repeat_cycles" id="repeat_cycles" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!--/span-->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">@lang('modules.tasks.priority')</label>

                        <div class="radio radio-danger">
                            <input type="radio" name="priority" id="radio13"
                                   value="high">
                            <label for="radio13" class="text-danger">
                                @lang('modules.tasks.high') </label>
                        </div>
                        <div class="radio radio-warning">
                            <input type="radio" name="priority"
                                   id="radio14" checked value="medium">
                            <label for="radio14" class="text-warning">
                                @lang('modules.tasks.medium') </label>
                        </div>
                        <div class="radio radio-success">
                            <input type="radio" name="priority" id="radio15"
                                   value="low">
                            <label for="radio15" class="text-success">
                                @lang('modules.tasks.low') </label>
                        </div>
                    </div>
                </div>
                <!--/span-->

            </div>
            <!--/row-->

        </div>
        <div class="form-actions">
            <button type="button" id="store-task" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
        </div>

        @if(isset($columnId))
            {!! Form::hidden('board_column_id', $columnId) !!}
        @endif

        @if(isset($pageName))
            {!! Form::hidden('page_name', $pageName) !!}
        @endif

        @if(isset($parentGanttId))
            {!! Form::hidden('parent_gantt_id', $parentGanttId) !!}
        @endif

        {!! Form::close() !!}
    </div>
</div>

<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
    //    update task
    $('#store-task').click(function () {
        $.easyAjax({
            url: '{{route('member.all-tasks.store')}}',
            container: '#storeTask',
            type: "POST",
            data: $('#storeTask').serialize(),
            success: function (response) {
                if(response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    });

    jQuery('#due_date2, #start_date2').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $("#task_project_id").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#dependent_task_id").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#task_user_id").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#task_project_id').change(function () {
        var id = $(this).val();
        var url = '{{route('member.all-tasks.members', ':id')}}';
        url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            redirect: true,
            success: function (data) {
                $('#task_user_id').html(data.html);
            }
        })

        // For getting dependent task
        var dependentTaskUrl = '{{route('member.all-tasks.dependent-tasks', ':id')}}';
        dependentTaskUrl = dependentTaskUrl.replace(':id', id);
        $.easyAjax({
            url: dependentTaskUrl,
            type: "GET",
            success: function (data) {
                $('#dependent_task_id').html(data.html);
            }
        })
    });

    $('#repeat-task').change(function () {
        if($(this).is(':checked')){
            $('#repeat-fields').show();
        }
        else{
            $('#repeat-fields').hide();
        }
    })

    $('#dependent-task').change(function () {
        if($(this).is(':checked')){
            $('#dependent-fields').show();
        }
        else{
            $('#dependent-fields').hide();
        }
    })
</script>
