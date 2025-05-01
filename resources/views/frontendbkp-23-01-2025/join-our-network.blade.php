
@extends('components.layouts.app')
@section('content')

@foreach ($intro as $intro)

<section class="section section-hero page-hero innerpage-hero pb-xl-0 bg-primary-light">
    <div class="swiper hero-slides">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="hero-slide-img" style="background-image: url(./assets/images/hero-2.webp);"></div>
            </div>
        </div>
    </div>
    <div class="container animFade">
        <div class="hero-content text-xl-center  flex-grow-1">
            <div class="hero-title">
                <h1>Home <em>Owners</em></h1>
            </div>
        </div>
    </div>
</section>

<section class="section section-home-owner bg-secondary-light2">
    <div class="container">
        <div class="title text-center text-secondary animFade">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <h2 class="h3 text-black">{!!$intro->introduction !!}</h2>
<p></p>
{{-- <p>If you are keen to know more about joining the V are Family vacation home network, please call us at +91-9810138738 or leave your contact information here and we will contact you as soon as possible. We can present you with the financial, and incidental, benefits of our concept.</p> --}}
{{-- <p>There are some exciting benefits in store for you within the V are Family vacation home network, including:</p> --}}
 </div>
            </div>
        </div>
    </div>
    <div class="container-fluid px-0">
        <div class="home-owner-cards">
            <div class="row gy-4">
                <div class="col-12 col-md-4 animFade">
                    <div class="card card-primary">
                        <div class="card-img-top">
                            <span>
                                <img loading="lazy" src="{{ asset('storage/join_our_network_intro/' . $intro->image1) }}" alt="Sustained Income Generation">
                            </span>
                        </div>
                        <div class="card-body pb-0">
                            <h3>{{$intro->title1}}</h3>
                            <p>{{$intro->description1}}</p></div>
                    </div>
                </div>
                <div class="col-12 col-md-4 animFade">
                    <div class="card card-primary">
                        <div class="card-img-top">
                            <span>
                                <img loading="lazy" src="{{ asset('storage/join_our_network_intro/' . $intro->image2) }}" alt="Maintenance of Your Home">
                            </span>
                        </div>
                        <div class="card-body pb-0">
                            <h3>{{$intro->title2}}</h3>
                            <p>{{$intro->description2}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 animFade">
                    <div class="card card-primary">
                        <div class="card-img-top">
                            <span>
                                <img loading="lazy" src="{{ asset('storage/join_our_network_intro/' . $intro->image3) }}" alt="Flexibility of Your Use">
                            </span>
                        </div>
                        <div class="card-body pb-0">
                            <h3>{{$intro->title3}}</h3>
                            <p>{{$intro->description3}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@endforeach

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 text-center">
                <div class="title text-secondary animFade">
                    <h2 class="h3 text-black">Please leave your contact information here and we will contact you as soon as possible.</h2>
                </div>
            </div>
            <div class="col-12 col-lg-10">
                <form action="">
                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <label for="Name">Name <span>*</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="Name">Email <span>*</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="Name">Phone <span>*</span></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="Name">Message <span>*</span></label>
                            <textarea name="" id="" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-secondary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


    <section class="section section-home-owner bg-secondary-light2">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="title text-secondary animFade">
                        <h2 class="h3 text-black">Frequently Asked Questions</h2>
                    </div>
                </div>
                <div class="col-12">
                    <ul class="accordionbox">
                        @foreach($faqs as $faq)
                        <li>
                            <h4>{{$faq->question}}</h4>
                            <div class="content">{{$faq->answer}}</div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>





@endsection
