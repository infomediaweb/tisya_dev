@extends('layout.main')
@section('content')
<section class="section section-why">
    <div class="container">
        <div class="section-heading">
            <h1 class="fs-2">Frequently Asked Questions</h1> 
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="faqs">
            <ul class="list-unstyled mb-0">
                @foreach($faqs as $index => $faq)
                   <li class="{{ $index == 0 ? 'active' : '' }}">
                    <h3 class="faq-title" role="button">{{ $faq->question }}</h3>
                   <div class="faq-content" style="display: {{ $index == 0 ? 'block' : 'none' }};">
                       <p>{{ $faq->answer }}</p>
                         </div>
                    </li>
                @endforeach
            </ul>
        </div>
            </div>
        </div>
    </div>
</section>

@endsection  