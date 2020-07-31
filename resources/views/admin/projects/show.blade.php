@extends('layouts.app') 
@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-4 col-sm-4 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> @lang('app.project') #{{ $project->id }} - {{ ucwords($project->project_name) }}</h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-4 col-sm-8 col-md-8 col-xs-12 text-right">
        @php
            if ($project->status == 'in progress') {
                $statusText = __('app.inProgress');
                $statusTextColor = 'text-info';
                $btnTextColor = 'btn-info';
            } else if ($project->status == 'on hold') {
                $statusText = __('app.onHold');
                $statusTextColor = 'text-warning';
                $btnTextColor = 'btn-warning';
            } else if ($project->status == 'not started') {
                $statusText = __('app.notStarted');
                $statusTextColor = 'text-warning';
                $btnTextColor = 'btn-warning';
            } else if ($project->status == 'canceled') {
                $statusText = __('app.canceled');
                $statusTextColor = 'text-danger';
                $btnTextColor = 'btn-danger';
            } else if ($project->status == 'finished') {
                $statusText = __('app.finished');
                $statusTextColor = 'text-success';
                $btnTextColor = 'btn-success';
            }
        @endphp

        <div class="btn-group dropdown">
            <button aria-expanded="true" data-toggle="dropdown"
                    class="btn b-all dropdown-toggle waves-effect waves-light visible-lg visible-md"
                    type="button">{{ $statusText }} <span style="width: 15px; height: 15px;"
                    class="btn {{ $btnTextColor }} btn-small btn-circle">&nbsp;</span></button>
            <ul role="menu" class="dropdown-menu pull-right">
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="in progress">@lang('app.inProgress')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-info btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="on hold">@lang('app.onHold')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-warning btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="not started">@lang('app.notStarted')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-warning btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="canceled">@lang('app.canceled')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-danger btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="submit-ticket" data-status="finished">@lang('app.finished')
                        <span style="width: 15px; height: 15px;"
                              class="btn btn-success btn-small btn-circle">&nbsp;</span>
                    </a>
                </li>
            </ul>
        </div>

        <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-primary btn-outline" style="font-size: small"><i class="icon-note"></i> @lang('app.edit')</a>

        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li><a href="{{ route('admin.projects.index') }}">{{ $pageTitle }}</a></li>
            <li class="active">@lang('app.details')</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection
@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/icheck/skins/all.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/multiselect/css/multi-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">

<style>
    #section-line-1 .col-in{
        padding:0 10px;
    }

    #section-line-1 .col-in h3{
        font-size: 15px;
    }
</style>
@endpush 
@section('content')

