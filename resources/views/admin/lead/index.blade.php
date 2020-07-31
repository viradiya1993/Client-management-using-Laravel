@extends('layouts.app')

@section('page-title')
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-6 col-sm-8 col-md-8 col-xs-12 text-right">
            <a href="{{ route('admin.leads.create') }}"
            class="btn btn-outline btn-success btn-sm">@lang('modules.lead.addNewLead') <i class="fa fa-plus"
                                                                                            aria-hidden="true"></i></a>
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
@endpush

@section('content')

    <div class="row dashboard-stats">
        <div class="col-md-12 m-b-30">
            <div class="white-box">
                <div class="col-md-4 text-center">
                    <h4><span class="text-dark">{{ $totalLeads }}</span> <span
                                class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalLeads')</span></h4>
                </div>
                <div class="col-md-4 text-center b-l">
                    <h4><span class="text-info">{{ $totalClientConverted }}</span> <span
                                class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalConvertedClient')</span>
                    </h4>
                </div>
                <div class="col-md-4 text-center b-l">
                    <h4><span class="text-warning">{{ $pendingLeadFollowUps }}</span> <span
                                class="font-12 text-muted m-l-5"> @lang('modules.dashboard.totalPendingFollowUps')</span>
                    </h4>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
 

        <div class="col-md-12">
            <div class="white-box">
                
                @section('filter-section')
                <div class="row" id="ticket-filters">
                    
                    <form action="" id="filter-form">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.lead.client')</label>
                                <select class="form-control selectpicker" name="client" id="client" data-style="form-control">
                                    <option value="all">@lang('modules.lead.all')</option>
                                    <option value="lead">@lang('modules.lead.lead')</option>
                                    <option value="client">@lang('modules.lead.client')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">@lang('modules.lead.followUp')</label>
                                <select class="form-control selectpicker" name="followUp" id="followUp" data-style="form-control">
                                    <option value="all">@lang('modules.lead.all')</option>
                                    <option value="pending">@lang('modules.lead.pending')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class="btn btn-success col-md-6"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                <button type="button" id="reset-filters" class="btn btn-inverse col-md-5 col-md-offset-1"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </form>
                </div>
                @endsection

                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="followUpModal" role="dialog" aria-labelledby="myModalLabel"
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
        <!-- /.modal-dialog -->
    </div>
    {{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="//cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

    {!! $dataTable->scripts() !!}
    <script>
        var table;
        function tableLoad() {
            window.LaravelDataTables["leads-table"].draw();
        }
        $(function() {
            tableLoad();
            $('#reset-filters').click(function () {
                $('#filter-form')[0].reset();
                $('#filter-form').find('select').selectpicker('render');
                $.easyBlockUI('#leads-table');
                tableLoad();
                $.easyUnblockUI('#leads-table');
            })
            var table;
            $('#apply-filters').click(function () {
                $('#leads-table').on('preXhr.dt', function (e, settings, data) {
                    var client = $('#client').val();
                    var followUp = $('#followUp').val();
                    data['client'] = client;
                    data['followUp'] = followUp;
                });
                $.easyBlockUI('#leads-table');
                tableLoad();
                $.easyUnblockUI('#leads-table');
            });

            $('body').on('click', '.sa-params', function(){
                var id = $(this).data('user-id');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover the deleted lead!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel please!",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm){
                    if (isConfirm) {

                        var url = "{{ route('admin.leads.destroy',':id') }}";
                        url = url.replace(':id', id);

                        var token = "{{ csrf_token() }}";

                        $.easyAjax({
                            type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                            success: function (response) {
                                if (response.status == "success") {
                                    $.easyBlockUI('#leads-table');
                                    tableLoad();
                                    $.easyUnblockUI('#leads-table');
                                }
                            }
                        });
                    }
                });
            });
        });

       function changeStatus(leadID, statusID){
           var url = "{{ route('admin.leads.change-status') }}";
           var token = "{{ csrf_token() }}";

           $.easyAjax({
               type: 'POST',
               url: url,
               data: {'_token': token,'leadID': leadID,'statusID': statusID},
               success: function (response) {
                   if (response.status == "success") {
                    $.easyBlockUI('#leads-table');
                    tableLoad();
                    $.easyUnblockUI('#leads-table');
                   }
               }
           });
        }

        $('.edit-column').click(function () {
            var id = $(this).data('column-id');
            var url = '{{ route("admin.taskboard.edit", ':id') }}';
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#edit-column-form').html(response.view);
                    $(".colorpicker").asColorPicker();
                    $('#edit-column-form').show();
                }
            })
        })

        function followUp (leadID) {

            var url = '{{ route('admin.leads.follow-up', ':id')}}';
            url = url.replace(':id', leadID);

            $('#modelHeading').html('Add Follow Up');
            $.ajaxModal('#followUpModal', url);
        }
        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        })
        function exportData(){

            var client = $('#client').val();
            var followUp = $('#followUp').val();

            var url = '{{ route('admin.leads.export', [':followUp', ':client']) }}';
            url = url.replace(':client', client);
            url = url.replace(':followUp', followUp);

            window.location.href = url;
        }
    </script>
@endpush