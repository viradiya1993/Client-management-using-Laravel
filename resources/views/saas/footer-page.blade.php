@extends('layouts.sass-app')
@section('content')
    <section class="pricing-section bg-white sp-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-title mb-60">
                        <h3>{{ ucwords($slugData->name) }}</h3>
                        <p>{!! $slugData->description !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('footer-script')

@endpush
