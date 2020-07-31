<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title">@lang('modules.tasks.subTask')</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        {!! Form::open(['id'=>'createSubTask','class'=>'ajax-form','method'=>'PUT']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.name')</label>
                        <input type="text" name="name" id="name" value="{{ $subTask->title }}" class="form-control">
                        <input type="hidden" name="taskID" id="taskID" value="{{ $subTask->task_id }}">
                    </div>
                </div>
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.dueDate')</label>
                        <input type="text" name="due_date" autocomplete="off" @if( $subTask->due_date) value="{{ $subTask->due_date->format($global->date_format) }}" @endif id="due_date3" class="form-control datepicker">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" onclick="updateSubTask({{$subTask->id}})" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<script>
    jQuery('#due_date3').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });
</script>
