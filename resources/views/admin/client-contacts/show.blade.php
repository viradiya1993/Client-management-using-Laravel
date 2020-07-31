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
                <li><a href="{{ route('admin.clients.index') }}">{{ __($pageTitle) }}</a></li>
                <li class="active">@lang('app.menu.contacts')</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    <div class="row">


        <div class="col-md-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-xs-6 b-r"> <strong>@lang('modules.employees.fullName')</strong> <br>
                        <p class="text-muted">{{ ucwords($client->client[0]->name) }}</p>
                    </div>
                    <div class="col-xs-6"> <strong>@lang('app.mobile')</strong> <br>
                        <p class="text-muted">{{ $client->client[0]->mobile ?? 'NA'}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 col-xs-6 b-r"> <strong>@lang('app.email')</strong> <br>
                        <p class="text-muted">{{ $client->client[0]->email }}</p>
                    </div>
                    <div class="col-md-3 col-xs-6"> <strong>@lang('modules.client.companyName')</strong> <br>
                        <p class="text-muted">{{ (count($client->client) > 0) ? ucwords($client->client[0]->company_name) : 'NA'}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 col-xs-6 b-r"> <strong>@lang('modules.client.website')</strong> <br>
                        <p class="text-muted">{{ $client->client[0]->website ?? 'NA' }}</p>
                    </div>
                    <div class="col-md-3 col-xs-6"> <strong>@lang('app.address')</strong> <br>
                        <p class="text-muted">{!!  (count($client->client) > 0) ? ucwords($client->client[0]->address) : 'NA' !!}</p>
                    </div>
                </div>

                {{--Custom fields data--}}
                @if(isset($fields))
                    <div class="row">
                        <hr>
                        @foreach($fields as $field)
                            <div class="col-md-4">
                                <strong>{{ ucfirst($field->label) }}</strong> <br>
                                <p class="text-muted">
                                    @if( $field->type == 'text')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}
                                    @elseif($field->type == 'password')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}
                                    @elseif($field->type == 'number')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}

                                    @elseif($field->type == 'textarea')
                                        {{$clientDetail->custom_fields_data['field_'.$field->id] ?? ''}}

                                    @elseif($field->type == 'radio')
                                        {{ !is_null($clientDetail->custom_fields_data['field_'.$field->id]) ? $clientDetail->custom_fields_data['field_'.$field->id] : '-' }}
                                    @elseif($field->type == 'select')
                                        {{ (!is_null($clientDetail->custom_fields_data['field_'.$field->id]) && $clientDetail->custom_fields_data['field_'.$field->id] != '') ? $field->values[$clientDetail->custom_fields_data['field_'.$field->id]] : '-' }}
                                    @elseif($field->type == 'checkbox')
                                        {{ !is_null($clientDetail->custom_fields_data['field_'.$field->id]) ? $field->values[$clientDetail->custom_fields_data['field_'.$field->id]] : '-' }}
                                    @elseif($field->type == 'date')
                                        {{ isset($clientDetail->dob)?Carbon\Carbon::parse($clientDetail->dob)->format($global->date_format):Carbon\Carbon::now()->format($global->date_format)}}
                                    @endif
                                </p>

                            </div>
                        @endforeach
                    </div>
                @endif

                {{--custom fields data end--}}
            </div>
        </div>

        <div class="col-md-12">

            <section>
                <div class="sttabs tabs-style-line">
                    <div class="white-box">
                        <nav>
                            <ul>
                                <li><a href="{{ route('admin.clients.projects', $client->client[0]->user_id) }}"><span>@lang('app.menu.projects')</span></a>
                                <li><a href="{{ route('admin.clients.invoices', $client->id) }}"><span>@lang('app.menu.invoices')</span></a>
                                </li>
                                <li class="tab-current"><a href="{{ route('admin.contacts.show', $client->id) }}"><span>@lang('app.menu.contacts')</span></a>
                                </li>
                                @if($gdpr->enable_gdpr)
                                    <li><a href="{{ route('admin.clients.gdpr', $client->id) }}"><span>@lang('modules.gdpr.gdpr')</span></a>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <div class="content-wrap">
                        <section id="section-line-3" class="show">
                            <div class="row">
                                <div class="col-md-12" id="issues-list-panel">
                                    <div class="white-box">
                                        <h2>@lang('app.menu.contacts')</h2>

                                        <div class="row m-b-10">
                                            <div class="col-md-12">
                                                <a href="javascript:;" id="show-add-form"
                                                   class="btn btn-success btn-outline"><i class="fa fa-user-plus"></i> @lang('modules.contacts.addContact')</a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! Form::open(['id'=>'addContact','class'=>'ajax-form hide','method'=>'POST']) !!}

                                                {!! Form::hidden('user_id', $client->id) !!}

                                                <div class="form-body">
                                                    <div class="row m-t-30">
                                                        <div class="col-md-4 ">
                                                            <div class="form-group">
                                                                <label>@lang('modules.contacts.contactName')</label>
                                                                <input type="text" name="contact_name" id="contact_name" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 ">
                                                            <div class="form-group">
                                                                <label>@lang('app.phone')</label>
                                                                <input id="phone" name="phone" type="tel" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 ">
                                                            <div class="form-group">
                                                                <label>@lang('app.email')</label>
                                                                <input id="email" name="email" type="email" class="form-control" >
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="form-actions m-t-30">
                                                    <button type="button" id="save-form" class="btn btn-success"> <i class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                                {!! Form::close() !!}

                                                <hr>
                                            </div>
                                        </div>

                                        <div class="table-responsive m-t-30">
                                            <table class="table table-bordered table-hover toggle-circle default footable-loaded footable" id="contacts-table">
                                                <thead>
                                                <tr>
                                                    <th>@lang('app.id')</th>
                                                    <th>@lang('app.name')</th>
                                                    <th>@lang('app.phone')</th>
                                                    <th>@lang('app.email')</th>
                                                    <th>@lang('app.action')</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </section>

                    </div><!-- /content -->
                </div><!-- /tabs -->
            </section>
        </div>


    </div>
    <!-- .row -->

    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="editContactModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>

<script>
    var table = $('#contacts-table').dataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.contacts.data', $client->id) !!}',
        deferRender: true,
        language: {
            "url": "<?php echo __("app.datatable") ?>"
        },
        "fnDrawCallback": function( oSettings ) {
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'contact_name', name: 'contact_name' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'action' }
        ]
    });

    $('#save-form').click(function () {
        $.easyAjax({
            url: '{{route('admin.contacts.store')}}',
            container: '#addContact',
            type: "POST",
            data: $('#addContact').serialize(),
            success: function (data) {
                if(data.status == 'success'){
                    $('#addContact').toggleClass('hide', 'show');
                    table._fnDraw();
                }
            }
        })
    });

    $('#show-add-form').click(function () {
        $('#addContact').toggleClass('hide', 'show');
    });


    $('body').on('click', '.sa-params', function(){
        var id = $(this).data('contact-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted contact!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('admin.contacts.destroy',':id') }}";
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

    $('body').on('click', '.edit-contact', function () {
        var id = $(this).data('contact-id');

        var url = '{{ route('admin.contacts.edit', ':id')}}';
        url = url.replace(':id', id);

        $('#modelHeading').html('Update Contact');
        $.ajaxModal('#editContactModal',url);

    });


</script>
@endpush