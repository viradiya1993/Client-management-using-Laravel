@extends('layouts.sass-app')
@section('content')

    <!-- START Saas Features -->
    <section class="border-bottom bg-white sp-100 pb-3 overflow-hidden">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-60">
                        <h3>{{ $frontDetail->task_management_title }}</h3>
                        <p>{{ $frontDetail->task_management_detail }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @forelse($featureTasks as $featureTask)
                    <div class="col-md-4 col-sm-6 col-12 mb-60">
                        <div class="saas-f-box">
                            <div class="icon">
                                <i class="{{ $featureTask->icon }}"></i>
                            </div>
                            <h5>{{ $featureTask->title }}</h5>
                            <p class="mb-0">{!!  $featureTask->description !!} </p>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>

    <section class="border-bottom bg-white sp-100 pb-3 overflow-hidden">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-60">
                        <h3>{{ $frontDetail->manage_bills_title }}</h3>
                        <p>{{ $frontDetail->manage_bills_detail }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @forelse($featureBills as $featureBill)
                    <div class="col-md-4 col-sm-6 col-12 mb-60">
                        <div class="saas-f-box">
                            <div class="icon">
                                <i class="{{ $featureBill->icon }}"></i>
                            </div>
                            <h5>{{ $featureBill->title }}</h5>
                            <p class="mb-0">{!!  $featureBill->description !!} </p>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>
    <!-- END Saas Features -->


    <!-- START SAAS Features -->
    <section class="sp-100-40 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-60">
                        <h3>{{ $frontDetail->teamates_title }}</h3>
                        <p>{{ $frontDetail->teamates_detail }}</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center wow fadeIn" data-wow-delay="0.4s">
                @forelse($featureTeams as $featureTeam)
                    <div class="col-lg-4 col-md-6 col-12 mb-60">
                        <div class="saas-f-box text-center">
                            <div class="icon mx-auto">
                                <i class="{{ $featureTeam->icon }}"></i>
                            </div>
                            <h5>{{ $featureTeam->title }}</h5>
                            <p class="mb-0">{!!  $featureTeam->description !!} </p>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>
    <!-- END SAAS Features -->

    <!-- START Clients Section -->
    @include('saas.section.client')
    <!-- END Clients Section -->

    <!-- START Integration Section -->
    <section class="sp-100-70 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-60">
                        <h3>{{ $frontDetail->favourite_apps_title }}</h3>
                        <p>{{ $frontDetail->favourite_apps_detail }}</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                @forelse($featureApps as $index => $featureApp)
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-30 wow fadeIn" data-wow-delay="0.4s">
                        <div class="integrate-box shadow">
                            <img src="{{ $featureApp->image_url }}"   alt="{{ $featureBill->title }}">
                            <h5 class="mb-0">{{ ucfirst($featureApp->title) }} </h5>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>
    <!-- END Integration Section -->


@endsection
@push('footer-script')

@endpush
