    
@extends('components.layouts.app')
@section('content')

<section class="section section-hero page-hero innerpage-hero pb-0 bg-primary-light">
    <div class="swiper hero-slides">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="hero-slide-img" style="background-image: url(./assets/images/hero-3.webp);"></div>
            </div>
        </div>
    </div>
    <div class="container animFade">
        <div class="hero-content text-xl-center  flex-grow-1">
            <div class="hero-title">
                <h1>Contact Us</h1>    
            </div>         
        </div>       
    </div>
</section>

<section class="section py-3 py-lg-5">
    <div class="container">
         <div class="content text-secondary">
            <div class="row justify-content-center">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="icon-phone-call fs-2"></i>
                                </div>
                                <div class="col">
                                    <h4>Corporate helpline:</h4>
                                    <a href="tel:+91 9810235477">+91 9810235477</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="icon-phone-call fs-2"></i>
                                </div>
                                <div class="col">
                                    <h4>Guest helpline:</h4>
                                    <a href="tel:+91 9810074777">+91 9810074777</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-envelope fs-1"></i>
                                </div>
                                <div class="col">
                                    <h4>Email us at:</h4>
                                    <a href="mailto:info@varefamily.com">info@varefamily.com</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
         <div class="row mt-5">
            <h3>Join us</h3>
            <p>Join the team at V are Family and work with a group of people who value a passion for hospitality, a professionalism in service, exceptional home keeping skills, and a love for travel. We take pride in being great hosts.</p>
            <p>If that sounds right up your alley reach out to us! Email us at <a href="mailto:info@varefamily.com">info@varefamily.com</a> or give us a call to discuss your future.</p>
         </div>
    </div>
</section>




@endsection