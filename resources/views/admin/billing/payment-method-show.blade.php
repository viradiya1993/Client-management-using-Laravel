<style>
    .stripe-button-el{
        display: none;
    }
    .displayNone {
        display: none;
    }
    .checkbox-inline, .radio-inline {
        vertical-align: top !important;
    }
    .payment-type {
        border: 1px solid #e1e1e1;
        padding: 20px;
        background-color: #f3f3f3;
        border-radius: 10px;

    }
    .box-height {
        height: 78px;
    }
    .button-center{
        display: flex;
        justify-content: center;
    }
    .paymentMethods{display: none; transition: 0.3s;}
    .paymentMethods.show{display: block;}

    .stripePaymentForm{display: none; transition: 0.3s;}
    .stripePaymentForm.show{display: block;}
    div#card-element{
        width: 100%;
        color: #4a5568;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        line-height: 1.25;
        border-width: 1px;
        border-radius: 0.25rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-style: solid;
        border-color: #e2e8f0;
    }
</style>
<div id="event-detail">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-cash"></i> Choose Payment Method</h4>
    </div>
    <div class="modal-body">
        <div class="form-body">
            <div class="row paymentMethods show">
                <div class="col-12 col-sm-12 mt-40 text-center">
                    <div class="form-group">
                        <div class="radio-list">
                            @if(($stripeSettings->paypal_status == 'active' || $stripeSettings->stripe_status == 'active'))
                                <label class="radio-inline p-0">
                                    <div class="radio radio-info">
                                        <input checked onchange="showButton('online')" type="radio" name="method" id="radio13" value="high">
                                        <label for="radio13">@lang('modules.client.online')</label>
                                    </div>
                                </label>
                            @endif
                            @if($methods->count() > 0)
                                <label class="radio-inline">
                                    <div class="radio radio-info">
                                        <input type="radio" @if((!($stripeSettings->paypal_status == 'active') && !($stripeSettings->stripe_status == 'active'))) checked @endif onchange="showButton('offline')" name="method" id="radio15">
                                        <label for="radio15">@lang('modules.client.offline')</label>
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 mt-40 text-center" id="onlineBox">
                    @if(($stripeSettings->paypal_status == 'active' || $stripeSettings->stripe_status == 'active'))
                        <div class="form-group payment-type box-height">
                            @if($stripeSettings->paypal_client_id != null && $stripeSettings->paypal_secret != null && $stripeSettings->paypal_status == 'active')
                                <button type="submit" class="btn btn-warning waves-effect waves-light paypalPayment pull-left" data-toggle="tooltip" data-placement="top" title="Choose Plan">
                                    <i class="icon-anchor display-small"></i><span>
                                    <i class="fa fa-paypal"></i> @lang('modules.invoices.payPaypal')</span>
                                </button>
                            @endif
                            @if($stripeSettings->razorpay_key != null && $stripeSettings->razorpay_secret != null  && $stripeSettings->razorpay_status == 'active')
                                <button type="submit" class="btn btn-info waves-effect waves-light pull-left m-l-10" onclick="razorpaySubscription();" data-toggle="tooltip" data-placement="top" title="Choose Plan">
                                    <i class="icon-anchor display-small"></i><span>
                                        <i class="fa fa-credit-card-alt"></i> RazorPay </span>
                                </button>
                            @endif
                            @if($stripeSettings->api_key != null && $stripeSettings->api_secret != null  && $stripeSettings->stripe_status == 'active')
                                <button type="submit" class="btn btn-success waves-effect waves-light stripePay" data-toggle="tooltip" data-placement="top" title="Choose Plan">
                                    <i class="icon-anchor display-small"></i><span>
                                    <i class="fa fa-cc-stripe"></i> @lang('modules.invoices.payStripe')</span>
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-12 col-sm-12 mt-40 text-center">
                    @if($methods->count() > 0)
                        <div class="form-group @if(($stripeSettings->paypal_status == 'active' || $stripeSettings->stripe_status == 'active')) displayNone @endif payment-type" id="offlineBox">
                            <div class="radio-list">
                                @forelse($methods as $key => $method)
                                    <label class="radio-inline @if($key == 0) p-0 @endif">
                                        <div class="radio radio-info" >
                                            <input @if($key == 0) checked @endif onchange="showDetail('{{ $method->id }}')" type="radio" name="offlineMethod" id="offline{{$key}}"
                                                   value="{{ $method->id }}">
                                            <label for="offline{{$key}}" class="text-info" >
                                                {{ ucfirst($method->name) }} </label>
                                        </div>
                                        <div class="" id="method-desc-{{ $method->id }}">
                                            {!! $method->description !!}
                                        </div>
                                    </label>
                                @empty
                                @endforelse
                            </div>
                            <div class="row">
                                <div class="col-md-12 " id="methodDetail">
                                </div>

                                @if(count($methods) > 0)
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-info save-offline" onclick="selectOffline('{{ $package->id }}')">@lang('app.select')</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="row stripePaymentForm">
                @if($stripeSettings->api_key != null && $stripeSettings->api_secret != null  && $stripeSettings->stripe_status == 'active')
                    <div class="m-l-10">
                        <form id="stripe-form" action="{{ route('admin.payments.stripe') }}" method="POST">
                            <input type="hidden" id="name" name="name" value="{{ $user->name }}">
                            <input type="hidden" id="stripeEmail" name="stripeEmail" value="{{ $user->email }}">
                            <input type="hidden" name="plan_id" value="{{ $package->id }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                            {{ csrf_field() }}

                            <div class="flex flex-wrap mb-6">
                                <label for="card-element" class="block text-gray-700 text-sm font-bold mb-2">
                                    Card Info
                                </label>
                                <div id="card-element" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></div>
                                <div id="card-errors" class="text-red-400 text-bold mt-2 text-sm font-medium"></div>
                            </div>

                            <!-- Stripe Elements Placeholder -->
                            <div class="flex flex-wrap mt-6" style="margin-top: 15px; text-align: center">
                                <button type="submit" id="card-button" class="btn btn-success inline-block align-middle text-center select-none border font-bold whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700">
                                    <i class="fa fa-cc-stripe"></i> {{ __('Pay') }}
                                </button>
                            </div>
                        </form>

                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">Close</button>
    </div>
