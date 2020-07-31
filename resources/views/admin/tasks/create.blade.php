@extends('layouts.app')

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
                <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
                <li><a href="{{ route('admin.all-tasks.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.addNew')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

    <style>
        .panel-black .panel-heading a, .panel-inverse .panel-heading a {
            color: unset !important;
        }
    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-inverse">
                <div class="panel-heading"> @lang('modules.tasks.newTask')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        {!! Form::open(['id'=>'storeTask','class'=>'ajax-form','method'=>'POST']) !!}

                        <div class="form-body">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.title')</label>
                                            <input type="text" id="heading" name="heading" class="form-control">
                                        </div>
                                    </div>
                                    @if(in_array('projects', $modules))
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">@lang('app.project')</label>
                                                <select class="select2 form-control"
                                                        data-placeholder="@lang("app.selectProject")" id="project_id"
                                                        name="project_id">
                                                    <option value=""></option>
                                                    @foreach($projects as $project)
                                                        <option
                                                                value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.tasks.taskCategory') <a
                                                        href="javascript:;"
                                                        id="createTaskCategory"
                                                        class="btn btn-sm btn-outline btn-success"><i
                                                            class="fa fa-plus"></i> @lang('modules.taskCategory.addTaskCategory')
                                                </a>
                                            </label>
                                            <select class="selectpicker form-control" name="category_id"
                                                    id="category_id"
                                                    data-style="form-control">
                                                @forelse($categories as $category)
                                                    <option value="{{ $category->id }}">{{ ucwords($category->category_name) }}</option>
                                                @empty
                                                    <option value="">@lang('messages.noTaskCategoryAdded')</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <!--/span-->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.description')</label>
                                            <textarea id="description" name="description"
                                                      class="form-control summernote"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="dependent-task" name="dependent" value="yes"
                                                       type="checkbox">
                                                <label for="dependent-task">@lang('modules.tasks.dependent')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="dependent-fields" style="display: none">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.tasks.dependentTask')</label>
                                            <select class="select2 form-control"
                                                    data-placeholder="@lang('modules.tasks.chooseTask')"
                                                    name="dependent_task_id" id="dependent_task_id">
                                                <option value=""></option>
                                                @foreach($allTasks as $allTask)
                                                    <option value="{{ $allTask->id }}">{{ $allTask->heading }}
                                                        (@lang('app.dueDate'): {{ $allTask->due_date->format($global->date_format) }}
                                                        )
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!--/span-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.startDate')</label>
                                            <input type="text" name="start_date" id="start_date2" class="form-control"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <!--/span-->

                                    <!--/span-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">@lang('app.dueDate')</label>
                                            <input type="text" name="due_date" id="due_date2" class="form-control"
                                                   autocomplete="off">
                                        </div>
                                    </div>

                                    <!--/span-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">@lang('modules.tasks.assignTo')</label>
                                            <select class="select2 select2-multiple " multiple="multiple"
                                                    data-placeholder="@lang('modules.tasks.chooseAssignee')"
                                                    name="user_id[]" id="user_id">
                                                <option value=""></option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="private-task" name="is_private" value="true"

                                                       type="checkbox">
                                                <label for="private-task">@lang('modules.tasks.makePrivate') <a
                                                            class="mytooltip font-12" href="javascript:void(0)"> <i
                                                                class="fa fa-info-circle"></i><span
                                                                class="tooltip-content5"><span
                                                                    class="tooltip-text3"><span
                                                                        class="tooltip-inner2">@lang('modules.tasks.privateInfo')</span></span></span></a></label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <div class="checkbox checkbox-info">
                                                <input id="repeat-task" name="repeat" value="yes"
                                                       type="checkbox">
                                                <label for="repeat-task">@lang('modules.events.repeat')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-12" id="repeat-fields" style="display: none">
                                        <div class="col-xs-6 col-md-4 ">
                                            <div class="form-group">
                                                <label>@lang('modules.events.repeatEvery')</label>
                                                <input type="number" min="1" value="1" name="repeat_count"
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-md-4">
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

                                        <div class="col-xs-6 col-md-4">
                                            <div class="form-group">
                                                <label>@lang('modules.events.cycles') <a class="mytooltip"
                                                                                         href="javascript:void(0)"> <i
                                                                class="fa fa-info-circle"></i><span
                                                                class="tooltip-content5"><span
                                                                    class="tooltip-text3"><span
                                                                        class="tooltip-inner2">@lang('modules.tasks.cyclesToolTip')</span></span></span></a></label>
                                                <input type="number" name="repeat_cycles" id="repeat_cycles"
                                                       class="form-control">
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
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <button type="button"
                                                class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button"
                                                style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i>
                                            File Select Or Upload
                                        </button>
                                        <div id="file-upload-box">
                                            <div class="row" id="file-dropzone">
                                                <div class="col-md-12">
                                                    <div class="dropzone"
                                                         id="file-upload-dropzone">
                                                        {{ csrf_field() }}
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple/>
                                                        </div>
                                                        <input name="image_url" id="image_url" type="hidden"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="taskID" id="taskID">
                                    </div>
                                </div>

                            </div>
                            <!--/row-->

                        </div>
                        <div class="form-actions">
                            <button type="button" id="store-task" class="btn btn-success"><i
                                        class="fa fa-check"></i> @lang('app.save')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
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
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>


    <script>
        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("div#file-upload-dropzone", {
            url: "{{ route('admin.task-files.store') }}",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*,application/pdf",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks: true,
            parallelUploads: 10,
            init: function () {
                myDropzone = this;
            }
        });

        myDropzone.on('sending', function (file, xhr, formData) {
            console.log(myDropzone.getAddedFiles().length, 'sending');
            var ids = $('#taskID').val();
            formData.append('task_id', ids);
        });

        myDropzone.on('completemultiple', function () {
            var msgs = "@lang('messages.taskCreatedSuccessfully')";
            $.showToastr(msgs, 'success');
            window.location.href = '{{ route('admin.all-tasks.index') }}'

        });
        $('.summernote').summernote({
            height: 200,                 // set editor height
            minHeight: null,             // set minimum height of editor
            maxHeight: null,             // set maximum height of editor
            focus: false,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ["view", ["fullscreen"]]
            ]
        });

        //    update task
        $('#store-task').click(function () {
            $.easyAjax({
                url: '{{route('admin.all-tasks.store')}}',
                container: '#storeTask',
                type: "POST",
                data: $('#storeTask').serialize(),
                success: function (data) {
                    $('#storeTask').trigger("reset");
                    $('.summernote').summernote('code', '');
                    if (myDropzone.getQueuedFiles().length > 0) {
                        taskID = data.taskID;
                        $('#taskID').val(data.taskID);
                        myDropzone.processQueue();
                    } else {
                        var msgs = "@lang('messages.taskCreatedSuccessfully')";
                        $.showToastr(msgs, 'success');
                        window.location.href = '{{ route('admin.all-tasks.index') }}'
                    }
                }
            })
        });

        jQuery('#due_date2, #start_date2').datepicker({
            autoclose: true,
            todayHighlight: true,
            weekStart: '{{ $global->week_start }}',
            format: '{{ $global->date_picker_format }}',
        });

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });

        $('#project_id').change(function () {
            var id = $(this).val();
            var url = '{{route('admin.all-tasks.members', ':id')}}';
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: "GET",
                redirect: true,
                success: function (data) {
                    $('#user_id').html(data.html);
                }
            })

            // For getting dependent task
            var dependentTaskUrl = '{{route('admin.all-tasks.dependent-tasks', ':id')}}';
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
            if ($(this).is(':checked')) {
                $('#repeat-fields').show();
            } else {
                $('#repeat-fields').hide();
            }
        })

        $('#dependent-task').change(function () {
            if ($(this).is(':checked')) {
                $('#dependent-fields').show();
            } else {
                $('#dependent-fields').hide();
            }
        })
    </script>
    <script>
        $('#createTaskCategory').click(function () {
            var url = '{{ route('admin.taskCategory.create-cat')}}';
            $('#modelHeading').html("@lang('modules.taskCategory.manageTaskCategory')");
            $.ajaxModal('#taskCategoryModal', url);
        })
    </script>
@endpush

