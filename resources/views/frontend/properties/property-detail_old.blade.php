@extends('layout.main')
@section('content')
    <div class="page-wrapper">
        <section class="section section-property-gallery">
            <div class="container">
                   <div class="row g-equal">
                    @if($property && $property->imagesVideos && $property->imagesVideos->isNotEmpty())
                    <div class="col-12 col-lg-6">
                        <a href="#" class="gallery-card" data-gallery-type="all">
                            <img src="{{ asset($property->imagesVideos->where('type', 'image')->first()->filename ?? 'public/assets/images/noimage-property.jpg') }}"
                                alt="Living Room">
                                <span class="seeAllPhoto btn btn-light" data-gallery-type="all" style="font-size: 13px; padding: 5px 15px">
                                    Show all photos
                                </span>
                            @if ($property->imagesVideos->first()->title ?? '')
                                <span>{{ $property->imagesVideos->first()->title ?? '' }}</span>
                            @endif
                        </a>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="row g-equal">
                            @foreach ($property->imagesVideos->where('type', 'image')->skip(1)->take(4) as $image)
                                <div class="col-6">
                                    <a href="#" class="gallery-card" data-gallery-type="all">
                                        <img src="{{ asset($image->filename ?? 'storage/home/images/noimage-property.jpg') }}"
                                            alt="Image {{ $loop->iteration }}" data-gallery-type="all">
                                           
                                        @if ($image->title)
                                            <span>{{ $image->title }}</span>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        
                            @for ($i = $property->imagesVideos->where('type', 'image')->skip(1)->take(4)->count(); $i < 4; $i++)
                                {{-- @for ($i = $property->imagesVideos->skip(1)->take(4)->count(); $i < 4; $i++) --}}
                                <div class="col-6">
                                    <a href="#" class="gallery-card" data-gallery-type="all">
                                        <img loading="lazy" src="{{ asset('assets/images/noimage-property.jpg') }}" class="w-100" alt="Image Title Goes Here"   data-gallery-type="all">
                                        <span>Default Image Title</span>
                                       
                                    </a>
                                </div>
                            @endfor
                        </div>
                        @endif
                    </div>



                </div>
                
            </div>
        </section>
        <!--- For Mobile -->
        <section class="section swiper-property-image section-mobile-gallery pt-0 mt-n2 d-md-none">
            <div class="container-fluid px-0">
                <div class="swiper swiper-gallery">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <a href="javascript:void(0)" data-gallery-type="all">
                                <img src="{{ asset('assets/images/goa1.jpg') }}" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="javascript:void(0)" data-gallery-type="all">
                                <img src="{{ asset('assets/images/goa2.jpg') }}" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="javascript:void(0)" data-gallery-type="all">
                                <img src="{{ asset('assets/images/goa3.jpg') }}" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a href="javascript:void(0)" data-gallery-type="all">
                                <img src="{{ asset('assets/images/goa4.jpg') }}" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
        <!--- For Mobile -->

        {{-- 
        detail page dynamic fields --}}
        <section class="section section-about-property pt-0">
            <div class="container">
                <div class="row g-5">
                    <div class="col-12 col-xl-8 page-detail-column order-2 order-xl-0">
                        <h1 class="text-primary fs-1 mb-3">{{ $property->home_name ?? '' }}</h1>

                        <div class="row align-items-center g-2 gx-4">
                            <div class="col-auto"><img src="{{ asset('assets/images/location.svg') }}" alt=""> <b
                                    class="text-primary">{{ $property->location ?? '' }}</b>,{{ $property->state ?? '' }}</div>
                            <div class="col-auto">
                                <ul class="nav property-short-info">
                                    <li><span class="icon-users"></span>Upto {{ $property->maximum_number_of_guests ?? '' }}
                                        Guests</li>
                                    <li><span class="icon-bed"></span>{{ $property->no_of_bedrooms ?? '' }}</li>
                                    <li><span class="icon-bath"></span>{{ $property->no_of_bathrooms ?? '' }}</li>
                                </ul>
                            </div>
                        </div>


                        <div class="detailContainer">
                            <div class="group">
                                <div class="content">
                                    {!! $property->description ?? '' !!}
                                </div>
                            </div>
                            <div class="group">
                                <h3 class="ci-title">Features:</h3>
                                <div class="content">
                                    @if ($property && $property->homeFeatures->isNotEmpty())
                                    @foreach ($property->homeFeatures as $homeFeature)
                                        <h5>{!! $homeFeature->title !!}</h5>
                                        <p>{!! $homeFeature->detail !!}</p>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="group">
                                <h3 class="ci-title">Amenities:</h3>
                                <div class="content">
                                    <ul class="amenities-list list-unstyled m-0">
                                        @if ($property && $property->amenities->isNotEmpty())
                                        @foreach ($property->amenities as $amenity)
                                            <li>
                                                <div class="amenities-small-icon">
                                                    <img src="{{ asset('storage/amenities/' . $amenity->amenities_image) }}"
                                                        alt="{{ $amenity->amenities_image }}">
                                                </div>
                                                <span>{{ $amenity->amenities_name }}</span>
                                            </li>
                                        @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            <div class="group">
                                <div class="row g-3">
                                    <div class="col-12 col-md">
                                        <a href="javascript:void(0);" data-fancybox data-src="#house-rules"
                                            class="btn btn-outline-dark bigBtn w-100">
                                            <div class="row g-2 align-items-center w-100 text-start">
                                                <div class="col-auto"><img
                                                        src="{{ asset('assets/images/house-rules.png') }}" alt="">
                                                </div>
                                                <div class="col">Home Rules</div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-12 col-md">
                                        <a href="javascript:void(0);" data-fancybox data-src="#cancellation-policy"
                                            class="btn btn-outline-dark bigBtn w-100">
                                            <div class="row g-2 align-items-center w-100 text-start">
                                                <div class="col-auto"><img
                                                        src="{{ asset('assets/images/cancellation.png') }}" alt="">
                                                </div>
                                                <div class="col">Cancellation Policy</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Location field --}}

                           <div class="group">
                                <h3 class="ci-title">Property Location:</h3>  
                                <div class="card p-2" style="border-radius:10px;">
                                    <div id="map" style="border-radius:10px;"></div>
                                </div>
                            </div>



                            {{-- Review field --}}
                        <div class="group">
    <h3 class="ci-title">Reviews:</h3>
    <div class="content mb-3">
        <!-- Filter Navigation -->
        <div class="row align-items-end mb-4">
            <div class="col">
                <div class="filter-nav">
                    <ul class="nav align-items-center">
                        <li class="active">
                            <button class="btn" data-source="all">All</button>
                        </li>
                        <li>
                            <button class="btn" data-source="tisya"><img src="{{ asset('storage/review/images/6768f7b1dbb6c_tisya.svg') }}" ></button>
                        </li>
                        <li>
                            <button class="btn" data-source="google"><img src="{{ asset('storage/review/images/6768f85c48f3e_google.png') }}" ></button>
                        </li>
                        <li>
                            <button class="btn" data-source="airbnb"><img src="{{ asset('storage/review/images/6768fa1a97658_airbnb.svg') }}" ></button>
                        </li>
                    </ul>
                </div>
            </div>
              <div class="col-auto">
                @php
                  
                    $averageRating = $property && $property->homeReviews->isNotEmpty()
                        ? $property->homeReviews->avg('rating') 
                        : 5; 
                @endphp
                
                <div class="overall-rating">
                    <strong>{{ number_format($averageRating, 1) }}</strong> 
                    {{ $averageRating >= 4 ? 'Good' : ($averageRating >= 3 ? 'Average' : 'Poor') }}
                </div>
            </div>
        </div>

        <!-- Review List -->
        <div class="swiper reviewList">
            <div class="swiper-wrapper">
                <!-- Dynamically Render Reviews -->
                @if ($property && $property->homeReviews->isNotEmpty())
                    @foreach ($property->homeReviews as $homeReview)
                        <div class="swiper-slide" data-review-site="{{ strtolower($homeReview->review_type) }}">
                            <div class="reviewBox">
                                <p>{{ $homeReview->comment }}</p>
                                <div class="row justify-content-between">
                                   <div class="col">
                                   <b>{{ $homeReview->guest_name }}</b> ({{ \Carbon\Carbon::parse($homeReview->review_date)->format('d F Y') }})
                                         </div>
                                    <div class="col-auto">
                                        <img src="{{ asset('storage/review/images/' . $homeReview->icons_image) }}" 
                                             alt="{{ $homeReview->review_type }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Swiper Navigation -->
            <div class="row justify-content-center pt-3">
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

                        
                           

                            {{-- Tags  --}}
                            <div class="group">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-auto">
                                        <h3 class="ci-title m-0">Tags:</h3>
                                    </div>
                                    <div class="col-12 col-lg">
                                        <ul class="tags">
                                            @if ($property && $property->tags->isNotEmpty())
                                             @foreach ($property->tags as $tag)
                                         <li><a href="">{{ $tag->tags_name }}</a></li>
                                               @endforeach
                                              @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-12 col-xl-4 order-1 order-xl-0">
                        <ul class="list-unstyled row g-2 share-links">
                            <li class="col-auto">
                                <a href="#" class="gallery-card btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" viewBox="2 1.7 20 20.6">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <title></title>
                                            <g id="Complete">
                                                <g id="download">
                                                    <g>
                                                        <path d="M3,12.3v7a2,2,0,0,0,2,2H19a2,2,0,0,0,2-2v-7"
                                                            fill="none" stroke="#ffffff" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"></path>
                                                        <g>
                                                            <polyline data-name="Right" fill="none" id="Right-2"
                                                                points="7.9 12.3 12 16.3 16.1 12.3" stroke="#ffffff"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"></polyline>
                                                            <line fill="none" stroke="#ffffff" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2" x1="12"
                                                                x2="12" y1="2.7" y2="14.2"></line>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    Brochure
                                </a>
                            </li>
                            @if ($property && $property->homeVideo->isNotEmpty())
                             @foreach($property->homeVideo as $videoPlay)
                        <li class="col-auto">
                            <a href="{{ $videoPlay->filename }}" data-fancybox class="btn btn-primary">

                                <svg xmlns="http://www.w3.org/2000/svg" width="18.125" height="18.125" viewBox="0 0 18.125 18.125">
                                    <g id="bxs-videos" transform="translate(0)">
                                        <path id="Path_97" data-name="Path 97" d="M5.812,16H4V26.875a1.812,1.812,0,0,0,1.812,1.812H16.687V26.875H5.812Z" transform="translate(-4 -10.563)" fill="#fff"/>
                                        <path id="Path_98" data-name="Path 98" d="M24.687,4H13.812A1.812,1.812,0,0,0,12,5.812V16.687A1.812,1.812,0,0,0,13.812,18.5H24.687A1.812,1.812,0,0,0,26.5,16.687V5.812A1.812,1.812,0,0,0,24.687,4ZM16.531,14.875V7.625l6.344,3.625Z" transform="translate(-8.375 -4)" fill="#fff"/>
                                    </g>
                                </svg>
                                Video
                            </a>
                        </li>
                        @endforeach
                        @endif
                            <li class="col-auto">
                                <a href="#" data-src="#share" data-custom-fancy data-close-button="false"
                                    class="btn border bg-transparent text-primary border-primary btn-outline-primary">
                                    <svg id="share-square-o" xmlns="http://www.w3.org/2000/svg" width="16.403"
                                        height="15.141" viewBox="0 0 16.403 15.141">
                                        <path id="Path_90" data-name="Path 90"
                                            d="M13.879,9.749V12.3a2.844,2.844,0,0,1-2.839,2.839h-8.2a2.734,2.734,0,0,1-2.006-.833A2.734,2.734,0,0,1,0,12.3V4.1A2.734,2.734,0,0,1,.833,2.095a2.734,2.734,0,0,1,2.006-.833H5.353a.32.32,0,0,1,.315.315.288.288,0,0,1-.256.315A6.588,6.588,0,0,0,4.1,2.484a.454.454,0,0,1-.158.039h-1.1a1.519,1.519,0,0,0-1.114.463A1.519,1.519,0,0,0,1.262,4.1v8.2a1.519,1.519,0,0,0,.463,1.114,1.519,1.519,0,0,0,1.114.463h8.2A1.582,1.582,0,0,0,12.618,12.3V10.193a.311.311,0,0,1,.177-.286,2.08,2.08,0,0,0,.532-.365.293.293,0,0,1,.345-.079A.3.3,0,0,1,13.879,9.749ZM16.216,4.86,12.43,8.645a.586.586,0,0,1-.444.187.685.685,0,0,1-.246-.049.589.589,0,0,1-.384-.582V6.309H9.779q-3.184,0-4.318,1.291-1.173,1.35-.729,4.663a.3.3,0,0,1-.2.335.539.539,0,0,1-.118.02.309.309,0,0,1-.256-.128q-.1-.138-.207-.306t-.389-.675q-.281-.508-.488-.981A7.84,7.84,0,0,1,2.7,9.4a4.716,4.716,0,0,1-.173-1.2q0-.483.035-.9A6.818,6.818,0,0,1,2.7,6.417a4.628,4.628,0,0,1,.276-.867,4.528,4.528,0,0,1,.463-.8,3.846,3.846,0,0,1,.675-.729,5.251,5.251,0,0,1,.932-.606,6.408,6.408,0,0,1,1.227-.478,10.47,10.47,0,0,1,1.572-.3,16.1,16.1,0,0,1,1.937-.108h1.577V.631A.589.589,0,0,1,11.74.049.685.685,0,0,1,11.987,0a.606.606,0,0,1,.444.187l3.785,3.785a.619.619,0,0,1,0,.887Z"
                                            transform="translate(0 0)" fill="#00423c" />
                                    </svg>
                                    Share
                                </a>
                            </li>
                        </ul>
                        {{-- calender --}}
                        <div class="main-search-outer detail-page-search ms-0">
                            <div class="main-search p-0">
                                <div class="row g-3 align-items-center">
                                    <div class="col-12">
                                        <div class="bh-price fw-bold">
                                            <strong>&#8377;<span class="PricePerNight"></span></strong> <small class="fw-normal">per night {{-- + taxes --}}</small>
                                        </div>
                                    </div>
                                    <div class="col-12 field-col d-flex position-relative">
                                        <button class="btn text-start btn-checkin search-field">
                                            <small>Check in</small>
                                            <div class="search-field-value js-checkin-text">{{ date('jS M', strtotime($checkInDate)) ?? 'Add dates' }}</div>
                                        </button>
                                        <button class="btn text-start btn-checkout search-field">
                                            <small>Check out</small>
                                            <div class="search-field-value js-checkout-text">{{ date('jS M', strtotime($checkOutDate)) ?? 'Add dates' }}</div>
                                        </button>
                                        <div class="custom-dropdown calendar-dropdown">
                                             <input id="detail-page-calendar" type="text" style="display:none;" value="@if($checkInDate){{ date('Y-m-d', strtotime($checkInDate)) . ' - ' . date('Y-m-d', strtotime($checkOutDate)) }}@endif""/>
                                        </div>
                                    </div>
                                    <div class="col-12 field-col position-relative">
                                        <button class="btn text-start search-field search-field-guest">
                                            <small>Guests</small>
                                            <div class="search-field-value" total-guests-detail>Add guests</div>
                                        </button>   
                                        <div class="custom-dropdown guests-counter guests-dropdown">
                                            <ul class="list-unstyled m-0">
                                                <li>
                                                    <div class="row flex-nowrap align-items-center">
                                                        <div class="col">
                                                            <div class="guests-title">
                                                                <strong>Adults</strong>
                                                                <small>Ages 18+</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="counter">
                                                                <a href="javascript:void(0)" class="btn counter-col c-minus c-minus-detail" data-type-detail="adults" data-minus-detail>
                                                                    <span class="icon-minus"></span>
                                                                </a>
                                                                {{-- <div class="counter-col">
                                                                    <input type="hidden" class="adults-count" name="" value="">
                                                                    <strong class="count-val">{{ $adult ?? 1 }}</strong>
                                                                </div> --}}



                                                                <div class="counter-col">
                                                                    <input type="hidden" class="adults-count adultsCountDetail" name="" value="{{ $adult ?? 1 }}" data-adults-value>
                                                                    <strong class="count-val count-val-detail">{{ $adult ?? 1 }}</strong>
                                                                </div>




                                                                <a href="javascript:void(0)" class="btn counter-col c-plus c-plus-detail" data-type-detail="adults" data-plus-detail>
                                                                    <span class="icon-plus"></span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="row flex-nowrap align-items-center">
                                                        <div class="col">
                                                            <div class="guests-title">
                                                                <strong>Children</strong>
                                                                <small>Ages 6 -17</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="counter">
                                                                <a href="javascript:void(0)" class="btn counter-col  c-minus c-minus-detail"  data-type-detail="children" data-minus-detail>
                                                                    <span class="icon-minus"></span>
                                                                </a>
                                                                {{-- <div class="counter-col">
                                                                    <input type="hidden" class="children-count" name="" value="">
                                                                    <strong class="count-val">{{ $child ?? 0 }}</strong>
                                                                </div> --}}

                                                                <div class="counter-col">
                                                                    <input type="hidden" class="children-count childrenCountDetail" name="" value="{{ $child ?? 0 }}" data-children-value>
                                                                    <strong class="count-val count-val-detail">{{ $child ?? 0 }}</strong>
                                                                </div>

                                                                <a href="javascript:void(0)" class="btn counter-col c-plus c-plus-detail" data-type-detail="children" data-plus-detail>
                                                                    <span class="icon-plus"></span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>                        
                                            </ul>
                                        </div>
                                    </div>  
                                    
                                    <div class="col-12">
                                        <div class="table-subtotal">
                                            <table class="table table-sm mb-0 table-borderless">
                                                <tr class="first-tr">
                                                    <td>&#8377;<span class="PricePerNight"></span> x <span class="totalNight"></span> nights</td>
                                                    <td align="right">&#8377;<span class="PriceWithPerNight"></span></td>
                                                </tr>
                                                <tr class="second-tr">
                                                    <td> Extra charge (<span class="extraGuestCharge"></span>
                                                             x <span class="totalNight"></span> )</td>
                                                    <td align="right">&#8377; <span class="totalExtraGuestCharge"></span></td>
                                                </tr>
                                                <tr>
                                                    <td>Taxes (<span class="tax">{{ $tax ?? '' }}</span>%)</td>
                                                    <td align="right">&#8377; <span class="taxAmount"></span></td>
                                                </tr>
                                                <tr class="fw-bold">
                                                    <td>Total incl. taxes</td>
                                                    <td align="right">&#8377;<span class="TotalAmount"></span></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <button class="btn w-100 border bg-transparent text-primary py-2 small border-primary btn-outline-primary">Check offers and discounts</button>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn py-3 fw-bold w-100 btn-primary">Book Now</button>
                                    </div>
                        
                                    <div class="col-12">
                                        <a href="#" class="note-link">Booking & Cancellation Policy</a>
                                    </div>
                        
                        
                                </div>
                            </div>
                        </div>
                        
                       {{-- calendar --}}
                    </div>

                </div>
            </div>
        </section>


    </div>

    <div class="mobile-reserve-wrap">
        <div class="container">
            <div class="row">
                <div class="col">
                    {{-- <div class="m-booking-info">
                    <strong>₹<span class="TotalAmount">7,116</span></strong> incl. taxes <br>
                    <ul class="list-unstyled mb-0 bi-info search-link">
                        <li class="date-text">14th Dec - 15th Dec</li>
                        <li><span class="totalNight">2</span> nights  <button class="btn btn-edit"></button></li>
                    </ul>                    
                </div> --}}
                </div>
                {{-- <div class="col-auto">
                <a href="javascript:void(0)" class="btn w-100 btn-primary px-4 make-reservation">Book Now</a>
            </div> --}}
            </div>
        </div>
    </div>

    <div id="house-rules" class="popup">
        <p>{!! $property->house_rules ?? '' !!}</p>
    </div>
    <div id="cancellation-policy" class="popup">
        <p>{!! $property->cancellation_policy ??
            '' !!}</p>
    </div>

    <div class="section-fancybox full-fanybox" id="share" style="display: none;max-width:480px;">
        <div class="row">
            <div class="col-12">
                <div class="fancy-heading">
                    <div class="row">
                        <div class="col">
                            <h3>Share this place</h3>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0)" class="fancy-close" onclick="Fancybox.close()">
                                <i class="icon-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <ul class="list-unstyled share-link-list m-0">
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=https://example.com&amp;t=Title"
                            target="_blank">
                            <svg style="width: 20px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                <path
                                    d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" />
                            </svg>Facebook</a>
                    </li>
                    <li>
                        <a href="https://twitter.com/intent/tweet?url=https://example.com" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
                            </svg>Twitter X</a>
                    </li>

                    <li>
                        <a href="https://wa.me/?text=https://example.com" data-action="share/whatsapp/share"
                            target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path
                                    d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                            </svg>WhatsApp</a>
                    </li>
                    <li><a href="mailto:?body=https://example.com&amp;subject=Title"><svg
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M64 112c-8.8 0-16 7.2-16 16l0 22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1l0-22.1c0-8.8-7.2-16-16-16L64 112zM48 212.2L48 384c0 8.8 7.2 16 16 16l384 0c8.8 0 16-7.2 16-16l0-171.8L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64l384 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64L0 128z" />
                            </svg>Email</a></li>
                </ul>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
           let parentElClass = '.detail-page-search';    
           let inputCalendar = document.getElementById('detail-page-calendar');
           function resetDateInput(){
             $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
             datepickerHero.clear();
           }        
           window.datepickerHero = new HotelDatepicker(inputCalendar, {
               inline: true,
               moveBothMonths: true,           
               // clearButton: true,
               // minNights: 4,
               clearButton: true,
               showTopbar: true,
               topbarPosition: 'bottom',
               disabledDates: JSON.parse('{!! json_encode($propertyUnavailableDates) !!}'),
               onSelectRange: function() {
                   let startDate = fecha.format(this.start, `Do MMM`);
                   let endDate = fecha.format(this.end, `Do MMM`);
                   $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
                   $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);   
                   //calendarBtn.toggle();             
               } ,
               onDayClick: function() {    
                   this.minNights = 3
                   let currentDate = new Date(fecha.format(this.start, `YYYY-MM-DD`))
                   let previousDates = [];
                   console.log(this)
                   if(this.start){
                       //$(".btn-end-date span").text("Departure");
                       let startDate = fecha.format(this.start, `Do MMM`);
                       $(parentElClass).find(".js-checkin-text").addClass('hasValue').text(startDate).parent().removeClass('active');
                       $(parentElClass).find(".js-checkout-text").parent().addClass('active');
                   }
                   if(this.end){
                       let endDate = fecha.format(this.end, `Do MMM`);
                       $(parentElClass).find(".js-checkout-text").addClass('hasValue').text(endDate); 
                   }
                   if(this.start && this.end){
                       
                       let days = datepickerHero.getNights();
                       ci_date= fecha.format(this.start, `YYYY-MM-DD`);
                       co_date= fecha.format(this.end, `YYYY-MM-DD`);
                      // tot_guest= $('#totalGuests').val();
                       tot_no_of_days= days;
                       getPropertyPrice();
                       $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
                   }
                   if(!this.start && !this.end){
                       resetDateInput()
                   }
               }         
           });
           $(datepickerHero.datepicker).find(".datepicker__clear-button").text("Clear Dates")
           $(datepickerHero.datepicker).find(".datepicker__buttons").append('<button type="button" class="btn btn-link close-datepicker">Close</button>');
           $(document).on("click",".close-datepicker", function(){
               $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
           })
   
           $(".clear-dates").on("click",function(e){
               e.stopPropagation();
               resetDateInput();
           });
   
        
   
           let mm = gsap.matchMedia();
   
           mm.add(
           "(min-width: 1300px)", function () {
               ScrollTrigger.create({
                   trigger: ".detail-page-search",
                   start:()=>`top 120px`, 
                   pin: true,     
                   // markers: true,
                   end: "bottom bottom",
                   endTrigger: ".page-detail-column"
               });
           });

