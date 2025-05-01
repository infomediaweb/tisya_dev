 
@extends('components.layouts.app')
@section('content')

<section class="section section-hero page-hero  innerpage-hero pb-0 bg-primary-light">
    <div class="swiper hero-slides">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="hero-slide-img" style="background-image: url(./assets/images/about-hero.webp);"></div>
            </div>
        </div>
    </div>
    <div class="container animFade">
        <div class="hero-content text-xl-center  flex-grow-1">
            <div class="hero-title">
                <h1>V are Family is a <em>HOME centric venture</em></h1>    
            </div>         
        </div>       
    </div>
</section>

<section class="section py-3 py-lg-5">
    <div class="container">
         <div class="content text-secondary">
            <div class="row justify-content-center text-xl-center">
                <div class="col-12 col-xl-9">
                    <p>We manage and operate a network of handpicked vacation homes, in stunning locations across India. Each vacation home is carefully planned to offer a private and curated experience to families and large groups.</p>
                    <p>All our homes have dedicated home cooks and staff who strive to provide guests with everything they require.</p>
                    <p>We aim to deliver the highest levels of personalised service in the comfort of a private and spacious home.</p>
                    <p>Our core attributes, as listed below, come together to deliver an unmatched sustainable experience across our homes.</p>
                </div>
            </div>
         </div>        
    </div>  
</section>

<section class="section py-0 bg-secondary-light">
    <div class="section-about1 mt-0">
        <div class="container-fluid">
            <div class="content">
                <div class="row my-4 justify-content-center text-lg-center">                
                    <div class="col-12 col-lg-9">
                        <h2 class="mb-4">Service Certainty</h2>
                        <p>When staying at a V are Family home guests are assured an unprecedented level of service, previously only seen at upscale hotels.</p>
                        <p>We guarantee a high level of personalized service, delivered by ‘home staff’, provided via a carefully built home management system and professional training, which is unparalleled in vacation homes.</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-12 col-md-6 col-xl-3 d-flex">
                        <div class="aboutBox w-100" style="background-image: url(./assets/images/blog-3.jpg);">
                            <div class="boxWrapper">
                                <h3>Seamless Support</h3>
                                <div class="content">
                                    <p>Our home support is the backbone of our service capabilities. The home staff is able to provide such high-quality service due to a strong home support system, encompassing all aspects of exceptional home maintenance, home keeping, and procurement of guest materials.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 d-flex">
                        <div class="aboutBox w-100" style="background-image: url(./assets/images/5.jpg);">
                            <div class="boxWrapper">
                                <h3>Empowered Thinking</h3>
                                <div class="content">
                                    <p>We do things differently at V are Family. There is no hierarchy within our team, every person is a leader in their own right and is proactive in decision-making situations. We believe providing individuals with the freedom and ability to make decisions evokes a sense of self and encourages team members to become active thinkers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 d-flex">
                        <div class="aboutBox w-100" style="background-image: url(./assets/images/hw-2.jpg);">
                            <div class="boxWrapper">
                                <h3>Passion for Excellence</h3>
                                <div class="content">
                                    <p>Everyone at V are Family has a sincere desire to host guests and welcome them, as they would in their own homes. Impossible to measure, this trait forms the heart of our venture and is at the core of what elevates our service. Every member of our team genuinely cares about providing guests with the perfect holiday experience.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 d-flex">
                        <div class="aboutBox w-100" style="background-image: url(./assets/images/blog-2.jpg);">
                            <div class="boxWrapper">
                                <h3>Social Inclusiveness</h3>
                                <div class="content">
                                    <p>V are Family is built on a foundation of social relevance and elevation of its people. Led by home makers and domestic home staff, we aim to build a new pedigree of professional ‘home managers’, that will pave the way to developing an organized home management industry.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
        </div> 
    </div> 
</section>


@endsection