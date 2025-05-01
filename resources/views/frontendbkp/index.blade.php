

{{-- Your content goes here --}}
@extends('components.layouts.app')
@section('content')

<section class="section section-hero pb-xl-0 bg-primary-light">
    <div class="swiper hero-slides">
        <div class="swiper-wrapper">
            @foreach($home_banner as $banner)
                @php $heading = $banner->heading; $subtitle = $banner->subtitle; @endphp
                <div class="swiper-slide">
                    <div class="hero-slide-img" style="background-image: url({{ asset('storage/home_banner/' . $banner->image) }});"></div>
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination pagination-white swiper-pagination-vertical"></div>
    </div>
    <div class="container animFade">
        <div class="hero-content text-xl-center  flex-grow-1">
            <div class="hero-title">
                <h1><?php echo $heading; ?></h1>
            </div>
            <div class="hero-body">
                <p style="font-style: normal;" ><?php echo $subtitle; ?></p>
            </div>

            <div class="booking-form-wrapper">
                @include('frontend.booking-form')
            </div>
        </div>
    </div>
</section>


<section class="section-location d-none d-xl-block">    
    <div class="container animFade" data-trigger-pos="100%">
        <ul class="nav">
            @foreach($locations as $location)
               <li style="font-style: normal;"><a href="{{route('properties',['id' => $location->id])}}">{{ $location->location_name }}</a></li>
            @endforeach
        </ul>
    </div>
</section>


<section class="section section-experience d-table w-100">
    <div class="swiper experience-slides d-none d-lg-block">
        <div class="swiper-wrapper">
            @foreach($diffrences as $diffrence)
                <div class="swiper-slide">
                    <div class="bg" style="background-image: url('{{ asset("storage/our_difference/{$diffrence->image}") }}');"></div>
                </div>
            @endforeach
        </div>
        
        <div class="swiper-pagination pagination-white swiper-pagination-vertical"></div>
    </div>
   <div class="d-table-cell align-middle">
          <div class="container">
                <div class="collapsible-wrapper animFade">
                    <h2 style="font-style: normal;">Experience Our Difference</h2>
                    <ul class="list-unstyled mb-0">
                    
                        @foreach($diffrences as $index => $diffrence)
                        <li class="{{ $index === 0 ? 'active' : '' }}">
                            <h3>{{ $diffrence->title }}</h3>
                            <div class="collapse-text" style="{{ $index === 0 ? '' : 'display: none;' }}">
                                <div class="collapse-text-inner" style="font-style: normal;">
                                    {!! $diffrence->detail !!}
                                    <div class="collapse-img d-lg-none">
                                        <img src="{{ asset('storage/our_difference/' . $diffrence->image) }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
   </div>
</section>

