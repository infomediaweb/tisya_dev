
@extends('layout.main')
@section('content')
<section class="section about-us">
    <div class="container">        
        <div class="row g-5 align-items-center">
            <div class="col-12 col-lg-6">
                <div class="section-heading">
                    <h1 class="fs-2">About Us</h1> 
                </div>
                
                  @foreach ($aboutus as $about)
                <div class="content">
                <p>{!! $about->about_content ?? '' !!}</p>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                 <div class="p-4 rounded-4 bg-white shadow overflow-hidden">
                    <img class="rounded-4 w-100"  src="{{ asset('storage/about_us/'.  $about->banner ?? 'assets/images/d1.jpg') }}" alt="">
                 </div>   
            </div>
             @endforeach
        </div>
    </div>
</section>
@endsection