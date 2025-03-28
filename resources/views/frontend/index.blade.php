@extends('layout.main')
@section('content')
    <section class="section flex-wrap section-hero section-bg">
         @if(!empty($home_banner->homeBannerImage) && $home_banner->homeBannerImage->isNotEmpty())
            <div class="section-bg">
                <div class="swiper hero-banner-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($home_banner->homeBannerImage as $bannerImage)
                            <div class="swiper-slide">
                                <img src="{{ $bannerImage->filepath ?? '' }}" alt="Banner Image">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        @if($home_banner->homeBannerVideo)
            <div class="section-bg">
                <video autoplay muted loop playsinline>
                    <source src="{{ asset('storage/home_banner/' . $home_banner->homeBannerVideo->file ?? '') }}" type="video/mp4">
                </video>
            </div>
        @endif

        <div class="container hero-content-container position-relative">
            <div class="row">
                <div class="col-12">
                    <div class="hero-card text-center">
                      <h1>{{  $home_banner->heading ?? '' }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-search">        
        <div class="container">
            @include('frontend.home-calendar.search')
        </div>
    </section>

    <section class="section section-swiper property-section">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-sm">
                        <h2> {{  $home_banner->subtitle ?? '' }}</h2>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="{{ route('property-list', ['type' => 'listAllProperty']) }}" class="orangeLink">Explore all properties</a>
                    </div>                   
                </div>
            </div>
            <div class="properties-wrapper">
                <div class="swiper-main-outer">
                    <div class="swiper swiper-property">
                        <div class="swiper-wrapper">
                            @foreach ($properties as $property)
                             @php $priceAndAvaliability = getPriceAndAvalibility($property->ru_property_id);   @endphp
                                <div class="swiper-slide">
                                    <div class="card card-property">
                                        <a href="{{ route('property-detail', ['home_type' => strtolower($property->home_type),'slug' => $property->url_key]) }}" target="_blank"
                                            class="card-img-top">
                                            <div class="swiper-outer">
                                                <div class="swiper swiper-property-image">
                                                    <div class="swiper-wrapper">
                                                        @if ($property->images->where('type', 'image')->isNotEmpty())
                                                            @foreach ($property->images->where('type', 'image') as $image)
                                                                <div class="swiper-slide">
                                                                    <div class="imgBox">
                                                                        <img loading="lazy"
                                                                            src="{{ asset($image->website_image) }}"
                                                                            class="w-100" alt="Image Title Goes Here">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="swiper-slide">
                                                                <div class="imgBox">
                                                                     <img loading="lazy" src="{{ asset('assets/images/noimage-property.jpg') }}" class="w-100" alt="Image Title Goes Here">
                                                                </div>
                                                            </div>
                                                        @endif


                                                    </div>
                                                    <div class="swiper-button-prev"></div>
                                                    <div class="swiper-button-next"></div>
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                            </div>
                                        </a>
                                        @php
                                           $firstTag = $property->tags->first();
                                        @endphp
                                        @if(!empty($firstTag))
                                            <a href="{{ route('tag-property-list', ['tag_name' => $firstTag->tags_name ?? '']) }}" class="badge z-1 text-decoration-none 
                                                text-bg-secondary text-white position-absolute top-0 left-0 fw-normal m-3">
                                                {{ $firstTag->tags_name }}
                                            </a>
                                        @endif
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-title h4 mb-0">{{ $property->home_name }}</div>
                                                <div class="location-state">
                                                    {{ $property->locationData->location_name ?? '' }}, {{ $property->state }}
                                                </div>
                                                  <div class="price">From &#8377;{{ number_format($priceAndAvaliability['per_night_price']) }} / night</div>
                                                <small>per night + taxes</small>
                                                {{-- <div class="price">From &#8377;{{ number_format($property->pl_price->nightly_rate) }} / night</div> --}}
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <ul class="nav property-short-info">
                                                <li><span class="icon-users"></span>Upto {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }}</li>
                                            <li><span class="icon-bed"></span>{{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Room' : $property->no_of_bedrooms . ' Rooms' }}</li>
                                             <li><span class="icon-bath"></span>{{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="row justify-content-center mt-4 pt-3 d-lg-flex">
                        <div class="col-auto position-relative">
                            <button class="btn p-0 swiper-outer-prev"><span class="icon-arrow-left"></span></button>
                        </div>
                        <div class="col-auto position-relative">
                            <button class="btn p-0 swiper-outer-next"><span class="icon-arrow-right"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="section section-swiper property-section pt-0">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-sm">
                        <h2> {{  $home_banner->apartment_title ?? '' }}</h2>
                    </div>
                                   
                </div>
            </div>
            <div class="properties-wrapper">
                <div class="swiper-main-outer">
                    <div class="swiper swiper-property">
                        <div class="swiper-wrapper">
                            @foreach ($propertiesApartment as $property)
                             @php $priceAndAvaliability = getPriceAndAvalibility($property->ru_property_id);   @endphp
                                <div class="swiper-slide">
                                    <div class="card card-property">
                                        <a href="{{ route('property-detail', ['home_type' => strtolower($property->home_type),'slug' => $property->url_key]) }}" target="_blank"
                                            class="card-img-top">
                                            <div class="swiper-outer">
                                                <div class="swiper swiper-property-image">
                                                    <div class="swiper-wrapper">
                                                        @if ($property->images->where('type', 'image')->isNotEmpty())
                                                            @foreach ($property->images->where('type', 'image') as $image)
                                                                <div class="swiper-slide">
                                                                    <div class="imgBox">
                                                                        <img loading="lazy"
                                                                            src="{{ asset($image->website_image) }}"
                                                                            class="w-100" alt="Image Title Goes Here">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="swiper-slide">
                                                                <div class="imgBox">
                                                                     <img loading="lazy" src="{{ asset('assets/images/noimage-property.jpg') }}" class="w-100" alt="Image Title Goes Here">
                                                                </div>
                                                            </div>
                                                        @endif


                                                    </div>
                                                    <div class="swiper-button-prev"></div>
                                                    <div class="swiper-button-next"></div>
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                            </div>
                                        </a>
                                        @php
                                           $firstTag = $property->tags->first();
                                        @endphp
                                        @if(!empty($firstTag))
                                            <a href="{{ route('tag-property-list', ['tag_name' => $firstTag->tags_name ?? '']) }}" class="badge z-1 text-decoration-none 
                                                text-bg-secondary text-white position-absolute top-0 left-0 fw-normal m-3">
                                                {{ $firstTag->tags_name }}
                                            </a>
                                        @endif
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-title h4 mb-0">{{ $property->home_name }}</div>
                                                <div class="location-state">
                                                    {{ $property->locationData->location_name ?? '' }}, {{ $property->state }}
                                                </div>
                                                  <div class="price">From &#8377;{{ number_format($priceAndAvaliability['per_night_price']) }} / night</div>
                                                <small>per night + taxes</small>
                                                {{-- <div class="price">From &#8377;{{ number_format($property->pl_price->nightly_rate) }} / night</div> --}}
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <ul class="nav property-short-info">
                                                <li><span class="icon-users"></span>Upto {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }}</li>
                                            <li><span class="icon-bed"></span>{{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Room' : $property->no_of_bedrooms . ' Rooms' }}</li>
                                             <li><span class="icon-bath"></span>{{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="row justify-content-center mt-4 pt-3 d-lg-flex">
                        <div class="col-auto position-relative">
                            <button class="btn p-0 swiper-outer-prev"><span class="icon-arrow-left"></span></button>
                        </div>
                        <div class="col-auto position-relative">
                            <button class="btn p-0 swiper-outer-next"><span class="icon-arrow-right"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--        <div class="properties-wrapper">-->
    <!--                <div class="swiper-main-outer">-->
    <!--                    <div class="swiper swiper-property">-->
    <!--                        <div class="swiper-wrapper">-->
    <!--                            <div class="swiper-slide">-->
    <!--                                <div class="card card-property">-->
    <!--                                    <a href="{{ route('properties') }}" class="card-img-top">-->
    <!--                                        <div class="swiper-outer">-->
    <!--                                            <div class="swiper swiper-property-image">-->
    <!--                                                <div class="swiper-wrapper">-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa1.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa2.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                </div>-->
    <!--                                                <div class="swiper-button-prev"></div>-->
    <!--                                                <div class="swiper-button-next"></div>        -->
    <!--                                                <div class="swiper-pagination"></div>-->
    <!--                                            </div>-->
    <!--                                        </div>-->
    <!--                                    </a>-->
    <!--                                    <div class="card-body">-->
    <!--                                        <div class="row">-->
    <!--                                            <div class="card-title h4 mb-0">Studio 109</div>-->
    <!--                                            <div class="card-text mb-2">Arpora, North Goa, Goa, India</div>-->
    <!--                                            <div class="price">From &#8377;5,499 / night</div>-->
    <!--                                        </div>-->
    <!--                                    </div>-->
    <!--                                    <div class="card-footer">-->
    <!--                                        <ul class="nav property-short-info">-->
    <!--                                            <li><span class="icon-users"></span>Upto 10 Guests</li>-->
    <!--                                            <li><span class="icon-bed"></span>4 Rooms</li>-->
    <!--                                            <li><span class="icon-bath"></span>3 Bathrooms</li>-->
    <!--                                        </ul>                                           -->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>-->
                                
    <!--                            <div class="swiper-slide">-->
    <!--                                <div class="card card-property">-->
    <!--                                    <a href="{{ route('properties') }}" class="card-img-top">-->
    <!--                                        <div class="swiper-outer">-->
    <!--                                            <div class="swiper swiper-property-image">-->
    <!--                                                <div class="swiper-wrapper">-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa2.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa3.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                </div>-->
    <!--                                                <div class="swiper-button-prev"></div>-->
    <!--                                                <div class="swiper-button-next"></div>        -->
    <!--                                                <div class="swiper-pagination"></div>-->
    <!--                                            </div>-->
    <!--                                        </div>-->
    <!--                                    </a>-->
    <!--                                    <div class="card-body">-->
    <!--                                        <div class="row">-->
    <!--                                            <div class="card-title h4 mb-0">Plush</div>-->
    <!--                                            <div class="card-text mb-2">Arpora, North Goa, Goa, India</div>-->
    <!--                                            <div class="price">From &#8377;5,499 / night</div>-->
    <!--                                        </div>-->
    <!--                                    </div>-->
    <!--                                    <div class="card-footer">-->
    <!--                                        <ul class="nav property-short-info">-->
    <!--                                            <li><span class="icon-users"></span>Upto 10 Guests</li>-->
    <!--                                            <li><span class="icon-bed"></span>4 Rooms</li>-->
    <!--                                            <li><span class="icon-bath"></span>3 Bathrooms</li>-->
    <!--                                        </ul> -->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>-->
                                
    <!--                            <div class="swiper-slide">-->
    <!--                                <div class="card card-property">-->
    <!--                                    <a href="{{ route('properties') }}" class="card-img-top">-->
    <!--                                        <div class="swiper-outer">-->
    <!--                                            <div class="swiper swiper-property-image">-->
    <!--                                                <div class="swiper-wrapper">-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa3.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa4.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                </div>-->
    <!--                                                <div class="swiper-button-prev"></div>-->
    <!--                                                <div class="swiper-button-next"></div>        -->
    <!--                                                <div class="swiper-pagination"></div>-->
    <!--                                            </div>-->
    <!--                                        </div>-->
    <!--                                    </a>-->
    <!--                                    <div class="card-body">-->
    <!--                                        <div class="row">-->
    <!--                                            <div class="card-title h4 mb-0">Villa Rayana</div>-->
    <!--                                            <div class="card-text mb-2">Mukteshwar, Nainital, Uttarakhand, India</div>-->
    <!--                                            <div class="price">From &#8377;5,499 / night</div>-->
    <!--                                        </div>-->
    <!--                                    </div>-->
    <!--                                    <div class="card-footer">-->
    <!--                                        <ul class="nav property-short-info">-->
    <!--                                            <li><span class="icon-users"></span>Upto 10 Guests</li>-->
    <!--                                            <li><span class="icon-bed"></span>4 Rooms</li>-->
    <!--                                            <li><span class="icon-bath"></span>3 Bathrooms</li>-->
    <!--                                        </ul> -->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>-->
                                
    <!--                            <div class="swiper-slide">-->
    <!--                                <div class="card card-property">-->
    <!--                                    <a href="{{ route('properties') }}" class="card-img-top">-->
    <!--                                        <div class="swiper-outer">-->
    <!--                                            <div class="swiper swiper-property-image">-->
    <!--                                                <div class="swiper-wrapper">-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa4.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                    <div class="swiper-slide">-->
    <!--                                                        <div class="imgBox">-->
    <!--                                                            <img loading="lazy" src="{{ asset('assets/images/goa3.jpg') }}" class="w-100" alt="Image Title Goes Here">-->
    <!--                                                        </div>-->
    <!--                                                    </div>-->
    <!--                                                </div>-->
    <!--                                                <div class="swiper-button-prev"></div>-->
    <!--                                                <div class="swiper-button-next"></div>        -->
    <!--                                                <div class="swiper-pagination"></div>-->
    <!--                                            </div>-->
    <!--                                        </div>-->
    <!--                                    </a>-->
    <!--                                    <div class="card-body">-->
    <!--                                        <div class="row">-->
    <!--                                            <div class="card-title h4 mb-0">Serene</div>-->
    <!--                                            <div class="card-text mb-2">Candolim, North Goa, Goa, India</div>-->
    <!--                                            <div class="price">From &#8377;12,999 / night</div>-->
    <!--                                        </div>-->
    <!--                                    </div>-->
    <!--                                    <div class="card-footer">-->
    <!--                                        <ul class="nav property-short-info">-->
    <!--                                            <li><span class="icon-users"></span>Upto 10 Guests</li>-->
    <!--                                            <li><span class="icon-bed"></span>4 Rooms</li>-->
    <!--                                            <li><span class="icon-bath"></span>3 Bathrooms</li>-->
    <!--                                        </ul>-->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>  -->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="row justify-content-center mt-4 pt-3">-->
    <!--                        <div class="col-auto position-relative">-->
    <!--                            <button class="btn p-0 swiper-outer-prev"><span class="icon-arrow-left"></span></button>-->
    <!--                        </div>-->
    <!--                        <div class="col-auto position-relative">-->
    <!--                            <button class="btn p-0 swiper-outer-next"><span class="icon-arrow-right"></span></button>-->
    <!--                        </div>-->
    <!--                    </div>                          -->
    <!--                </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->

    <section class="section section-swiper collections-section pt-0">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-sm">
                        <h2>Curated Collections</h2>
                    </div>
                    <div class="col-auto align-self-center">
                       <a href="{{ route('property-list', ['type' => 'listCollectionProperty']) }}" class="orangeLink">Explore all properties</a>
                       
                    </div>                   
                </div>
            </div>           

            <div class="collection-wrapper">              
                <div class="swiper-main-outer">
                    <div class="swiper swiper-collection">
                        <div class="swiper-wrapper">
                               @foreach ( $collections as $collection)
                                                       
                            <div class="swiper-slide">
                                <a href="{{ route('property-list', ['filter_name' => $collection->collection_name, 'type' => 'listCollectionBasedProperty']) }}" class="card card-collection">
                                     
                                    <div class="imgBox">
                                         <!--<img loading="lazy" -->
                                         <!--  src="{{ $collection->image ?? '' }}" >-->
                                         
                                         <img loading="lazy" 
                                           src="{{ asset('storage/collection/images/'. $collection->image ?? 'assets/images/d1.jpg') }}" >
                                         
                                        <!--<img loading="lazy" src=" {{ asset('assets/images/c4.jpg') }}" alt="">-->
                                    </div>
                                    <div class="content">
                                    <h3>{{ $collection->collection_name ?? '' }}</h3>
                                    <p>{!! $collection->collection_description ?? '' !!}</p>
                                    </div>
                                    <span class="roundedBtn"><i class="icon-arrow-up-right"></i></span>
                                </a>
                            </div>
                                    @endforeach
                            <!--<div class="swiper-slide">-->
                            <!--    <a href="#" class="card card-collection">                                        -->
                            <!--        <div class="imgBox">-->
                            <!--            <img loading="lazy" src="{{ asset('assets/images/c1.jpg') }}" alt="">-->
                            <!--        </div>-->
                            <!--        <div class="content">-->
                            <!--            <h3>Relax an</h3>-->
                            <!--            <p>Lorem ipsum dolor sit amet adipisig elit Sunt hic ipsa, culpa ducimus rerum aliquam corrupti</p>-->
                            <!--        </div>-->
                            <!--        <span class="roundedBtn"><i class="icon-arrow-up-right"></i></span>-->
                            <!--    </a>-->
                            <!--</div>-->
                            
                            <!--<div class="swiper-slide">-->
                            <!--    <a href="#" class="card card-collection">                                        -->
                            <!--        <div class="imgBox">-->
                            <!--            <img loading="lazy" src="{{ asset('assets/images/c2.jpg') }}" alt="">-->
                            <!--        </div>-->
                            <!--        <div class="content">-->
                            <!--            <h3>Redefining Luxe</h3>-->
                            <!--            <p>Lorem ipsum dolor sit amet adipisig elit Sunt hic ipsa, culpa ducimus rerum aliquam corrupti</p>-->
                            <!--        </div>-->
                            <!--        <span class="roundedBtn"><i class="icon-arrow-up-right"></i></span>-->
                            <!--    </a>-->
                            <!--</div>-->
                            
                            <!--<div class="swiper-slide">-->
                            <!--    <a href="#" class="card card-collection">                                        -->
                            <!--        <div class="imgBox">-->
                            <!--            <img loading="lazy" src="{{ asset('assets/images/c3.jpg') }}" alt="">-->
                            <!--        </div>-->
                            <!--        <div class="content">-->
                            <!--            <h3>Love and Joy</h3>-->
                            <!--            <p>Lorem ipsum dolor sit amet adipisig elit Sunt hic ipsa, culpa ducimus rerum aliquam corrupti</p>-->
                            <!--        </div>-->
                            <!--        <span class="roundedBtn"><i class="icon-arrow-up-right"></i></span>-->
                            <!--    </a>-->
                            <!--</div>-->
                            
                            <!--<div class="swiper-slide">-->
                            <!--    <a href="#" class="card card-collection">                                        -->
                            <!--        <div class="imgBox">-->
                            <!--            <img loading="lazy" src="{{ asset('assets/images/c4.jpg') }}" alt="">-->
                            <!--        </div>-->
                            <!--        <div class="content">-->
                            <!--            <h3>Creating Memories</h3>-->
                            <!--            <p>Lorem ipsum dolor sit amet adipisig elit Sunt hic ipsa, culpa ducimus rerum aliquam corrupti</p>-->
                            <!--        </div>-->
                            <!--        <span class="roundedBtn"><i class="icon-arrow-up-right"></i></span>-->
                            <!--    </a>-->
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="row justify-content-center mt-4 pt-3 d-lg-flex">
                        <div class="col-auto position-relative">
                            <button class="btn p-0 swiper-outer-prev"><span class="icon-arrow-left"></span></button>
                        </div>
                        <div class="col-auto position-relative">
                            <button class="btn p-0 swiper-outer-next"><span class="icon-arrow-right"></span></button>
                        </div>
                    </div>  
                </div>                
            </div>
        </div>
    </section>




  <div class="bg-wrapper">
    <div class="section-bg d-none d-lg-block" style="background-color: #E7E0CE;">
        <img loading="lazy" src="{{ asset('assets/images/bg.png') }}" alt="">
    </div>
    <section class="section section-swiper destinations-section position-relative">
        <div class="section-bg mt-0 d-lg-none" style="background-color: #E7E0CE;">
            <img loading="lazy" src="{{ asset('assets/images/bg.png') }}" alt="">
        </div>
        <div class="container">
            <div class="section-heading">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-auto">
                        <h2>Trending Destinations</h2>
                    </div>
                    <div class="col-12 col-lg">
                        <div class="subTxt">
                           Explore our selection of the best places to stay in Goa
                        </div>
                    </div>
                    <div class="col-auto align-self-end order-2 order-lg-0  ms-lg-auto d-lg-flex">
                        <div class="navBox row justify-content-center my-3">
                            <div class="col-auto position-relative"><button class="btn p-0 swiper-outer-prev"><span class="icon-arrow-left"></span></button></div>
                            <div class="col-auto position-relative"><button class="btn p-0 swiper-outer-next"><span class="icon-arrow-right"></span></button></div>
                        </div> 
                    </div>                   
                </div>
            </div> 
            <div class="row">
                <div class="col-12">
                    <div class="swiper-main-outer">
                        <div class="swiper swiper-destinations">
                            <div class="swiper-wrapper">   
                             @foreach ($locations as $location)
                                <div class="swiper-slide">
                                    <a href="{{ route('location-property', ['slug_location' => $location->location_name]) }}" class="card card-destinations">
                                        <div class="img-destinations">
                                             <img loading="lazy" 
                                           src="{{ asset('storage/home/images/'.$location->image ?? 'assets/images/d1.jpg') }}" >
                                            <!--<img loading="lazy" src="{{ asset('assets/images/d1.jpg') }}" alt="Arpora, North Goa">-->
                                        </div>
                                        <div class="content">
                                           <h3>  {{ $location->location_name }}, {{ $location->state->name }}</h3>
                                        </div>
                                    </a>
                                </div>
                                 @endforeach
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="section experience-section position-relative">
        <div class="container">
            <div class="section-heading">
                <h2>Experience Goa with Tisya Stays</h2>
            </div> 
            <div class="row">
                    @foreach ($addventures as $addventure)
                <div class="col-12 col-md-4">
                    <a href="{{route('contactus')}}" class="card card-img-text">
                        <div class="card-img-top cls-img" style="--cls-img-height: 78%;">
                             <img loading="lazy" 
                                           src="{{ asset('storage/home/images/'.$addventure->image ?? 'assets/images/d1.jpg') }}" >
                            
                        </div>
                        <div class="card-body">
                            <h3>{{ $addventure->title}}</h3>
                            <p>{{ $addventure->description}}</p>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
  </div>



 @if ($specialoffers && $specialoffers->isNotEmpty())
  <section class="section offers-section">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-lg">
                        <h2>Special Offers</h2>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="{{ route('specialoffers') }}" class="orangeLink">Explore all offers</a>
                    </div>                   
                </div>
            </div>
              
            <div class="row gy-4">
                @foreach ($specialoffers as $specialoffer)
                <div class="col-12 col-xl-4">
                    <div class="card card-offers">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col"><h3>{{ $specialoffer->offer_name ?? ''}}</h3></div>
                                <div class="col-auto">
                                    <div class="card-offer-img">
                                        <img loading="lazy" src="{{ $specialoffer->image ? asset('storage/special_invitations/' . $specialoffer->image) : asset('assets/images/d1.jpg') }}" alt="">
                                    </div>
                                </div>
                            </div>
                            {!! $specialoffer->description ?? '' !!}
                        </div>
                        <div class="card-footer">
                            <div class="row gy-2 gx-3 align-items-center">
                                <div class="col-12 col-xxl-7">
                                    <div class="offer-code">
                                        <span>{{ explode(',', $specialoffer->discountCoupon->codes ?? '')[0] ?? '' }}</span>
                                        <button class="btn btn-primary" data-clipboard-text="{{ explode(',', $specialoffer->discountCoupon->codes ?? '')[0] ?? '' }}"><i class="icon-copy"></i>Copy</button>
                                    </div>
                                </div>
                                <div class="col-auto"><div class="exp-text">Expires on <strong>{{ \Carbon\Carbon::parse($specialoffer->validity)->format('d M, Y') }}</strong></div></div>
                            </div>                             
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section> 
    @endif


    <section class="section blog-section pt-0">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-lg">
                        <h2>Recent Blogs</h2>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="{{route('blog')}}" class="orangeLink">Read all blogs</a>
                    </div>                   
                </div>
            </div>          

            <div class="swiper-main-outer">
                <div class="swiper swiper-blog">
                    <div class="swiper-wrapper">
                            @foreach ($blogs as $blog)  
                        <div class="swiper-slide h-auto">
                            <a href="{{route('blogdetails', ['slug' => $blog->slug])}}" class="card card-blog"> 
                                <div class="card-img-top cls-img" style="--cls-img-height: 71%;">
                                     <img loading="lazy"  src="{{ asset($blog->image ? 'storage/blogs/'.$blog->image : 'assets/images/download.png') }}">
                                       <time>{{ \Carbon\Carbon::parse($blog->date)->format('d M Y') }}</time>
                                </div>
                                <div class="card-body pb-0">
                                    <div class="content">
                                     <h3>{{ $blog->title ?? '' }}</h3>
                                {!! \Illuminate\Support\Str::words($blog->description, 20, '...') !!}

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <span class="link">Learn more </span>
                                </div>
                            </a>
                        </div>
                        @endforeach
                        <!--<div class="swiper-slide h-auto">-->
                        <!--    <a href="#" class="card card-blog">                                        -->
                        <!--        <div class="card-img-top cls-img" style="--cls-img-height: 71%;">-->
                        <!--            <img loading="lazy" src="{{ asset('assets/images/b1.jpg') }}" alt="">-->
                        <!--        </div>-->
                        <!--        <div class="card-body pb-0">-->
                        <!--            <div class="content">-->
                        <!--                <h3>Discover the alluring beauty of Goa's -->
                        <!--                Northern Beaches</h3>-->
                        <!--                <p>When it comes to experiencing Goa, there is no better place than North Goa, where you'll find the most sumptuous pleasures in its beaches, water ...</p>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--        <div class="card-footer">-->
                        <!--            <span class="link">Learn more</span>-->
                        <!--        </div>-->
                        <!--    </a>-->
                        <!--</div> -->
   </div
     </div>
                <!--        <div class="swiper-slide h-auto">-->
                <!--            <a href="#" class="card card-blog">                                        -->
                <!--                <div class="card-img-top cls-img" style="--cls-img-height: 71%;">-->
                <!--                    <img loading="lazy" src="{{ asset('assets/images/b2.jpg') }}" alt="">-->
                <!--                </div>-->
                <!--                <div class="card-body pb-0">-->
                <!--                    <div class="content">-->
                <!--                        <h3>Explore the treasures of Candolim</h3>-->
                <!--                        <p>Goa, located on India's West Coast, is an enchanting paradise. With a rich cultural legacy, well-preserved colonial structures from the ...</p>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--                <div class="card-footer">-->
                <!--                    <span class="link">Learn more</span>-->
                <!--                </div>-->
                <!--            </a>-->
                <!--        </div> -->
                <!--        <div class="swiper-slide h-auto">-->
                <!--            <a href="#" class="card card-blog">                                        -->
                <!--                <div class="card-img-top cls-img" style="--cls-img-height: 71%;">-->
                <!--                    <img loading="lazy" src="{{ asset('assets/images/b3.jpg') }}" alt="">-->
                <!--                </div>-->
                <!--                <div class="card-body pb-0">-->
                <!--                    <div class="content">-->
                <!--                        <h3>Goa's Mango Mania</h3>-->
                <!--                        <p>It's not an unknown fact that mangoes have ruled the subcontinent for over 4000 years! But in Goa, it is a whole another story, as Goa's ...</p>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--                <div class="card-footer">-->
                <!--                    <span class="link">Learn more</span>-->
                <!--                </div>-->
                <!--            </a>-->
                <!--        </div> -->

                <!--        <div class="swiper-slide h-auto">-->
                <!--            <a href="#" class="card card-blog">                                        -->
                <!--                <div class="card-img-top cls-img" style="--cls-img-height: 71%;">-->
                <!--                    <img loading="lazy" src="{{ asset('assets/images/b4.jpg') }}" alt="">-->
                <!--                </div>-->
                <!--                <div class="card-body">-->
                <!--                    <div class="content">-->
                <!--                        <h3>Top 10 Things To Do in Goa</h3>-->
                <!--                        <p>Beneath the sunny skies and amidst the crashing waves of the Arabian Sea lies a tropical haven that beckons all to its shores ...</p>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--                <div class="card-footer">-->
                <!--                    <span class="link">Learn more</span>-->
                <!--                </div>-->
                <!--            </a>-->
                <!--        </div>      -->
                <!--    </div>-->
                <!--</div>-->
                <div class="row justify-content-center mt-4 pt-3 d-none d-lg-flex">
                    <div class="col-auto position-relative">
                        <button class="btn p-0 swiper-outer-prev"><span class="icon-arrow-left"></span></button>
                    </div>
                    <div class="col-auto position-relative">
                        <button class="btn p-0 swiper-outer-next"><span class="icon-arrow-right"></span></button>
                    </div>
                </div>                  
            </div>
        </div>
    </section>


 <section class="section why-section">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3 align-items-center">
                    <div class="col-12 col-xxl-auto">
                        <h2>{{ $footerHomeData->title ?? "" }}</h2>
                    </div>
                    <div class="col-12 col-xxl align-self-center">
                        <p>{{ $footerHomeData->sub_title ?? "" }}</p>
                    </div>                   
                </div>
            </div>     
            
          
            <div class="why-wrapper">
                <div class="row gy-4">
                     <div class="col-12 col-xl-5">
                         <div class="why-text">

                            @if($footerHomeData->list_content && count($footerHomeData->list_content) > 0)
                            @foreach($footerHomeData->list_content as $list)
                            <div class="row gy-4">
                                <div class="col-12 col-sm-auto">
                                    <div class="why-text-icon">
                                        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="72.442" height="72.446" viewBox="0 0 72.442 72.446">
                                            <g id="Layer_1" data-name="Layer 1" transform="translate(-0.009 -0.013)">
                                            <g id="Group_19" data-name="Group 19" transform="translate(0.009 0.013)">
                                            <path id="Path_47" data-name="Path 47" d="M112.539,308.661h38.093l.607.416,9.52,13.14a1.239,1.239,0,0,1-.161,1.172l-28.567,34.3a1.208,1.208,0,0,1-1.323-.3l-28.3-34.275-.062-.653,9.582-13.381.607-.416Zm16.415,2.308H114.92l5.334,9.648Zm19.3,0H134.217l8.7,9.65Zm-7.6,10.526-9.121-9.934-9.007,9.934H140.65Zm-34.793,0h11.988l-5.188-9.5-6.8,9.5Zm39.471,0h11.988l-6.8-9.5Zm-26.168,2.339H106l22.22,26.753-9.062-26.753Zm22.513,0H121.5l10.159,29.093,10.016-29.093Zm15.5,0H144.012l-9.062,26.753,22.22-26.753Z" transform="translate(-96.534 -291.12)" fill="#00423c"/>
                                            <path id="Path_48" data-name="Path 48" d="M10.166,905.833c.333.333.626,2.411.814,3.061.789,2.736,1.7,4.29,4.452,5.343a20.391,20.391,0,0,1,2.858.9,1.262,1.262,0,0,1,0,1.769c-.533.413-2.9.843-3.874,1.351a6.59,6.59,0,0,0-3.253,4.2c-.263.812-.625,3.5-1.141,3.842a1.177,1.177,0,0,1-1.616-.231,28.25,28.25,0,0,1-.909-3.611,6.667,6.667,0,0,0-3.331-4.271c-.933-.469-3.253-.862-3.8-1.283a1.261,1.261,0,0,1,0-1.769,22.745,22.745,0,0,1,2.67-.8,6.885,6.885,0,0,0,3.978-3.478c.493-1.085,1.055-4.491,1.426-4.967a1.283,1.283,0,0,1,1.72-.064Zm-.692,5.514c-.38-.067-.226.053-.3.207-.95,1.871-2.1,3.654-4.226,4.326,1.764,1.21,3.7,2.621,4.24,4.823.38.067.226-.053.3-.207.96-1.893,2.066-3.654,4.226-4.326l-.1-.289a7.3,7.3,0,0,1-4.14-4.534Z" transform="translate(-0.009 -854.074)" fill="#e56d00"/>
                                            <path id="Path_49" data-name="Path 49" d="M996.847.313c.324.324.788,3.143,1.1,3.944a5.455,5.455,0,0,0,3.031,3.255c.7.331,2.457.652,2.823.939a1.26,1.26,0,0,1,0,1.769c-.439.34-2.292.7-3.018,1.037a5.269,5.269,0,0,0-2.837,3.157c-.339.845-.767,3.527-1.051,3.881a1.211,1.211,0,0,1-1.808-.08c-.346-.543-.613-2.752-.974-3.692a5.193,5.193,0,0,0-2.583-3.119c-.834-.432-2.827-.809-3.31-1.183a1.261,1.261,0,0,1,0-1.769c.362-.283,1.981-.561,2.626-.844a5.542,5.542,0,0,0,3.187-3.245c.366-.909.733-3.527,1.092-3.986a1.283,1.283,0,0,1,1.72-.064Zm-.692,5.369c-.34-.074-.234.045-.325.188a12.79,12.79,0,0,1-.985,1.643,8.577,8.577,0,0,1-2.2,1.753l1.17.873a6.345,6.345,0,0,1,2.048,2.852l.3-.089a6.162,6.162,0,0,1,3.206-3.421l-.091-.3a6.231,6.231,0,0,1-3.127-3.5Z" transform="translate(-931.721 -0.013)" fill="#e56d00"/>
                                            <path id="Path_50" data-name="Path 50" d="M1015.674,925.658c.32.32.755,3,1.1,3.8a4.35,4.35,0,0,0,2.205,2.326c.952.468,2.941.468,2.875,1.79-.06,1.2-1.976,1.226-2.875,1.667a4.6,4.6,0,0,0-2.454,2.955c-.247.738-.45,2.654-.8,3.106a1.172,1.172,0,0,1-2.017-.589c-.592-2.363-.643-4.381-3.2-5.575-.929-.434-2.751-.37-2.685-1.688.059-1.168,1.79-1.2,2.661-1.589,2.769-1.255,2.5-3.065,3.229-5.544a1.169,1.169,0,0,1,1.963-.659Zm-.692,5.222-.3.091a4.97,4.97,0,0,1-2.325,2.4l.092.3a4.575,4.575,0,0,1,2.248,2.619c.833-.912,1.364-2.21,2.629-2.634.193-.23-.752-.7-.945-.879a4.122,4.122,0,0,1-1.4-1.9Z" transform="translate(-950.548 -872.73)" fill="#e56d00"/>
                                            </g>
                                            </g>
                                        </svg> --}}
                                        <img src="{{ isset($list['image']) && $list['image'] ? url('storage/home/images/' . $list['image']) : '' }}" height="72.446" viewBox="0 0 72.442 72.446" alt="">
                                    </div>
                                </div>
                                <div class="col">
                                    <h3>{{ $list['list_title'] ?? "" }}</h3>
                                    <p>{{ $list['list_content'] ?? "" }}</p>
                                </div>
                            </div>
                            @endforeach
                            @endif
                            
                           


                            


                         </div>
                     </div>
                     <div class="col">
                        <div class="why-images">
                            <div class="row">
                                 <div class="col-6">
                                      <div class="cls-img overflow-hidden rounded-4">
                                          <img loading="lazy" src="{{ isset($footerHomeData->image_banner_first) && $footerHomeData->image_banner_first ? url('storage/home/images/' . $footerHomeData->image_banner_first) : '' }}" alt="">
                                      </div>
                                 </div>
                                 <div class="col-6">
                                      <div class="cls-img overflow-hidden rounded-4">
                                          <img loading="lazy" src="{{ isset($footerHomeData->image_banner_second) && $footerHomeData->image_banner_second ? url('storage/home/images/' . $footerHomeData->image_banner_second) : '' }}" alt="">
                                      </div>
                                 </div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>            
        </div>
    </section>


    <section class="section section-featured">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-12 col-xxl-3  col-xl-4">
                    <div class="section-heading mb-0">
                        <h2>Featured In</h2>
                    </div>       
                </div>
                <div class="col">
                    <div class="row align-items-center">
                        <div class="col-auto me-auto">
                           <a href="https://mediaindia.eu/tourism/interview-with-gagan-gambhir-founder-tisya-stays-goa/" target="_blank"><img loading="lazy" src="{{ asset('assets/images/mig.jpg') }}" alt=""></a>
                        </div>
                        <div class="col-auto me-auto">
                           <a href="https://www.mansworldindia.com/fashion/style/a-goan-escapade-with-tisyastays" target="_blank"><img loading="lazy" src=" {{ asset('assets/images/mw.jpg') }}" alt=""></a>
                        </div>
                        <div class="col-auto me-auto">
                           <a href="#" target="_blank"><img loading="lazy" src="{{ asset('assets/images/theman.jpg') }}" alt=""></a>
                        </div>
                        <div class="col-auto me-auto">
                           <a href="https://www.architecturaldigest.in/story/check-in-to-this-goa-homestay-that-makes-guests-feel-like-part-of-the-family/" target="_blank"><img loading="lazy" src="{{ asset('assets/images/ad.jpg') }}" alt=""></a>
                        </div>
                    </div>
                </div>                   
            </div>
        </div>
    </section>

<script>
    document.addEventListener('DOMContentLoaded', () => { 
        $(".offer-code .btn").each(function(){
            var clipboard = new ClipboardJS(this);
            clipboard.on('success', function(e) {
                //let el = e.trigger

                $(e.trigger).addClass("active");

                setTimeout(() => {
                   // console.log("el", el)
                    $(e.trigger).removeClass("active");
                }, 1000);

                e.clearSelection();
            });

        })


        const filterSwiper = new Swiper('.swiper-filter', {
            slidesPerView: "auto",
            freeMode: true,
            watchSlidesProgress: true,
        })

        const heroSwiper = new Swiper('.hero-banner-swiper', {
            loop: true,
            slidesPerView: 1,
            effect: "fade",
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
        })


        let PropertySwiperElement = document.querySelectorAll('.swiper-property')
        
        PropertySwiperElement.forEach((item, idx) => {
            var swiper = new Swiper(item, {
                spaceBetween: 32,
                grabCursor: true,
                pagination: {
                    el: ".swiper-property .swiper-pagination",
                    dynamicBullets: true,
                    clickable: true
                },
                navigation: {
                    nextEl: $(item).parent().find('.swiper-outer-next').get(0),
                    prevEl: $(item).parent().find('.swiper-outer-prev').get(0),
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                        pagination: false
                    },
                    992: {
                        slidesPerView: 2, 
                        //pagination: true,
                    },
                    1350: {
                        slidesPerView: 3,
                    },
                    1700: {
                        spaceBetween: 25,
                        slidesPerView: 4,
                    }
                }
            });
        })
        

        
        
        
        new Swiper(".swiper-collection", {
            spaceBetween: 32,
            grabCursor: true,
            pagination: {
                el: ".swiper-property .swiper-pagination",
                dynamicBullets: true,
                clickable: true
            },
            navigation: {
                nextEl: ".collections-section .swiper-outer-next",
                prevEl: ".collections-section .swiper-outer-prev",
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    pagination: false
                },
                992: {
                    slidesPerView: 2, 
                    //pagination: true,
                },
                1350: {
                    slidesPerView: 3,
                },
                1700: {
                    slidesPerView: 4,
                }
            }
        });
        
        
        new Swiper(".swiper-destinations", {
            spaceBetween: 32,
            grabCursor: true,
            pagination: {
                el: ".swiper-property .swiper-pagination",
                dynamicBullets: true,
                clickable: true
            },
            navigation: {
                nextEl: ".destinations-section .swiper-outer-next",
                prevEl: ".destinations-section .swiper-outer-prev",
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    pagination: false
                },
                992: {
                    slidesPerView: 2, 
                    //pagination: true,
                },
                1350: {
                    slidesPerView: 3,
                },
                1700: {
                    slidesPerView: 4,
                }
            }
        });


        new Swiper(".swiper-blog", {
            spaceBetween: 32,
            grabCursor: true,               
            navigation: {
                nextEl: ".blog-section .swiper-outer-next",
                prevEl: ".blog-section .swiper-outer-prev",
            },
            breakpoints: {              
                320: {
                    slidesPerView: 1,
                    pagination: false
                },
                992: {
                    slidesPerView: 2,                     
                },
                1350: {
                    slidesPerView: 3,
                },
                1700: {
                    slidesPerView: 4,
                }
            }
        });


        

        new Swiper(".swiper-property-image", {
            spaceBetween: 30,
            allowTouchMove: false,
            pagination: {
                el: ".swiper-property-image .swiper-pagination",
                dynamicBullets: true,
                clickable: true
            },  
            navigation: {
                nextEl: ".swiper-property-image .swiper-button-next",
                prevEl: ".swiper-property-image .swiper-button-prev",
            },          
            mousewheel: {
                enabled: true,  
                forceToAxis: true             
            },
        });


      






    })
</script>
@endsection