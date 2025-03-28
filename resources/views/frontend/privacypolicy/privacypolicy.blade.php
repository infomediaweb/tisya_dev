@extends('layout.main')

@section('content')
<section class="section">
    <div class="container">
        @foreach ($privacypolicyies as $privacypolicy)
            <div class="section-heading">
                <h1 class="fs-2">{!! $privacypolicy->title ?? '' !!}</h1>
            </div>
            <div class="content">
                <p>{!! $privacypolicy->cancellation_policy ?? '' !!}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
