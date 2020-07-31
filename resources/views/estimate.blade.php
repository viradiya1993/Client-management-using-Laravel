<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">

    <title>Client Panel | {{ $pageTitle }}</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css'>

    <!-- This is Sidebar menu CSS -->
    <link href="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/bower_components/toast-master/css/jquery.toast.css') }}"   rel="stylesheet">
    <link href="{{ asset('plugins/bower_components/sweetalert/sweetalert.css') }}"   rel="stylesheet">

    <!-- This is a Animation CSS -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">

<!-- This is a Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- color CSS you can use different color css from css/colors folder -->
    <!-- We have chosen the skin-blue (default.css) for this starter
       page. However, you can choose any other skin from folder css / colors .
       -->
    <link href="{{ asset('css/colors/default.css') }}" id="theme"  rel="stylesheet">
    <link href="{{ asset('plugins/froiden-helper/helper.css') }}"   rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}"   rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .sidebar .notify  {
            margin: 0 !important;
        }
        .sidebar .notify .heartbit {
            top: -23px !important;
            right: -15px !important;
        }
        .sidebar .notify .point {
            top: -13px !important;
        }
        .top-notifications .message-center .user-img{
            margin: 0 0 0 0 !important;
        }
        .wrapper {
            position: relative;
            width: 100%;
            height: 250px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 0;
            width:100%;
            height: 100%;
            background-color: white;
        }
    </style>