</div>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script src="{{ asset('pricing/js/index.js') }}"></script>
@if($stripeSettings->stripe_status == 'active')
<script>
    const stripe = Stripe('{{ config("cashier.key") }}');
    console.log(stripe);

    const elements = stripe.elements();
    const cardElement = elements.create('card');

    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;
    let validCard = false;
    const cardError = document.getElementById('card-errors');

    cardElement.addEventListener('change', function(event) {

        if (event.error) {
            validCard = false;
            cardError.textContent = event.error.message;
        } else {
            validCard = true;
            cardError.textContent = '';
        }
    });

    var form = document.getElementById('stripe-form');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        $('#card-button').attr("disabled", true);

        const { paymentMethod, error } = await stripe.createPaymentMethod(
            'card', cardElement, {
                billing_details: { name: cardHolderName.value }
            }
        );

        if (error) {
            // Display "error.message" to the user...
            console.log(error);
            $('#card-button').attr("disabled", false);
        } else {
            // The card has been verified successfully...
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', paymentMethod.id);
            form.appendChild(hiddenInput);
            form.submit();
            {{--$.easyAjax({--}}
            {{--    type:'POST',--}}
            {{--    url:'{{route('admin.payments.stripe')}}',--}}
            {{--    data: $('#stripe-form').serialize(),--}}
            {{--    redirect:true,--}}
            {{--    success:function(response){--}}
            {{--        --}}{{--window.location.href = '{{route("admin.billing")}}';--}}
            {{--    },--}}
            {{--    error:function(){--}}

            {{--    }--}}
            {{--})--}}
        }
    });

