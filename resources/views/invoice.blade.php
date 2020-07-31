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

@stack('head-script')

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

                <div class="col-md-offset-2 col-md-8 col-md-offset-2">
                    <div class="row m-b-20">
                        <div class="col-md-12">
                            <a href="{{ route("front.invoiceDownload", md5($invoice->id)) }}" class="btn btn-default pull-right m-r-10"><i class="fa fa-file-pdf-o"></i> Download</a>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <i class="fa fa-check"></i> {!! $message !!}
                        </div>
                        <?php Session::forget('success');?>
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="custom-alerts alert alert-danger fade in">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            {!! $message !!}
                        </div>
                        <?php Session::forget('error');?>
                    @endif


                    <div class="white-box printableArea ribbon-wrapper" style="background: #ffffff !important;">
                        <div class="ribbon-content " id="invoice_container">
                            @if($invoice->status == 'paid')
                                <div class="ribbon ribbon-bookmark ribbon-success">@lang('modules.invoices.paid')</div>
                            @elseif($invoice->status == 'partial')
                                <div class="ribbon ribbon-bookmark ribbon-info">@lang('modules.invoices.partial')</div>
                            @else
                                <div class="ribbon ribbon-bookmark ribbon-danger">@lang('modules.invoices.unpaid')</div>
                            @endif

                            <h3><b>@lang('app.invoice')</b> <span class="pull-right">{{ $invoice->invoice_number }}</span></h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="pull-left">
                                        <address>
                                            <h3> &nbsp;<b class="text-danger">{{ ucwords($global->company_name) }}</b></h3>
                                            @if(!is_null($settings))
                                                <p class="text-muted m-l-5">{!! nl2br($global->address) !!}</p>
                                            @endif
                                            @if($invoiceSetting->show_gst == 'yes' && !is_null($invoiceSetting->gst_number))
                                                <p class="text-muted m-l-5"><b>@lang('app.gstIn')
                                                        :</b>{{ $invoiceSetting->gst_number }}</p>
                                            @endif
                                        </address>
                                    </div>
                                    <div class="pull-right text-right">
                                        <address>
                                            @if(!is_null($invoice->project) && !is_null($invoice->project->client))
                                                <h3>To,</h3>
                                                <h4 class="font-bold">{{ ucwords($invoice->project->client->name) }}</h4>

                                                <p class="text-muted m-l-30">{!! nl2br($invoice->project->client->address) !!}</p>
                                                @if($invoiceSetting->show_gst == 'yes' && !is_null($invoice->project->client->gst_number))
                                                    <p class="m-t-5"><b>@lang('app.gstIn')
                                                            :</b>  {{ $invoice->project->client->gst_number }}
                                                    </p>
                                                @endif
                                            @endif

                                            <p class="m-t-30"><b>@lang('modules.invoices.invoiceDate') :</b> <i
                                                        class="fa fa-calendar"></i> {{ $invoice->issue_date->format($global->date_format) }}
                                            </p>

                                            <p><b>@lang('modules.dashboard.dueDate') :</b> <i
                                                        class="fa fa-calendar"></i> {{ $invoice->due_date->format($global->date_format) }}
                                            </p>
                                            @if($invoice->recurring == 'yes')
                                                <p><b class="text-danger">@lang('modules.invoices.billingFrequency') : </b> {{ $invoice->billing_interval . ' '. ucfirst($invoice->billing_frequency) }} ({{ ucfirst($invoice->billing_cycle) }} cycles)</p>
                                            @endif
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
                                            @foreach($invoice->items as $item)
                                                @if($item->type == 'item')
                                                    <tr>
                                                        <td class="text-center">{{ ++$count }}</td>
                                                        <td>{{ ucfirst($item->item_name) }}
                                                            @if(!is_null($item->item_summary))
                                                                <p class="font-12">{{ $item->item_summary }}</p>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">{{ $item->quantity }}</td>
                                                        <td class="text-right"> {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $item->unit_price }} </td>
                                                        <td class="text-right"> {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $item->amount }} </td>
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
                                            : {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $invoice->sub_total }}</p>

                                        <p>@lang("modules.invoices.discount")
                                            : {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $discount }} </p>
                                        @foreach($taxes as $key=>$tax)
                                            <p>{{ strtoupper($key) }}
                                                : {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $tax }} </p>
                                        @endforeach
                                        <hr>
                                        <h3><b>@lang("modules.invoices.total")
                                                :</b> {!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $invoice->total }}
                                        </h3>
                                        <hr>
                                        @if ($invoice->credit_notes()->count() > 0)
                                            <p>
                                                @lang('modules.invoices.appliedCredits'): {{ $invoice->currency->currency_symbol.''.$invoice->appliedCredits() }}
                                            </p>
                                        @endif
                                        <p class="@if ($invoice->amountDue() > 0) text-danger @endif">
                                            @lang('modules.invoices.amountDue'): {{ $invoice->currency->currency_symbol.''.$invoice->amountDue() }}
                                        </p>
                                    </div>

                                    @if(!is_null($invoice->note))
                                        <div class="col-md-12">
                                            <p><strong>@lang('app.note')</strong>: {{ $invoice->note }}</p>
                                        </div>
                                    @endif
                                    <div class="clearfix"></div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 text-left">
                                            {{--<div class="clearfix"></div>--}}
                                            <div class="col-md-12 p-l-0 text-left">
                                                @if($invoice->status == 'unpaid' && ($credentials->paypal_status == 'active' || $credentials->stripe_status == 'active'))

                                                    <div class="btn-group" id="onlineBox">
                                                        <div class="dropup">
                                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                @lang('modules.invoices.payNow') <span class="caret"></span>
                                                            </button>
                                                            <ul role="menu" class="dropdown-menu">

                                                                @if($credentials->paypal_status == 'active')
                                                                    <li>
                                                                        <a href="{{ route('client.paypal-public', [$invoice->id]) }}"><i
                                                                                    class="fa fa-paypal"></i> @lang('modules.invoices.payPaypal') </a>
                                                                    </li>
                                                                @endif

                                                                @if($credentials->stripe_status == 'active')
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <a href="javascript:void(0);" id="stripePaymentButton"><i
                                                                                    class="fa fa-cc-stripe"></i> @lang('modules.invoices.payStripe') </a>
                                                                    </li>
                                                                @endif

                                                                @if($credentials->razorpay_status == 'active')
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <a href="javascript:void(0);" id="razorpayPaymentButton"><i
                                                                                    class="fa fa-cc-stripe"></i>  @lang('modules.invoices.payRazorpay')  </a>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>

                                                    </div>
                                                @endif
                                            </div>


                                        </div>
                                        <div class="col-md-6 text-right">

                                        </div>
                                    </div>
                                    <div>
                                        <div class="col-md-12">
                                            <span><p class="displayNone" id="methodDetail"></p></span>
                                        </div>
                                    </div>
                                </div>
                                @if(count($invoice->payment) > 0)
                                    <div class="col-md-12">
                                        <h3>@lang("modules.invoices.paymentDetails")</h3>
                                        <hr>
                                        <div class="table-responsive m-t-40" style="clear: both;">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th class="text-center"><b>#</b></th>
                                                    <th class="text-right"><b>@lang("modules.invoices.amount")</b></th>
                                                    <th><b>@lang("modules.invoices.paymentGateway")</b></th>
                                                    <th><b>@lang("modules.invoices.transactionID")</b></th>
                                                    <th><b>@lang("modules.invoices.paidOn")</b></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $count = 0; ?>
                                                @forelse($invoice->payment as $payment)
                                                    <tr>
                                                        <td class="text-center">{{ $count=$count+1 }}</td>
                                                        <td class="text-right">{!! htmlentities($invoice->currency->currency_symbol)  !!}{{ $payment->amount }}</td>
                                                        <td>{{ htmlentities($payment->gateway)  }}</td>
                                                        <td>{{ $payment->transaction_id }}</td>
                                                        <td>@if(!is_null($payment->paid_on)) {{ $payment->paid_on->format($global->date_format.' '.$global->time_format) }} @endif</td>
                                                    </tr>
                                                    @if($payment->remarks)
                                                        <tr><td colspan="5"><b>@lang("modules.invoices.remark")</b> : {!! $payment->remarks !!}</td></tr>
                                                    @endif
                                                @empty
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
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
</div>
<!-- /#wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js'></script>

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
<script src="https://checkout.stripe.com/checkout.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
            @if($credentials->stripe_status == 'active')
    var handler = StripeCheckout.configure({
            key: '{{ $credentials->stripe_client_id }}',
            image: '{{ $global->logo_url }}',
            locale: 'auto',
            token: function(token) {
                // You can access the token ID with `token.id`.
                // Get the token ID to your server-side code for use.
                $.easyAjax({
                    url: '{{route('client.stripe-public', [$invoice->id])}}',
                    container: '#invoice_container',
                    type: "POST",
                    redirect: true,
                    data: {token: token, "_token" : "{{ csrf_token() }}"}
                })
            }
        });

    document.getElementById('stripePaymentButton').addEventListener('click', function(e) {
        // Open Checkout with further options:
        handler.open({
            name: '{{ $companyName }}',
            amount: {{ $invoice->total*100 }},
            currency: '{{ $invoice->currency->currency_code }}',
            email: ""

        });
        e.preventDefault();
    });

    // Close Checkout on page navigation:
    window.addEventListener('popstate', function() {
        handler.close();
    });



    @endif

    @if($credentials->razorpay_status == 'active')
        $('#razorpayPaymentButton').click(function() {
            console.log('{{ $invoice->currency->currency_code }}');
            var amount = {{ $invoice->total*100 }};
            var invoiceId = {{ $invoice->id }};
            var clientEmail = "";

            var options = {
                "key": "{{ $credentials->razorpay_key }}",
                "amount": amount,
                "currency": 'INR',
                "name": "{{ $companyName }}",
                "description": "Invoice Payment",
                "image": "{{ $global->logo_url }}",
                "handler": function (response) {
                    confirmRazorpayPayment(response.razorpay_payment_id,invoiceId,response);
                },
                "modal": {
                    "ondismiss": function () {
                        // On dismiss event
                    }
                },
                "prefill": {
                    "email": clientEmail
                },
                "notes": {
                    "purchase_id": invoiceId //invoice ID
                }
            };
            var rzp1 = new Razorpay(options);

            rzp1.open();

        })

        //Confirmation after transaction
        function confirmRazorpayPayment(id,invoiceId,rData) {
            $.easyAjax({
                type:'POST',
                url:'{{route('public.pay-with-razorpay')}}',
                data: {paymentId: id,invoiceId: invoiceId,rData: rData,_token:'{{csrf_token()}}'}
            })
        }

    @endif

    // Show offline method detail
    function showDetail(id){
        var detail = $('#method-desc-'+id).html();
        $('#methodDetail').html(detail);
        $('#methodDetail').show();
    }
</script>

</body>
</html>