</head>
<body class="fix-sidebar">
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">

    <!-- Left navbar-header end -->
    <!-- Page Content -->
    <div id="page-wrapper" style="margin-left: 0px !important;">
        <div class="container-fluid">

            <!-- .row -->
            <div class="row" style="margin-top: 70px; !important;">

                <div class="col-md-offset-2 col-md-8 col-md-offset-2" id="estimates">
                    <div class="row m-b-20">
                        <div class="col-md-12">
                            <div class="visible-xs">
                                <div class="clearfix"></div>
                            </div>
                            @if($estimate->status == 'waiting')
                            <button type="button" id="accept_action" class="btn btn-success pull-right m-r-10" onclick="accept();return false;"><i class="fa fa-check"></i> @lang('app.accept')</button>
                            <button type="submit" class="btn btn-danger pull-right m-r-10" onclick="decline();return false;"><i class="fa fa-remove"></i> @lang('app.decline')</button>

                            @elseif($estimate->status == 'accepted')
                                <a href="javascript:;" class="btn btn-success pull-right m-r-10"><i class="fa fa-check"></i> @lang('app.signed')</a>
                            @endif
                            <a href="{{ route("front.estimateDownload", md5($estimate->id)) }}" class="btn btn-default pull-right m-r-10"><i class="fa fa-file-pdf-o"></i> Download</a>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="white-box printableArea ribbon-wrapper" style="background: #ffffff !important;">
                                <div class="ribbon-content " id="invoice_container">
                                    @if($estimate->status == 'waiting')
                                        <div class="ribbon ribbon-bookmark ribbon-warning">@lang('modules.estimates.waiting')</div>
                                    @endif
                                    @if($estimate->status == 'declined')
                                        <div class="ribbon ribbon-bookmark ribbon-danger">@lang('modules.estimates.declined')</div>
                                    @endif

                                    @if($estimate->status == 'accepted')
                                        <div class="ribbon ribbon-bookmark ribbon-success">@lang('modules.estimates.accepted')</div>
                                    @endif

                                    <h3><b>@lang('app.estimate')</b> <span class="pull-right">{{ $estimate->estimate_number }}</span></h3>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="pull-left">
                                                <address>
                                                    <h3> &nbsp;<b class="text-danger">{{ ucwords($global->company_name) }}</b></h3>
                                                    @if(!is_null($settings))
                                                        <p class="text-muted m-l-5">{!! nl2br($global->address) !!}</p>
                                                    @endif
                                                </address>
                                            </div>
                                            <div class="pull-right text-right">
                                                <address>
                                                    @if(!is_null($estimate->client))
                                                        <h3>@lang('app.to'),</h3>
                                                        <h4 class="font-bold">{{ ucwords($estimate->client->name) }}</h4>

                                                        @if(!is_null($estimate->client))
                                                            <p class="text-muted m-l-30">{!! nl2br($estimate->client_details->address) !!}</p>
                                                        @endif
                                                    @endif
                                                    <p class="m-t-30"><b>@lang('modules.estimates.validTill') :</b> <i
                                                                class="fa fa-calendar"></i> {{ $estimate->valid_till->format($settings->date_format) }}
                                                    </p>
                                                </address>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive m-t-40" style="clear: both;">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th>@lang("modules.invoices.item")</th>
                                                        <th class="text-right">@lang("modules.invoices.qty")</th>
                                                        <th class="text-right">@lang("modules.invoices.unitPrice")</th>
                                                        <th class="text-right">@lang("modules.invoices.price")</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $count = 0; ?>
                                                    @foreach($estimate->items as $item)
                                                        @if($item->type == 'item')
                                                            <tr>
                                                                <td class="text-center">{{ ++$count }}</td>
                                                                <td>{{ ucfirst($item->item_name) }}
                                                                    @if(!is_null($item->item_summary))
                                                                        <p class="font-12">{{ $item->item_summary }}</p>
                                                                    @endif
                                                                </td>
                                                                <td class="text-right">{{ $item->quantity }}</td>
                                                                <td class="text-right"> {!! htmlentities($estimate->currency->currency_symbol)  !!}{{ $item->unit_price }} </td>
                                                                <td class="text-right"> {!! htmlentities($estimate->currency->currency_symbol)  !!}{{ $item->amount }} </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="pull-right m-t-30 text-right">
                                                <p>@lang("modules.invoices.subTotal")
                                                    : {!! htmlentities($estimate->currency->currency_symbol)  !!}{{ $estimate->sub_total }}</p>

                                                <p>@lang("modules.invoices.discount")
                                                    : {!! htmlentities($estimate->currency->currency_symbol)  !!}{{ $discount }} </p>
                                                @foreach($taxes as $key=>$tax)
                                                    <p>{{ strtoupper($key) }}
                                                        : {!! htmlentities($estimate->currency->currency_symbol)  !!}{{ $tax }} </p>
                                                @endforeach
                                                <hr>
                                                <h3><b>@lang("modules.invoices.total")
                                                        :</b> {!! htmlentities($estimate->currency->currency_symbol)  !!}{{ $estimate->total }}
                                                </h3>
                                            </div>

                                            @if(!is_null($estimate->note))
                                                <div class="col-md-12">
                                                    <p><strong>@lang('app.note')</strong>: {{ $estimate->note }}</p>
                                                </div>
                                            @endif
                                            <div class="clearfix"></div>
                                            <hr>
                                            <div>
                                                <div class="col-md-12">
                                                    <span><p class="displayNone" id="methodDetail"></p></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->


    {{--Timer Modal--}}
    <div class="modal fade bs-modal-md in" id="estimateAccept" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <button type="button" class="btn blue">@lang('app.accept')</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{--Timer Modal Ends--}}
</div>
<!-- /#wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>

<!-- Sidebar menu plugin JavaScript -->
<script src="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
<!--Slimscroll JavaScript For custom scroll-->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('js/waves.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('plugins/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.init.js') }}"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

<script>
    //Decline estimate
    function decline() {
        $.easyAjax({
            type:'POST',
            url:'{{route('front.estimate.decline', $estimate->id)}}',
            container:'#estimates',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response){
                if(response.status == 'success') {
                    window.location.reload();
                }
            }
        })
    }

    //Accept estimate
    function accept() {
        var url = '{{ route('front.estimate.accept', $estimate->id) }}';
        $.ajaxModal('#estimateAccept', url);
    }
</script>

</body>
</html>
