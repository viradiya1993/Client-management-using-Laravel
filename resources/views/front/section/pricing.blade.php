<section class="section" id="section-pricing">
    <div class="container">

        <header class="section-header">
            <h2>{{ $detail->price_title }}</h2>
            <hr>
            <p class="lead">{{ $detail->price_description }}</p>
        </header>


        <div class="text-center mb-70">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-round btn-outline btn-dark w-150 active">
                    <input type="radio" onchange="planShow('monthly')" name="pricing" value="monthly" autocomplete="off" checked> @lang('app.monthly')
                </label>
                <label class="btn btn-round btn-outline btn-dark w-150">
                    <input type="radio" onchange="planShow('yearly')" name="pricing" value="yearly" autocomplete="off"> @lang('app.annual')
                </label>
            </div>
        </div>

        <section class="pricing-section-2 text-center monthly-packages" id="monthlyPlan">
            <div class="container container-scroll">
                <div class="row @if(count($packages) > 5) flex-nowrap @else justify-content-center @endif">

                    <div class="col-md-2 pick-plan">
                            <div class="pricing pricing-3">
                                <div class="pricing__head boxed planNameTitle" >
                                    <h3>@lang('modules.frontCms.pickPlan')</h3>
                                </div>

                                <ul>
                                    <li>@lang('app.max') @lang('app.menu.employees')</li>
                                    @foreach($packageFeatures as $packageFeature)
                                        <li>
                                            <span>{{ __('modules.module.'.$packageFeature) }}</span>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    @foreach ($packages as $item)
                        <div class="col-md-2 ">
                            <div class="pricing pricing-3">
                                @if($item->recommended)
                                    <div class="pricing__head bg--primary boxed background-color"> <span class="label">@lang('app.recommended')</span>
                                        <h5>{{ ucwords($item->name) }}</h5> <span class="h1">{{ $item->formatted_monthly_price }}</span>
                                        <p class="type--fine-print">@lang('modules.frontCms.perMonth'), {{ $global->currency->currency_code }}.</p>
                                    </div>
                                @else
                                    <div class="pricing__head bg--secondary boxed planNameHead">
                                        <h5>{{ ucwords($item->name) }}</h5> <span class="h4">{{ $item->formatted_monthly_price }}</span>
                                        <p class="type--fine-print">@lang('modules.frontCms.perMonth'), {{ $global->currency->currency_code }}.</p>
                                    </div>
                                @endif
                                <ul>
                                    <li>{{ $item->max_employees }} &nbsp;</li>
                                    @php
                                        $packageModules = (array)json_decode($item->module_in_package);
                                    @endphp
                                    @foreach($packageFeatures as $packageFeature)
                                        <li>
                                            @if(in_array($packageFeature, $packageModules))
                                                <i class="fa fa-check-circle module-available"></i>
                                            @endif
                                            &nbsp;
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>
        </section>
        <section class="pricing-section-2 text-center annual-packages" style="display: none;" id="annualPlan">
            <div class="container container-scroll">
                <div class="row @if(count($packages) > 5) flex-nowrap @else justify-content-center @endif">

                    <div class="col-md-2 pick-plan">
                            <div class="pricing pricing-3">
                                <div class="pricing__head boxed planNameTitle" >
                                    <h3>@lang('modules.frontCms.pickPlan')</h3>
                                </div>

                                <ul>
                                    <li>@lang('app.max') @lang('app.menu.employees')</li>
                                    @foreach($packageFeatures as $packageFeature)
                                        <li>
                                            <span>{{ __('modules.module.'.$packageFeature) }}</span>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>

                    @foreach ($packages as $item)
                        <div class="col-md-2 " >
                            <div class="pricing pricing-3">
                                @if($item->recommended)
                                    <div class="pricing__head bg--primary boxed background-color"> <span class="label">@lang('app.recommended')</span>
                                        <h5>{{ ucwords($item->name) }}</h5> <span class="h1">{{ $item->formatted_annual_price }}</span>
                                        <p class="type--fine-print">@lang('modules.frontCms.perYear'), {{ $global->currency->currency_code }}.</p>
                                    </div>
                                @else
                                    <div class="pricing__head bg--secondary boxed planNameHead">
                                        <h5>{{ ucwords($item->name) }}</h5> <span class="h4">{{ $item->formatted_annual_price }}</span>
                                        <p class="type--fine-print">@lang('modules.frontCms.perYear'), {{ $global->currency->currency_code }}.</p>
                                    </div>
                                @endif
                                <ul>
                                    <li>{{ $item->max_employees }} &nbsp;</li>
                                    @php
                                        $packageModules = (array)json_decode($item->module_in_package);
                                    @endphp
                                    @foreach($packageFeatures as $packageFeature)
                                        <li>
                                            @if(in_array($packageFeature, $packageModules))
                                                <i class="fa fa-check-circle module-available"></i>
                                            @endif
                                            &nbsp;
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach



                </div>
            </div>
        </section>


    </div>
</section>