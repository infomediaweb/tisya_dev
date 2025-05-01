    
@extends('components.layouts.app')
@section('content')
 
<section class="section section-hero page-hero innerpage-hero pb-0 bg-primary-light">
    <div class="swiper hero-slides">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="hero-slide-img" style="background-image: url(./assets/images/faq-hero.webp);"></div>
            </div>
        </div>
    </div>
    <div class="container animFade">
        <div class="hero-content text-xl-center  flex-grow-1">
            <div class="hero-title">
                <h1>FAQ'S</h1>    
            </div>         
        </div>       
    </div>
</section>

<section class="section faq-section py-3 py-lg-5">
    <div class="container">
        <div class="content text-secondary">
            <div class="row justify-content-center mb-3">
                <div class="col-12">
                    <ul class="faqList">
                        @foreach($faqs as $faq)
                        <li>
                            <h4 class="question">{{ $faq->question }}</h4>
                            <div class="answer"><p>{{ $faq->answer }}</p></div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        $(".faqList li h4").on('click', function(){
            $(this).next(".answer").slideToggle();
            $(this).toggleClass("active");
            $(this).parent().siblings().find(".answer").slideUp();
            $(this).parent().siblings().find("h4").removeClass("active");
        });
    });
</script>

@endsection
