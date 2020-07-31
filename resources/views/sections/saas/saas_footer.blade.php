<footer class="bg-white footer">
    <div class="container">
        <div class="footer-top border-bottom">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 mb-30">
                    <div class="f-contact-detail">
                        <i class="flaticon-email"></i>
                        <h5>@lang('app.email')</h5>
                        <p class="mb-0">{{ $frontDetail->email }}</p>
                    </div>
                </div>
                @if($frontDetail->phone)
                    <div class="col-lg-4 col-md-6 col-12 mb-30">
                        <div class="f-contact-detail">
                            <i class="flaticon-call"></i>
                            <h5>@lang('app.phone')</h5>
                            <p class="mb-0">{{ $frontDetail->phone }}</p>
                        </div>
                    </div>
                @endif
                <div class="col-lg-4 col-md-6 col-12 mb-30">
                    <div class="f-contact-detail">
                        <i class="flaticon-placeholder"></i>
                        <h5>@lang('app.address')</h5>
                        <p class="mb-0">{{ $frontDetail->address }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright py-4">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-lg-4 col-md-6">
                    <p class="mb-0">{{ ucwords($frontDetail->footer_copyright_text) }} </p>
                </div>
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="col-12 col-lg-6">
                        @php $routeName = request()->route()->getName(); @endphp
                        <ul class="nav nav-primary nav-hero">
                            @forelse($footerSettings as $footerSetting)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('front.page', $footerSetting->slug) }}" >{{ $footerSetting->name }}</a>
                                </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-12 d-md-flex align-items-center">
                    <div class="form-group d-inline-block mr-20 my-2">
                        <select class="form-control" onchange="location = this.value;">
                            <option value="{{ route('front.language.lang', 'en') }}" @if($locale == 'en') selected @endif>English </option>
                            @forelse($languages as $language)
                                <option value="{{ route('front.language.lang', $language->language_code) }}"  @if($locale == $language->language_code) selected @endif>{{ $language->language_name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="socials text-right">
                        @if($frontDetail->social_links)
                            @forelse (json_decode($frontDetail->social_links,true) as $link)
                                @if (strlen($link['link']) > 0)
                                    <a href="{{ $link['link'] }}" target="_blank">
                                        <i class="zmdi zmdi-{{$link['name']}}"></i>
                                    </a>
                                @endif
                            @empty
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>