<div class="row">
    <div class="col-md-12">

        <section>
            <div class="sttabs tabs-style-line">

                @include('admin.projects.show_project_menu')

                <div class="white-box">
                    <div class="row">

                        <div class="col-md-9">
                            <div class="row project-top-stats">
                                <div class="col-md-3 m-b-20 m-t-10 text-center">
                                    <span class="text-primary">
                                        @if(!is_null($project->project_budget))
                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$project->project_budget : $project->project_budget }}
                                        @else
                                        --
                                        @endif
                                    </span> <span class="font-12 text-muted m-l-5"> @lang('modules.projects.projectBudget')</span>
                                </div>
                            
                                <div class="col-md-3 m-b-20 m-t-10 text-center b-l">

                                    <span class="text-success">
                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$earnings : $earnings }}
                                    </span> <span class="font-12 text-muted m-l-5"> @lang('app.earnings')</span>
                                </div>

                                <div class="col-md-3 m-b-20 m-t-10 text-center b-l">
                                    <span class="text-info">
                                        @if(!is_null($project->project_budget))
                                            {{ $project->hours_allocated }}
                                         @else
                                             --
                                         @endif
                                    </span> <span class="font-12 text-muted m-l-5"> @lang('modules.projects.hours_allocated')</span>
                                </div>
                                <div class="col-md-3 m-b-20 m-t-10 text-center b-l">

                                    <span class="text-warning">
                                        {{ !is_null($project->currency_id) ? $project->currency->currency_symbol.$expenses : $expenses }}
                                    </span> <span class="font-12 text-muted m-l-5"> @lang('modules.projects.expenses_total')</span>
                                    
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12" style="max-height: 400px; overflow-y: auto;">
                                    <h5>@lang('app.project') @lang('app.details')</h5>
                                    {!! $project->project_summary !!}
                                </div>
                            </div>

                            <div class="row m-t-25">
                                <div class="col-md-4">
                                    <div class="panel panel-inverse">
                                        <div class="panel-heading">@lang('modules.client.clientDetails') </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                @if(!is_null($project->client))
                                                <dl>
                                                    @if(!is_null($project->client->client))
                                                    <dt>@lang('modules.client.companyName')</dt>
                                                    <dd class="m-b-10">{{ $project->client->client[0]->company_name }}</dd>
                                                    @endif

                                                    <dt>@lang('modules.client.clientName')</dt>
                                                    <dd class="m-b-10">{{ ucwords($project->client->name) }}</dd>

                                                    <dt>@lang('modules.client.clientEmail')</dt>
                                                    <dd class="m-b-10">{{ $project->client->email }}</dd>
                                                </dl>
                                                @else @lang('messages.noClientAddedToProject') @endif {{--Custom fields data--}} @if(isset($fields))
                                                <dl>
                                                    @foreach($fields as $field)
                                                    <dt>{{ ucfirst($field->label) }}</dt>
                                                    <dd class="m-b-10">
                                                        @if( $field->type == 'text') {{$project->custom_fields_data['field_'.$field->id] ?? '-'}} @elseif($field->type == 'password')
                                                        {{$project->custom_fields_data['field_'.$field->id] ?? '-'}}
                                                        @elseif($field->type == 'number') {{$project->custom_fields_data['field_'.$field->id]
                                                        ?? '-'}} @elseif($field->type == 'textarea') {{$project->custom_fields_data['field_'.$field->id]
                                                        ?? '-'}} @elseif($field->type == 'radio') {{ !is_null($project->custom_fields_data['field_'.$field->id])
                                                        ? $project->custom_fields_data['field_'.$field->id] : '-' }}
                                                        @elseif($field->type == 'select') {{ (!is_null($project->custom_fields_data['field_'.$field->id])
                                                        && $project->custom_fields_data['field_'.$field->id] != '') ?
                                                        $field->values[$project->custom_fields_data['field_'.$field->id]]
                                                        : '-' }} @elseif($field->type == 'checkbox') {{ !is_null($project->custom_fields_data['field_'.$field->id])
                                                        ? $field->values[$project->custom_fields_data['field_'.$field->id]]
                                                        : '-' }} @elseif($field->type == 'date')
                                                            {{ \Carbon\Carbon::parse($project->custom_fields_data['field_'.$field->id])->format($global->date_format)}}
                                                        @endif
                                                    </dd>
                                                    @endforeach
                                                </dl>
                                                @endif {{--custom fields data end--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="panel panel-inverse">
                                        <div class="panel-heading">@lang('modules.projects.milestones')
                                            <a href="{{ route('admin.milestones.show', $project->id) }}" class="pull-right"><i class="fa fa-plus text-success "></i></a>
                                        </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <div id="project-milestones">
                                                    @forelse ($milestones as $key=>$item)
                                                    <div class="row">
                                                        <div class="col-xs-12 m-b-5">
                                                            <a href="javascript:;" class="milestone-detail" data-milestone-id="{{ $item->id }}">
                                                                <h6>{{ ucfirst($item->milestone_title )}}</h6>
                                                            </a>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            @if($item->status == 'complete')
                                                                <label class="label label-success">@lang('app.complete')</label> 
                                                            @else
                                                                <label class="label label-danger">@lang('app.incomplete')</label> 
                                                            @endif
                                                        </div>
                                                        <div class="col-xs-6 text-right">
                                                            @if($item->cost > 0)
                                                                {{ $item->currency->currency_symbol.$item->cost
                                                            }} 
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    @empty 
                                                        @lang('messages.noRecordFound') 
                                                    @endforelse

                                                   
                                                </div>
                    
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="panel panel-inverse">
                                        <div class="panel-heading">@lang('modules.projects.activeTimers')</div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body" id="timer-list">
                                                
                                                @forelse($activeTimers as $key=>$time)
                                                <div class="row m-b-10">
                                                    <div class="col-xs-12 m-b-5">
                                                        {{ ucwords($time->user->name) }}
                                                    </div>
                                                    <div class="col-xs-9 font-12">
                                                        {{ $time->duration }}
                                                    </div>
                                                    <div class="col-xs-3 text-right">
                                                        <button type="button" data-time-id="{{ $time->id }}" class="btn btn-danger btn-xs stop-timer">@lang('app.stop')</button>
                                                    </div>
                                                </div>
                                                
                                                @empty
                                                    @lang('messages.noActiveTimer')
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-inverse">
                                        <div class="panel-heading">@lang('modules.projects.members') 
                                            <span class="label label-rouded label-custom pull-right">{{ count($project->members) }}</span>    
                                        </div>
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                @forelse($project->members as $member)
                                                    <img src="{{ asset($member->user->image_url) }}"
                                                    data-toggle="tooltip" data-original-title="{{ ucwords($member->user->name) }}"

                                                    alt="user" class="img-circle" width="25" height="25" height="25" height="25">
                                                @empty 
                                                    @lang('messages.noMemberAddedToProject') 
                                                @endforelse
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="panel panel-inverse">
                                        
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body dashboard-stats">
                                               <div class="row">
                                                   <div class="col-md-12 m-b-5 project-stats">
                                                        <span class="text-danger">{{ count($openTasks) }}</span> @lang('modules.projects.openTasks')
                                                   </div>
                                                   <div class="col-md-12 m-b-5 project-stats">
                                                        <span class="text-info">{{ $daysLeft }}</span>@lang('modules.projects.daysLeft')
                                                   </div>
                                                   <div class="col-md-12 m-b-5 project-stats">
                                                        <span class="text-success">{{ $hoursLogged }}</span>@lang('modules.projects.hoursLogged')
                                                   </div>
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12"   id="project-timeline">
                                    <div class="panel panel-inverse">
                                        <div class="panel-heading">@lang('modules.projects.activityTimeline')</div>
                                        
                                        <div class="panel-wrapper collapse in">
                                            <div class="panel-body">
                                                <div class="steamline">
                                                    @foreach($activities as $activ)
                                                    <div class="sl-item">
                                                        <div class="sl-left"><i class="fa fa-circle text-primary"></i>
                                                        </div>
                                                        <div class="sl-right">
                                                            <div>
                                                                <h6>{{ $activ->activity }}</h6> <span class="sl-date">{{ $activ->created_at->diffForHumans() }}</span></div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
                <!-- /content -->
            </div>
            <!-- /tabs -->
        </section>
    </div>


</div>
<!-- .row -->

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="projectCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
    <!-- /.modal-dialog -->.
</div>
{{--Ajax Modal Ends--}}
@endsection
 @push('footer-script')
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/multiselect/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">
    //    (function () {
//
//        [].slice.call(document.querySelectorAll('.sttabs')).forEach(function (el) {
//            new CBPFWTabs(el);
//        });
//
//    })();

    $('#timer-list').on('click', '.stop-timer', function () {
       var id = $(this).data('time-id');
        var url = '{{route('admin.time-logs.stopTimer', ':id')}}';
        url = url.replace(':id', id);
        var token = '{{ csrf_token() }}'
        $.easyAjax({
            url: url,
            type: "POST",
            data: {timeId: id, _token: token},
            success: function (data) {
                $('#timer-list').html(data.html);
            }
        })

    });

    $('.milestone-detail').click(function(){
        var id = $(this).data('milestone-id');
        var url = '{{ route('admin.milestones.detail', ":id")}}';
        url = url.replace(':id', id);
        $('#modelHeading').html('@lang('app.update') @lang('modules.projects.milestones')');
        $.ajaxModal('#projectCategoryModal',url);
    })

    $('.submit-ticket').click(function () {

        const status = $(this).data('status');
        const url = '{{route('admin.projects.updateStatus', $project->id)}}';
        const token = '{{ csrf_token() }}'

        $.easyAjax({
            url: url,
            type: "POST",
            data: {status: status, _token: token},
            success: function (data) {
                window.location.reload();
            }
        })
    });
    $('ul.showProjectTabs .projects').addClass('tab-current');
</script>

@endpush
