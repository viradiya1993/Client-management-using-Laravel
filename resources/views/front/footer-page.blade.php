@extends('layouts.front-app')
@section('content')
    <section class="section" style="min-height: 450px;">
        <div class="container">
            <div class="row gap-y">
                <div class="col-12 d-flex justify-content-center ">
                    <h3>{{ ucwords($slugData->name) }}</h3>
                </div>

                <div class="col-12">
                    <p>{!! $slugData->description !!}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