<section class="section section-invitations section-swiper bg-secondary-light2">
    <div class="container ">
        <div class="title text-center animFade">
            <h2>Special Invitations</h2>
        </div>

        <div class="tabs-content animFade">
            <div class="row g-5">
                <div class="col-12 col-lg-auto order-2 order-lg-0">
                    <div class="tabs-outer swiper tabs-slider">
                        <ul class="tabs-btn swiper-wrapper flex-lg-wrap nav-tabs list-unstyled mb-0  border-0" role="tablist">
                           
                     
                                @foreach($special_invitation as $index => $invitation)
                                    <li class="swiper-slide {{ $index === 0 ? 'active' : '' }}">
                                        <button class="btn rounded-pill{{ $index === 0 ? ' active' : '' }}" 
                                                data-bs-toggle="tab" 
                                                id="{{ $invitation->id }}" 
                                                data-bs-target="#pane-{{ $invitation->id }}" 
                                                type="button" 
                                                role="tab" 
                                                aria-controls="{{ $invitation->target }}" 
                                                aria-s
                                                elected="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $invitation->offer_name }}
                                        </button>
                                    </li>
                                @endforeach
                            
                            
                          </ul>
                    </div>
                </div>
                <div class="col-12 col-lg order-1 order-lg-0">
                    <div class="invitations-content">
                        <div class="tab-content">

                            @foreach($special_invitation as $index => $invitation)
                            <div class="tab-pane {{ $index === 0 ? ' show active' : '' }}" id="pane-{{ $invitation->id }}" role="tabpanel" aria-labelledby="{{ $invitation->id }}" tabindex="0">
                                <div class="row g-4">
                                    <div class="col-12 col-lg-auto">
                                        <div class="mx-n3 mx-sm-0">
                                            <img loading="lazy" src="{{ asset('storage/special_invitations/' . $invitation->image) }}" alt="{{ $invitation->name }}">
                                        </div>
                                    </div>
                                    <div class="col align-self-center ps-lg-5">
                                        <div style="font-style: normal;" class="tc-title">{{ $invitation->offer_name }}</div>
                                        <div class="tc-content">
                                            <p>{{ $invitation->headline }}</p>
                                        </div>
                        
                                        <div style="font-style: normal;" class="offer-date">Valid Till: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $invitation->validity)->format('jS M Y') }}

                                        </div>
                                        

                        
                                        <a href="#" class="btn btn-secondary" style="font-style: normal;" data-fancybox data-src="#offer-pop{{ $invitation->id}}">VIEW DETAILS</a>
                                    </div>
                                </div>
                            </div>
                            <div id="offer-pop{{ $invitation->id}}" class="offer-popup popup">
                                <div class="ic-info mb-4 pb-2">
                                    <p >{{$invitation->offer_name}}</p>
                                    <h3 class="my-3">{{$invitation->headline}}</h3>
                                    <h6>Valid Till: {{\Carbon\Carbon::createFromFormat('Y-m-d', $invitation->validity)->format('jS M Y') }}</h6>

                                </div>
                                <div class="row gy-4 gx-5">
                                    <div class="col-12 col-md-5">
                                        <img loading="lazy" src="{{ asset('storage/special_invitations/' . $invitation->image) }}" class="w-100" alt="General Offer" >
                                    </div>
                                    <div class="col-12 col-md">
                                        <div class="content">
                                            {!!$invitation->description!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    <div class="swiper-track d-lg-none">
        <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
        <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
    </div>
</section>


<section class="section py-0 section-guests-comments section-swiper bg-primary-light position-relative">
    <div class="container">
        <div class="row">
            <div class="col-12 col-xl-6">
                <div class="swiper comments-thumb-slider comments-images h-100">
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $testimonial)
                        @if ($testimonial->file_type == 'image')
                            <div class="swiper-slide">
                                <div class="ratio ratio-4x3">
                                    <img loading="lazy" src="{{ asset('storage/testimonials/' . $testimonial->file) }}" alt="">
                                </div>
                            </div>
                        @else
                            <div class="swiper-slide">
                                <div class="ratio ratio-4x3">
                                    <video src="{{ asset('storage/testimonials/' . $testimonial->file) }}"></video>
                                    <a href="{{ asset('storage/testimonials/' . $testimonial->file) }}" class="video-control" data-fancybox>
                                        <i class="bi bi-play-fill"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    
                    </div>
                    
                    
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="comments-slider-wrapper animFade">
                    <h3>Guest Comments</h3>
                    
                    <div class="swiper-outer-wrapper">
                        <div class="swiper comments-slider h-100">
                            <div class="swiper-wrapper">
                                @foreach ($testimonials as $testimonial)
                                <article class="swiper-slide">
                                    <blockquote>
                                        <p>{!! Str::words($testimonial->headline, 24) !!}</p>
                                        @if (str_word_count($testimonial->headline) > 24)
                                        <button class="bg-transparent border-0 text-primary fs-6 fst-italic read-more-btn" data-fancybox data-src="#comment{{ $testimonial->id }}">Read More</button>
                                        @endif
                                    </blockquote>
                                    <cite>
                                        <strong>{{ $testimonial->guest_name }}</strong> 
                                        {{ $testimonial->home_name }}, {{ $testimonial->location }}<br>
                                        {{ date('F d, Y', strtotime($testimonial->date)) }}
                                    </cite>                               
                                </article>
                                <div id="comment{{ $testimonial->id }}" class="offer-popup popup">
                                    <div class="comments-slider-wrapper ps-0 text-center">
                                        <article>
                                            <blockquote>
                                                <p>{!! $testimonial->headline !!}</p>
                                            </blockquote>
                                            <cite>
                                                <strong>{{ $testimonial->guest_name }}</strong>
                                                {{ $testimonial->home_name }}, {{ $testimonial->location }}<br>
                                                {{ date('F d, Y', strtotime($testimonial->date)) }}
                                            </cite>                               
                                        </article>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="comments-logo">
                       <img src="assets/images/sites-logo.jpg" alt="">
                    </div>
                </div>
            </div>
            

        </div>
    </div>
    <div class="swiper-track mt-0">
        <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
        <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
    </div>
</section>

