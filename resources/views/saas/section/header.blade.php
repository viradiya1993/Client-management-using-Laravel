<section class="section-hero">

    <div class="banner position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-12 text-lg-left text-center">
                    <div class="banner-text mr-0 mr-lg-5">
                        <h3 class="mb-3 mb-md-4 font-weight-bold">  {{ $frontDetail->header_title }}</h3>
                        <p>
                            {{ $frontDetail->header_description }}</p>
                        @if($frontDetail->get_started_show == 'yes')
                            <a href="{{ route('front.signup.index') }}"
                               class="btn btn-lg btn-custom mt-4 btn-outline">{{ $frontMenu->get_start }}</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 col-12 d-none d-lg-block wow zoomIn" data-wow-delay="0.4s">
                    <div class="banner-img shadow">
                        <img src="{{ $frontDetail->image_url }}" alt="business" class="shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

