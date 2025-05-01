    
@extends('components.layouts.app')
@section('content')

<section class="section section-hero page-hero innerpage-hero pb-0 bg-primary-light">
    <div class="swiper hero-slides">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="hero-slide-img" style="background-image: url(./assets/images/team-hero.webp);"></div>
            </div>
        </div>
    </div>
    <div class="container animFade">
        <div class="hero-content text-xl-center  flex-grow-1">
            <div class="hero-title">
                <h1>Delivering an <em>Unparalleled Home Experience</em></h1>    
            </div>         
        </div>       
    </div>
</section>

    <section class="section pt-3 pt-lg-5 pb-5">
        <div class="container">
            <div class="content text-secondary">
                <div class="row justify-content-center text-lg-center">
                    <div class="col-12 col-lg-9">
                        <p>As an emerging group of professional homemakers, every member of our team is handpicked, and mostly not from a typical hospitality background. We are all united by our common passion: delivering an unparalleled home experience.</p>
                        <p>The joy received from a relaxing holiday, the excitement attained from spending time with your loved ones, the pleasure relished from a comfortable home; these are all feelings we are familiar with and strive to provide for our guests.  </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="swiper team-slider">
                <div class="swiper-wrapper">
                    @foreach($teams as $team)
                    <div class="swiper-slide">
                        <a href="javascript:void(0)" data-fancybox data-src="#team{{ $team->id }}" class="image-card">
                            <div class="ic-img">
                                <img src="{{ asset('storage/teams/' . $team->image) }}" alt="{{ $team->name }}">
                            </div>
                            <div class="ic-info">   
                                <h3>{{ $team->name }}</h3>
                                <p>{{ $team->designation }}</p>
                            </div>
                        </a>
                        <div id="team{{ $team->id }}" class="team-popup popup">
                             <div class="ic-info mb-4 pb-2">
                                <h3>{{ $team->name }}</h3>
                                <p>{{ $team->designation }}</p>
                             </div>
                             <div class="row gy-4 gx-5">
                                 <div class="col-12 col-md-auto">
                                     <img src="{{ asset('storage/teams/' . $team->image) }}" alt="{{ $team->name }}">
                                 </div>
                                 <div class="col-12 col-md">
                                    <div class="content">
                                        <p>{{ $team->description }}</p>
                                    </div>
                                 </div>
                             </div>
                        </div>
                    </div>
                    @endforeach
                </div>
    
                <div class="container px-0">
                    <div class="swiper-track team-slide-track mx-n3 px-3 mx-lg-0 px-lg-0">
                        <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
                        <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
                    </div>
                </div>
            </div>
        </div>   
    </section>
    


@endsection