@foreach ($second_home as $home )

    <section class="section section-home-owner bg-secondary-light2">
        <div class="container">
            <div class="title text-center text-secondary animFade">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <h2 class="h3 text-black">{{$home->tag_line}}</h2>
                        <p>{{$home->introduction}}

                    </div>
                </div>
            </div>
        </div>   
        <div class="container-fluid px-0">     
            <div class="home-owner-cards">
                <div class="row gy-lg-4">
                    <div class="col-12 col-md-4 animFade">
                        <div class="card card-primary">
                            <div class="card-img-top">
                                <span>
                                    <img loading="lazy" src="{{ asset('storage/second_home/' . $home->image1) }}" alt="Sustained Income Generation">
                                </span>
                            </div>
                            <div class="card-body pb-0">
                                <h3>{{$home->title1}}</h3>
                                <p>{{$home->description1}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 animFade">
                        <div class="card card-primary">
                            <div class="card-img-top">
                                <span>
                                    <img loading="lazy" src="{{ asset('storage/second_home/' . $home->image2) }}" alt="Maintenance of Your Home">
                                </span>
                            </div>
                            <div class="card-body pb-0">
                                <h3>{{$home->title2}}</h3>
                                <p>{{$home->description2}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 animFade">
                        <div class="card card-primary">
                            <div class="card-img-top">
                                <span>
                                    <img loading="lazy" src="{{ asset('storage/second_home/' . $home->image3) }}" alt="Flexibility of Your Use">
                                </span>
                            </div>
                            <div class="card-body pb-0">
                                <h3>{{$home->title3}}</h3>
                                <p>{{$home->description3}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="btn-wrap animFade">
                <a href="{{ route('join-our-network') }}" class="btn btn-secondary" wire:navigate >JOIN OUR NETWORK</a>
            </div>
        </div>
    </section>
    @endforeach


    <section class="section section-blog section-swiper">
        <div class="container">
            <div class="title text-center animFade">
                <h2>Recent Blogs</h2>         
            </div>
            
            <div class="home-owner-cards">
                <div class="swiper blog-swiper">
                    <div class="swiper-wrapper row gy-4 gx-3 gx-sm-4">
                        @foreach ($recent_blog as $blog )
                            
                        
                        <div class="col-12 col-md-4 animFade swiper-slide">
                            <a href="{{ route('blog.detail', $blog->id) }}" class="card card-secondary">
                                <div class="card-img-top">
                                    <span>
                                        <img style="font-style: normal;" loading="lazy" src="{{ asset('storage/blogs/' . $blog->image) }}" alt="Explore Our Newest Apartments in Kasauli Hills">
                                    </span>
                                </div>
                                <div class="card-footer">
                                    <time datetime="{{ date('Y-m-d', strtotime($blog->date)) }}">{{ date('d F Y', strtotime($blog->date)) }}
                                    </time>

                                    <h3>{{$blog->title}}</h3>
                                </div>
                            </a>
                        </div> 
                        @endforeach
                                
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-track d-lg-none">
            <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
            <div
             class="cs-next"><i class="bi bi-chevron-right"></i></div>
        </div>

        <div class="container">
            <div class="btn-wrap animFade">
                <a href="{{ route('blogs') }}" class="btn btn-secondary" wire:nevigate>READ ALL BLOGS</a>
            </div>
        </div>
    </section>
    

    
    <section class="section section-insta section-swiper bg-secondary-light2">
        <div class="container">
            <div class="insta-link mb-4 mb-sm-5 animFade">
                <a href="#" target="_blank">
                    <i class="icon-instagram"></i>
                    @varefamilyvacationhomes
                </a>
            </div>
            
            <div class="insta-images">
                <div class="swiper insta-swiper insta-image-inner">
                    <div class="swiper-wrapper row gx-3 gx-sm-4">
                        <div class="col-3 animFade swiper-slide">
                            <a href="#"><img loading="lazy" src="./assets/images/insta-1.jpg" alt=""></a>
                        </div>
                        <div class="col-3 animFade swiper-slide">
                            <a href="#"><img loading="lazy" src="./assets/images/insta-2.jpg" alt=""></a>
                        </div>
                        <div class="col-3 animFade swiper-slide">
                            <a href="#"><img loading="lazy" src="./assets/images/insta-3.jpg" alt=""></a>
                        </div>
                        <div class="col-3 animFade swiper-slide">
                            <a href="#"><img loading="lazy" src="./assets/images/insta-4.jpg" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-track d-lg-none">
            <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
            <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
        </div>
        
    </section>

    <script>
        window.onload = function() {
            if (performance.navigation.type == 2) {
                // Reload the page if the user navigated back to it
                location.reload(true);
            }
        }
    </script>
    
@endsection