let adults = "@php if(isset($adult)){ echo $adult; }else{ echo 1;} @endphp";
let children = "@php if(isset($child)){ echo $child; }else{ echo 0;} @endphp";
let tot_guest = "@php echo $totGuest; @endphp";
let tot_no_of_days = "@php echo $no_of_nights; @endphp";
let ci_date = "@php echo $checkInDate; @endphp";
let co_date = "@php echo $checkOutDate; @endphp";
let propertyId = "{{ $property->ru_property_id  }}";
//let adults = parseInt(adults);
//let children = parseInt(children);
let adultsCount = parseInt(adults);
let childrenCount = parseInt(children);
    initializeGuestCounterDetail();
    $(document).on('click', '[data-type-detail]', function () {
        let dataTypeDetail = $(this).data('type-detail');
        counterdetail($(this), dataTypeDetail);
    });
    function initializeGuestCounterDetail() {
        let adultsCountDetail = $('.adultsCountDetail').val() || 1; 
        let childrenCountDetail = $('.childrenCountDetail').val() || 0;
        $('.adultsCountDetail').val(adultsCountDetail);
        $('.childrenCountDetail').val(childrenCountDetail);
        $('.adultsCountDetail').parent().find('.count-val-detail').text(adultsCountDetail);
        $('.childrenCountDetail').parent().find('.count-val-detail').text(childrenCountDetail);
        $('.adultsCountDetail').val() > 1 
            ? $('[data-type-detail="adults"][data-minus-detail]').removeClass('disabled') 
            : $('[data-type-detail="adults"][data-minus-detail]').addClass('disabled');
        updateTotalGuestsDetail();
    }

    function counterdetail(el, dataTypeDetail) {
        let inputDetailEl = el.parent().find(`input[data-${dataTypeDetail}-value]`);
        let minusEl = el.parent().find('[data-minus-detail]');
        let plusEl = el.parent().find('[data-plus-detail]');

        let inputValue = parseInt(inputDetailEl.val()) || 0;
        if (el.hasClass('c-plus-detail')) {
            if (dataTypeDetail === 'children' && inputValue >= 2) return; 
            inputValue++;
        } else {
            inputValue--;
        }
        inputValue = Math.max(0, inputValue); 
        if (dataTypeDetail === 'adults' && inputValue < 1) {
            inputValue = 1; 
        }
        inputDetailEl.val(inputValue);
        el.parent().find('.count-val-detail').text(inputValue);
        if (dataTypeDetail === 'adults') {
            inputValue > 1 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
        } else {
            inputValue > 0 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
        }
        if (dataTypeDetail === 'children') {
            inputValue >= 2 ? plusEl.addClass('disabled') : plusEl.removeClass('disabled');
        }
        updateTotalGuestsDetail();
    }
    function updateTotalGuestsDetail() {
         adultsCount = $('.adultsCountDetail').val() || 0;
         childrenCount = $('.childrenCountDetail').val() || 0;
        let totalGuest = tot_guest = Number(adultsCount) + Number(childrenCount);
        const totalGuestInputText = totalGuest === 0 
            ? 'Add guests' 
            : totalGuest > 1 
            ? `${totalGuest} Guests` 
            : `${totalGuest} Guest`;

        $('[total-guests-detail]').text(totalGuestInputText);
        getPropertyPrice();
    }


    function getPropertyPrice(){
        $.ajax({
            url: "get/ajax/property/price",
            type: 'get',
            headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
            data: {
                adults:adultsCount,
                children:childrenCount,
               // all_total_guests:all_total_guests,
                checkin_date: ci_date,
                checkout_date: co_date,
                propertyId: propertyId,
                tot_guest: tot_guest,
                tot_no_of_days: tot_no_of_days
            },
            success: function(res) {
                //$('.totalPrice').text(res.data.formatted_base_price)
                $('.PricePerNight').text(res.data.price_per_night_num_formatted)
                $('.PriceWithPerNight').text(res.data.total_price_multiple)
                $('.tax').text(res.data.tax)
                $('.taxAmount').text(res.data.formatted_total_taxable_amount)
                $('.TotalAmount').text(res.data.num_formatted_tot_price)
                $('.totalNight').text(tot_no_of_days)
                $('.totalExtraGuestCharge').text(res.data.total_extra_guest_charge)
                $('.second-tr').hide();
                if (res.data && res.data.total_extra_guest_charge !== 0) {
                    $('.second-tr').show();
                }
                $('.extraGuestCharge').text(res.data.extra_guest_charge)

                amountBeforeTax = res.data.amountBeforeTax + res.data.total_additional_charges;
                tax = initialTax =  res.data.tax;
                tax_amount = initialTaxAmount =  taxAmount =  res.data.tax_amount;
                totalPayableAmount = totalPayableInitialAmount =  amountBeforeTax + Math.round(tax_amount) ;
                
                console.log(totalPayableAmount,"totalPayableAmount");

                price_per_night = initial_price_per_night =  res.data.per_night_price;
                formatted_total_taxable_amount = res.data.formatted_total_taxable_amount;
                additionalCharges = res.data.additionalCharges;
                additionalChargesAmount = res.data.total_additional_charges;
                let additionalChargesTr = '';
                if(additionalCharges && additionalCharges.length > 0){
                    additionalCharges.forEach(item => {
                        if(item.type_option =='Per_Stay'){
                            additionalChargesTr = additionalChargesTr+'<tr class ="additional-tr"><td>'+item.name+'</td><td align="right">&#8377;<span>'+item.price+'</span></td></tr>';

                        }
                        else{
                            additionalChargesTr = additionalChargesTr+'<tr class ="additional-tr"><td>'+item.name+'</td><td align="right">&#8377;<span>'+item.price*tot_no_of_days+'</span></td></tr>';
                        }
                        
                    });
                }
                $(".additional-tr").remove();
                $(additionalChargesTr).insertAfter('.first-tr');


            },
            error: function(res) {
            }
        });
    }


});

       


   </script>


    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBadtfvHfxj3uAeNivR0Prec9tEWQUZoX0&loading=async&callback=initMap">
    </script>
    
    <!--image-->
   <script>
        let baseUrl = "{{ URL('/') }}";
        @if($property && $property->imagesVideos)
            let images = JSON.parse('{!! json_encode($property->imagesVideos->where("type", "image")) !!}');
        @else
            let images = [];
        @endif
        let sliders = Object.entries(images);
        let slideData = []
        if (sliders.length != 0) {
            sliders.forEach(item => {
                slideData.push({
                    src: baseUrl + '/' + item[1].filename
                })
            });
        }
        let galleryImages = {
            all: slideData
        }
        document.addEventListener("DOMContentLoaded", function() {
            Fancybox.bind('[data-fancybox]', {});

            Fancybox.bind("[data-custom-fancy]", {
                hideScrollbar: true,
                closeButton: false,
            })

            const mobileGallery = new Swiper('.swiper-gallery', {
                spaceBetween: 0,
                slidesPerView: 1,
                grabCursor: true,
                freeMode: true,
                pagination: {
                    el: ".section-mobile-gallery .swiper-pagination",
                    dynamicBullets: true,
                },
            })

     

            $(document).on("click", '[data-gallery-type]', function(e) {
                e.preventDefault();
                let galType = $(this).attr('data-gallery-type');
                let index = $(this).parents('.section-property-gallery').find('[data-gallery-type]').index(
                    this);


                // console.log(galleryImages[galType][index])
                Fancybox.show(galleryImages[galType], {
                    Thumbs: false,
                    startIndex: galleryImages[galType][index] ? index : 0,
                    mainClass: "gallery-popup",
                    Toolbar: {
                        display: {
                            left: ["infobar"],
                            middle: [],
                            right: ["close"],
                        },
                    },
                    Images: {
                        initialSize: "fit",
                    },
                    on: {
                        reveal: (fancybox, slide) => {

                        },
                    },

                });
            })




        })

      

    </script>
    
    <!--review-->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const reviewSlide = new Swiper('.reviewList', {
        spaceBetween: 30,
        slidesPerView: 2,
        grabCursor: true,
        observer: true,
        observeParents: true,
        navigation: {
            nextEl: ".swiper-outer-next",
            prevEl: ".swiper-outer-prev",
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
            },
            992: {
                slidesPerView: 2,
            },
        },
    });

    const filterButtons = document.querySelectorAll(".filter-nav .btn");
    const reviews = document.querySelectorAll(".reviewList .swiper-slide");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            const source = this.getAttribute("data-source");

            // Update active button
            filterButtons.forEach(btn => btn.parentElement.classList.remove("active"));
            this.parentElement.classList.add("active");

            // Filter reviews based on the source
            reviews.forEach(slide => {
                const reviewSite = slide.getAttribute("data-review-site");
                if (source === "all" || reviewSite === source) {
                    slide.style.display = ""; // Show the slide
                } else {
                    slide.style.display = "none"; // Hide the slide
                }
            });

            // Update Swiper instance after filtering
            reviewSlide.update();
        });
    });
});

    </script>




    {{-- Map --}}
    <script>

         let mapStyles = [
            {
                "featureType": "administrative",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#444444"
                    }
                ]
            },
            {
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [
                    {
                        "color": "#fefefe"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "all",
                "stylers": [
                    {
                        "saturation": -100
                    },
                    {
                        "lightness": "6"
                    },
                    {
                        "color": "#cce6e0"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels",
                "stylers": [
                    {
                        "color": "#716e6e"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "weight": "0.01"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "weight": "0.01"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "simplified"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "transit",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "all",
                "stylers": [
                    {
                        "color": "#cddae5"
                    },
                    {
                        "visibility": "on"
                    }
                ]
            }
        ]
      
        window.initMap = function(){
            const location = { lat: 15.55967851741905, lng: 73.75130487361152 }; 
            // Create the map, centered at the location
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: location,
                styles: mapStyles,
                disableDefaultUI: true
            });

            
            
            // const circle = new google.maps.Circle({
            //     strokeColor: "#76B879",
            //     // strokeOpacity: 0.8,
            //     strokeWeight: 1,
            //     fillColor: "#76B879",
            //     fillOpacity: 0.2,
            //     map: map,
            //     center: location,
            //     radius: 100,
            // });

            // Add an icon in the center
            const iconMarker = new google.maps.Marker({
                position: location,
                map: map,
                icon: {
                     
                url: "/assets/images/map.svg",
                scaledSize: new google.maps.Size(46, 46),
                anchor: new google.maps.Point(12, 20)
                },
            });

        }
    </script>
@endsection