</script>
@endif
<script>
    $('.stripePay').click(function(e){
        e.preventDefault();
        $('.paymentMethods').removeClass('show');
        $('.stripePaymentForm').addClass('show');
        $('.modal-title').text('Enter Your Card Details');
    });
    // Payment mode
    function showButton(type){

        if(type == 'online'){
            $('#offlineBox').addClass('displayNone');
            $('#onlineBox').removeClass('displayNone')();
        }else{
            $('#offlineBox').removeClass('displayNone');
            $('#onlineBox').addClass('displayNone')();
        }
    }
    // redirect on paypal payment page
    $('body').on('click', '.paypalPayment', function(){
        $.easyBlockUI('#package-select-form', 'Redirecting Please Wait...');
        var url = "{{ route('admin.paypal', [$package->id, $type]) }}";
        window.location.href = url;
    });


    function selectOffline(package_id) {
        let offlineId = $("input[name=offlineMethod]").val();
        $.ajaxModal('#package-offline', '{{ route('admin.billing.offline-payment')}}'+'?package_id='+package_id+'&offlineId='+offlineId+'&type='+'{{ $type }}');
        {{--$.easyAjax({--}}
        {{--    url: '{{ route('admin.billing.offline-payment') }}',--}}
        {{--    type: "POST",--}}
        {{--    redirect: true,--}}
        {{--    data: {--}}
        {{--        package_id: package_id,--}}
        {{--        "offlineId": offlineId--}}
        {{--    }--}}
        {{--})--}}
    }
    {{--$('.save-offline').click(function() {--}}
    {{--    let offlineId = $("input[name=offlineMethod]").val();--}}

    {{--    $.easyAjax({--}}
    {{--        url: '{{ route('client.invoices.store') }}',--}}
    {{--        type: "POST",--}}
    {{--        redirect: true,--}}
    {{--        data: {invoiceId: "{{ $invoice->id }}", "_token" : "{{ csrf_token() }}", "offlineId": offlineId}--}}
    {{--    })--}}

    {{--})--}}


    //Confirmation after transaction
    function razorpaySubscription() {
        var plan_id = '{{ $package->id }}';
        var type = '{{ $type }}';
        $.easyAjax({
            type:'POST',
            url:'{{route('admin.billing.razorpay-subscription')}}',
            data: {plan_id: plan_id,type: type,_token:'{{csrf_token()}}'},
            success:function(response){
                razorpayPaymentCheckout(response.subscriprion)
           }
        })
    }


    function razorpayPaymentCheckout(subscriptionID) {
        var options = {
            "key": "{{ $stripeSettings->razorpay_key }}",
            "subscription_id":subscriptionID,
            "name": "{{$companyName}}",
            "description": "{{ $package->description }}",
            "image": "{{ $logo }}",
            "handler": function (response){
                confirmRazorpayPayment(response);
            },
            "notes": {
                "package_id": '{{ $package->id }}',
                "package_type": '{{ $type }}',
                "company_id": '{{ $company->id }}'
            },
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();
    }

    //Confirmation after transaction
    function confirmRazorpayPayment(response) {
        var plan_id = '{{ $package->id }}';
        var type = '{{ $type }}';
         var payment_id = response.razorpay_payment_id;
         var subscription_id = response.razorpay_subscription_id;
         var razorpay_signature = response.razorpay_signature;
//         console.log([plan_id, type, payment_id, subscription_id, razorpay_signature]);
        $.easyAjax({
            type:'POST',
            url:'{{route('admin.billing.razorpay-payment')}}',
            data: {paymentId: payment_id,plan_id: plan_id,subscription_id: subscription_id,type: type,razorpay_signature: razorpay_signature,_token:'{{csrf_token()}}'},
            redirect:true,
        })
    }
</script>

