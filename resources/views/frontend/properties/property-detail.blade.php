@extends('layout.main')
@section('content')
<style>
    .filter-nav ul li .btn img {
        max-width: 70px;
        object-fit: contain;
    }
</style>
    <div class="page-wrapper">
       
        
        @if($property->images->isNotEmpty()) 
       <section class="section section-property-gallery d-none d-md-block pt-4">
    <div class="container">
        <div class="row g-equal-detail">
            <!-- First Image -->
            <div class="col-12 col-lg-6">
                <a href="#" class="gallery-card" data-gallery-type="all">
                    @php $firstImage = $property->images->first(); @endphp
                    @if($firstImage)
                        <img src="{{ asset($firstImage->medium_image ?? 'assets/images/noimage-property.jpg') }}" alt="Property Image">
                    @else
                        <img src="{{ asset('assets/images/noimage-property.jpg') }}" alt="No Image Available">
                    @endif
                </a>
            </div>

            <!-- Middle and Last Images -->
            <div class="col-12 col-lg-6">
                <div class="row g-equal-detail">
                    @php
                        $displayedImageIds = []; // Track displayed image IDs
                        $firstImageId = $firstImage ? $firstImage->id : null;
                        $lastImage = $property->images->last();
                        $lastImageId = $lastImage ? $lastImage->id : null;
                        $displayCount = 0; // Track middle images displayed
                    @endphp

                    <!-- Middle Images -->
                    @foreach($property->images as $image)
                        @if($image->id != $firstImageId && $image->id != $lastImageId && $displayCount < 3)
                            <div class="col-6">
                                <a href="#" class="gallery-card" data-gallery-type="all">
                                    <img src="{{ asset($image->medium_image ?? 'assets/images/noimage-property.jpg') }}" alt="Property Image">
                                </a>
                            </div>
                            @php
                                $displayedImageIds[] = $image->id;
                                $displayCount++;
                            @endphp
                        @endif
                    @endforeach

                    <!-- Last Image -->
                    <div class="col-6">
                        <div class="gallery-card">
                            @if($lastImage && !in_array($lastImageId, $displayedImageIds))
                                <img src="{{ asset($lastImage->medium_image ?? 'assets/images/noimage-property.jpg') }}" data-gallery-type="all" alt="Property Image">
                                <span class="seeAllPhoto btn btn-light" data-gallery-type="all" style="font-size: 13px; padding: 5px 15px">
                                    Show all photos
                                </span>
                                @php $displayedImageIds[] = $lastImage->id; @endphp
                            @else
                                <img src="{{ asset('assets/images/noimage-property.jpg') }}" data-gallery-type="all" alt="No Image Available">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif 
        
        <!--- For Mobile -->
               @if($property->images->isNotEmpty())
        <section class="section swiper-property-image section-mobile-gallery pt-0 pb-4 mt-n2 d-md-none">
            <div class="container-fluid px-0">
                <div class="swiper swiper-gallery">
                    <div class="swiper-wrapper">
                        @if($property->images->isNotEmpty())
                            @foreach($property->images as $image)
                                <div class="swiper-slide">
                                    <a href="javascript:void(0)" data-gallery-type="all">
                                        <img src="{{ asset($image->medium_image ?? '') }}" alt="">
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="swiper-slide">
                                <a href="javascript:void(0)" data-gallery-type="all">
                                    <img src="{{ asset('assets/images/noimage-property.jpg') }}" alt="">
                                </a>
                            </div>
                        @endif

                        
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
            @endif

        <!--- For Mobile -->

        {{-- 
        detail page dynamic fields --}}
              <section class="section section-about-property {{ $property->images->isNotEmpty() ? 'pt-0' : '' }}">
            <div class="container">
                <div class="row gx-5">
                    <div class="col-12 col-xl-8 page-detail-column order-2 order-xl-0">
                        <!--<h1 class="text-primary fs-1 mb-3">{{ $property->home_name ?? '' }}</h1>-->



                        <div class="row mb-3 align-items-center">
                            <div class="col">
                                <h1 class="text-primary fs-1 mb-0">{{ $property->home_name ?? '' }}</h1>
                            </div>
                             @if ($property && $property->homeReviews->isNotEmpty())
                               
                                <div class="col-auto">
                                    @php
                                        $averageRating = $property->homeReviews->avg('rating') ?? 5;
                                    @endphp
                                    <div class="overall-rating">
                                        <strong>{{ number_format($averageRating, 1) }}/5</strong>
                                        <span>({{ $totalReviews }})</span>
                                    </div>
                                </div>
                            @endif
                        </div>
        






                        <div class="row align-items-center g-2 gx-4">
                            <div class="col-auto"><img src="{{ asset('assets/images/location.svg') }}" alt=""> <b
                                    class="text-primary">{{ $property->location ?? '' }}</b>, {{ $property->state ?? '' }}</div>
                            <div class="col-auto">
                                <ul class="nav property-short-info">
                                    <li><span class="icon-users"></span>Upto {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }}
                                        </li>
                                    <li><span class="icon-bed"></span>{{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Room' : $property->no_of_bedrooms . ' Rooms' }}</li>
                                    <li><span class="icon-bath"></span>{{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}</li>
                                </ul>
                            </div>
                        </div>


                        <div class="detailContainer">
                            <div class="group">
                                <div class="content js-read-smore" data-read-smore-chars="450" >
                                    {!! $property->description ?? '' !!}
                                </div>
                            </div>
                            @if ($property && $property->homeFeatures->isNotEmpty())
                            <div class="group">
                                <h3 class="ci-title">Features:</h3>
                                <div class="contentt js-read-smore" data-read-smore-words="15">
                                    
                                    @foreach ($property->homeFeatures as $homeFeature)
                                        <h5>{!! $homeFeature->title !!}</h5>
                                        <p>{!! $homeFeature->detail !!}</p>
                                    @endforeach
                                    
                                </div>
                            </div>
                            @endif
                            @if ($property && $property->amenities->isNotEmpty())
                            <div class="group">
                                <h3 class="ci-title">Amenities:</h3>
                                <div class="content">
                                    <ul class="amenities-list list-unstyled m-0">
                                        
                                        @foreach ($property->amenities as $amenity)
                                            <li>
                                                <div class="amenities-small-icon">
                                                    <img src="{{ asset('storage/amenities/' . $amenity->amenities_image) }}"
                                                        alt="{{ $amenity->amenities_image }}">
                                                </div>
                                                <span>{{ $amenity->amenities_name }}</span>
                                            </li>
                                        @endforeach
                                       
                                    </ul>
                                </div>
                            </div>
                             @endif

                            <div class="group">
                                <div class="row g-3">
                                     @if(!empty($property->house_rules))
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
                                      @endif
                                     @if(!empty($property->cancellation_policy))
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
                                        @endif
                                </div>
                            </div>

                            {{-- Location field --}}

                           <div class="group">
                                <h3 class="ci-title">Property Location:</h3>  
                                <div class="card p-2" style="border-radius:10px;">
                                    <div id="map" style="border-radius:10px;"></div>
                                </div>
                            </div>

                            @if (!empty($property->location_info))
                                <div class="group">
                                    <div class="content">
                                        {!! $property->location_info ?? '' !!}
                                    </div>
                                </div>
                            @endif


                            {{-- Review start  --}}
                         @if ($property && $property->homeReviews->isNotEmpty())
                            <div class="group">
                                <h3 class="ci-title">Reviews:</h3>
                                <div class="content mb-3">
                                    <div class="row align-items-end mb-4">
                                        <div class="col">
                                            <div class="filter-nav">
                                                <ul class="nav align-items-center">
                                                    <li class="active"><button class="btn" data-source="all">All</button></li>
                                                    <li><button class="btn" data-source="Tisya"><img src="{{ asset('assets/images/tisya.svg') }}" alt=""></button></li>
                                                    <li><button class="btn" data-source="Google"><img src="{{ asset('assets/images/google.svg') }}" alt=""></button></li>
                                                    <li><button class="btn" data-source="Airbnb"><img src="{{ asset('assets/images/airbnb.svg') }}" alt=""></button></li>
                                                    <li><button class="btn" data-source="MakeMyTrip"><img src="{{ asset('assets/images/makemytrip.png') }}" alt=""></button></li>
                                                    <li><button class="btn" data-source="Booking.com"><img src="{{ asset('assets/images/booking.com.png') }}" alt=""></button></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            @php
                                                $averageRating = $property->homeReviews->avg('rating') ?? 5;
                                            @endphp
                                            <div class="overall-rating">
                                                <strong>{{ number_format($averageRating, 1) }}/5</strong>
                                                <span>({{ $totalReviews }})</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper reviewList">
                                        <div class="swiper-wrapper">
                                            @foreach ($property->homeReviews->sortByDesc('review_date') as $homeReviewsKey => $homeReview)
                                                <div class="swiper-slide" data-review-site="{{ $homeReview->reviewImages->review_name  ?? '' }}">
                                                    <div class="reviewBox">
                                                        <p>{{ Str::limit($homeReview->comment, 100, '') }}
                                                            @if (Str::length($homeReview->comment) > Str::length(Str::limit($homeReview->comment, 100, '')))
                                                                <span class="text-primary" style="cursor: pointer;" data-src="#r{{$homeReviewsKey}}" data-type="clone" data-fancybox><u>Read More</u></span>
                                                            @endif
                                                        </p>
                                                        <div class="row justify-content-between">
                                                            <div class="col">
                                                                <b>{{ $homeReview->guest_name ?? '' }}</b> 
                                                                ({{ \Carbon\Carbon::parse($homeReview->review_date)->diffForHumans() }})
                                                            </div>
                                                            <div class="col-auto">
                                                                 @if($homeReview->reviewImages && $homeReview->reviewImages->review_image)
                                                                <img src="{{ asset('storage/review/images/' . $homeReview->reviewImages->review_image) }}" alt="">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="review-full-content" id="r{{$homeReviewsKey}}" style="display:none; max-width: 600px;">
                                                            <p class="pe-5">{{ $homeReview->comment }}</p>
                                                            <div class="row justify-content-between">
                                                                <div class="col">
                                                                    <b>{{ $homeReview->guest_name ?? '' }}</b> 
                                                                    ({{ \Carbon\Carbon::parse($homeReview->review_date)->diffForHumans() }})
                                                                </div>
                                                                <div class="col-auto">
                                                                     @if($homeReview->reviewImages && $homeReview->reviewImages->review_image)
                                                                    <img src="{{ asset('storage/review/images/' . $homeReview->reviewImages->review_image) }}" alt="">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
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
                        @endif
                            {{-- Review end  --}}


                            {{-- Tags  --}}
                            @if ($property && $property->tags->isNotEmpty())
                            <div class="group">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-auto">
                                        <h3 class="ci-title m-0">Tags:</h3>
                                    </div>
                                    <div class="col-12 col-lg">
                                        <ul class="tags">
                                            @if ($property && $property->tags->isNotEmpty())
                                             @foreach ($property->tags as $tag)
                                         <li><a href="{{ route('tag-property-list', ['tag_name' => $tag->tags_name ?? '']) }}">{{ $tag->tags_name }}</a></li>
                                               @endforeach
                                              @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                           @endif

                        </div>
                    </div>
                    <div class="col-12 col-xl-4 order-1 order-xl-0">
                        <ul class="list-unstyled row g-2 share-links">
                            @if($property->pdf_full_path)
                            <li class="col-auto">
                                <a href="{{ $property->pdf_full_path ?? '' }}" download class="gallery-card btn btn-primary">
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
                            @endif
                       @if ($property->imagesVideo->isNotEmpty())
                        @php
                      $firstVideo = $property->imagesVideo->first(); 
                                @endphp
                             @if ($firstVideo)
                   <li class="col-auto">
            <a  href="{{ asset($firstVideo->filename) }}" data-fancybox class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="18.125" height="18.125" viewBox="0 0 18.125 18.125">
                    <g id="bxs-videos" transform="translate(0)">
                        <path id="Path_97" data-name="Path 97" d="M5.812,16H4V26.875a1.812,1.812,0,0,0,1.812,1.812H16.687V26.875H5.812Z" transform="translate(-4 -10.563)" fill="#fff"/>
                        <path id="Path_98" data-name="Path 98" d="M24.687,4H13.812A1.812,1.812,0,0,0,12,5.812V16.687A1.812,1.812,0,0,0,13.812,18.5H24.687A1.812,1.812,0,0,0,26.5,16.687V5.812A1.812,1.812,0,0,0,24.687,4ZM16.531,14.875V7.625l6.344,3.625Z" transform="translate(-8.375 -4)" fill="#fff"/>
                    </g>
                </svg>
                Video
            </a>
        </li>
                 @endif
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
                            <li class="col-auto offer-code h-auto">
                                <a href="javascript:void(0)" data-clipboard-text="{{url()->full()}}" class="btn border bg-transparent text-primary border-primary btn-outline-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="#00423c" d="M384 336l-192 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l140.1 0L400 115.9 400 320c0 8.8-7.2 16-16 16zM192 384l192 0c35.3 0 64-28.7 64-64l0-204.1c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1L192 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64L0 448c0 35.3 28.7 64 64 64l192 0c35.3 0 64-28.7 64-64l0-32-48 0 0 32c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l32 0 0-48-32 0z"/></svg>
                                    Copy Link
                                </a>
                            </li>
                        </ul>
                        {{-- calender --}}
                        <div class="main-search-outer detail-page-search ms-0">
                            <span role="button" class="d-xl-none close-btn lh-1 fs-1 fw-light ps-3"><i class="icon-close"></i></span>
                            <div class="main-search p-0">
                                <div class="row gx-3 gy-2 align-items-center">
                                    <div class="col-12">
                                        <div class="bh-price fw-bold">
                                            <strong>&#8377;<span class="PricePerNight"></span></strong> <small class="fw-normal">per night {{-- + taxes --}}</small>
                                        </div>
                                    </div>
                                    <div class="col-12 field-col d-flex position-relative">
                                        <button class="btn text-start btn-checkin search-field">
                                            <small>Check in</small>
                                            <div class="search-field-value js-checkin-text">{{ $isBookingEnable ? (date('jS M', strtotime($checkInDate)) ?? 'Add dates') : 'Add dates' }}</div>
                                        </button>
                                        <button class="btn text-start btn-checkout search-field">
                                            <small>Check out</small>
                                            <div class="search-field-value js-checkout-text">{{ $isBookingEnable ? (date('jS M', strtotime($checkOutDate)) ?? 'Add dates') : 'Add dates' }}</div>
                                        </button>
                                        <div class="custom-dropdown calendar-dropdown">
                                            <!--<input id="detail-page-calendar" type="text" style="display:none;" value="@if($checkInDate && $checkOutDate){{ date('Y-m-d', strtotime($checkInDate)) . ' - ' . date('Y-m-d', strtotime($checkOutDate)) }}@endif" />-->
                                            @if($isBookingEnable && $checkInDate && $checkOutDate)
                                                <input id="detail-page-calendar" type="text" style="display:none;" value="{{ date('Y-m-d', strtotime($checkInDate)) . ' - ' . date('Y-m-d', strtotime($checkOutDate)) }}" />
                                            @else
                                                <input id="detail-page-calendar" type="text" style="display:none;" />
                                            @endif

                                       
                                       
                                        </div>
                                    </div>
                                    <div class="col-12 field-col position-relative">
                                        <button class="btn text-start search-field search-field-guest">
                                            <small>Guests</small>
                                            <div class="search-field-value" total-guests-detail>Add guests</div>
                                        </button>   
                                        <div class="custom-dropdown guests-counter guests-dropdown pStatic">
                                            <ul class="list-unstyled m-0">
                                                <li>
                                                    <div class="row flex-nowrap align-items-center">
                                                        <div class="col">
                                                            <div class="guests-title">
                                                                <strong>Adults</strong>
                                                                <small>Ages 11+</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="counter">
                                                                <a href="javascript:void(0)" class="btn counter-col c-minus c-minus-detail" data-type-detail="adults" data-minus-detail>
                                                                    <span class="icon-minus"></span>
                                                                </a>
                                                                
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
                                                                <small>Ages 0-10</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="counter">
                                                                <a href="javascript:void(0)" class="btn counter-col  c-minus c-minus-detail"  data-type-detail="children" data-minus-detail>
                                                                    <span class="icon-minus"></span>
                                                                </a>
                                                                
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
                                                    <td> Extra charge (<span class="extraGuestCharge"></span> x <span class="totalNight"></span>)</td>
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
                                     @if($property->only_for_enquiry ==0)
                                    <div class="col-12">
                                        <button class="btn w-100 border bg-transparent text-primary py-2 small border-primary btn-outline-primary" data-src="#coupon" data-custom-fancy id="coupon-href">Check offers and discounts</button>
                                    </div>
                                    @endif

                                    <!---------------Discount--------------->
                            
                             
                                @csrf
                                @if($property->only_for_enquiry ==0)
                                    <div class="section-fancybox half-fancybox mxw-600" id="coupon" style="display: none;">
                                     <form action="#">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="fancy-heading">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h3>Apply Offer Code</h3>
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
                                                <div class="row g-4">
                                                    <div class="col-12">
                                                        <div class="form-group cs-input">
                                                            <label for="">Enter your Offer code</label>
                                                            <input type="hidden" name="discount_amount" id="discount_amount"/>
                                                            <input type="hidden" name="discount" id="discount"/>
                                                            <input type="text" class="form-control text-uppercase" name="coupon_code" id="coupon_code">
                                                        </div>
                                                        <span class="error justify-content-center coupon_error " ></span>
                                                    </div>
                                    
                                                    <div class="col-12">
                                                        <div class="form-group cs-input bottom-toolbar">
                                                            <div class="row align-items-center">
                                                                <div class="col-auto d-md-none">
                                                                    <a href="javascript:void(0)" class="clear-btn">Clear</a>
                                                                </div>
                                                                <div class="col text-end">
                                                                    <button type="submit" class="btn btn-primary icon-link icon-link-hover w-md-100 apply_coupon" disabled>Apply <span class="d-none d-md-block"><i class="bi icon-chevron-right"></i></span></button> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>  
                                    </div>
                                @endif    
                            
                          
                            


                             
                                    <div class="col-12">
                                        <form action="{{ route('website.customer.property.book') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $property->id }}">
                                            <!--<input type="hidden" name="checkin_date" value="{{ $checkInDate }}">-->
                                            <!--<input type="hidden" name="checkout_date" value="{{ $checkOutDate }}">-->
                                            
                                            <input type="hidden" name="slug" value="{{ $property->url_key }}">
                                            <input type="hidden" name="location_name" value="{{ $location_name }}">
                                            <input type="hidden" name="checkin_date" value="{{ $checkin_date }}">
                                            <input type="hidden" name="checkout_date" value="{{ $checkout_date }}">
                                            <input type="hidden" name="city_id" value="{{ $city_id }}">
                                            <input type="hidden" name="total_guests" value="{{ $total_guests }}">
                                            <input type="hidden" name="adultsCount" value="{{ $adultsCount }}">
                                            <input type="hidden" name="childrenCount" value="{{ $childrenCount }}">
                                            <input type="hidden" name="guestCount" value="{{ $guestCount }}">
                                            
                                            
                                            @if(session('status'))
                                                <div id="status-message" class="alert alert-danger">
                                                    {{ session('status') }}
                                                </div>
                                                
                                                <script>
                                                    setTimeout(function() {
                                                        var message = document.getElementById('status-message');
                                                        if (message) {
                                                            message.style.display = 'none';
                                                        }
                                                    }, 3000); // 3000ms = 3 seconds
                                                </script>
                                            @endif
                                            
                                            
                                            @if($property->only_for_enquiry ==0)
                                               <button type="submit" class="btn py-3 fw-bold w-100 btn-primary booknow" @if(!$isBookingEnable) disabled="disabled" @endif>Book Now</button>
                                            @else
                                               <button role="button" data-fancybox data-src="#enquire" class="btn py-3 fw-bold w-100 btn-primary">Enquire Now</button>
                                            @endif
                                        </form>
                                    </div>
                                    
                        
                                    <div class="col-12">
                                        @if($property->only_for_enquiry ==0)    
                                            <span role="button" class="note-link text-primary" data-fancybox data-src="#enquire">Enquire Now</span>
                                             <span style="vertical-align: sub;">/</span>
                                        @endif    
                                          <a href="javascript:void(0);" class="note-link" data-fancybox data-src="#cancellation-policy">Booking & Cancellation Policy</a>
                                          
                                    </div>
                        
                        
                                </div>
                            </div>
                        </div>
                    
                    </div>

                </div>
            </div>
        </section>


    </div>

<div class="mobile-reserve-wrap d-xl-none">
    <div class="container">  
        <div class="row">
            <div class="col">
                <div class="m-booking-info">
                    <strong>₹<span class="PricePerNight"></span></strong> per night + taxes <br>
                    <ul class="list-unstyled mb-0 bi-info book-link ">
                        <!--<li class="date-text">{{ date('jS M', strtotime($checkInDate)) ?? '' }} - {{ date('jS M', strtotime($checkOutDate)) ?? '' }}</li>-->
                        
                        <li class="date-text checkIn_chechout_mobile_display">{{ date('jS M', strtotime($checkInDate)) }} - {{ date('jS M', strtotime($checkOutDate)) }}</li>
                        <li>
                            <span class="totalNight">  </span> <span class="">nights</span>                             
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152L0 424c0 48.6 39.4 88 88 88l272 0c48.6 0 88-39.4 88-88l0-112c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 112c0 22.1-17.9 40-40 40L88 464c-22.1 0-40-17.9-40-40l0-272c0-22.1 17.9-40 40-40l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24L88 64z"/></svg>
                        </li>
                    </ul>                    
                </div>
            </div>
            <!--<div class="col-auto">-->
            <!--    <a href="javascript:void(0)" class="btn w-100 btn-primary px-3 make-reservation">Book Now</a>-->
            <!--</div>-->
            <div class="col-auto">
                  <form action="{{ route('website.customer.property.book') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $property->id }}">
                    
                    <input type="hidden" name="slug" value="{{ $property->url_key }}">
                    <input type="hidden" name="location_name" value="{{ $location_name }}">
                    <input type="hidden" name="checkin_date" value="{{ $checkin_date }}">
                    <input type="hidden" name="checkout_date" value="{{ $checkout_date }}">
                    <input type="hidden" name="city_id" value="{{ $city_id }}">
                    <input type="hidden" name="total_guests" value="{{ $total_guests }}">
                    <input type="hidden" name="adultsCount" value="{{ $adultsCount }}">
                    <input type="hidden" name="childrenCount" value="{{ $childrenCount }}">
                    <input type="hidden" name="guestCount" value="{{ $guestCount }}">
                    
                    @if(session('status'))
                    <div id="status-message" class="alert alert-danger">
                        {{ session('status') }}
                    </div>
                    
                    <script>
                        setTimeout(function() {
                            var message = document.getElementById('status-message');
                            if (message) {
                                message.style.display = 'none';
                            }
                        }, 3000); // 3000ms = 3 seconds
                    </script>
                @endif

                    <button type="submit" class="btn w-100 btn-primary px-3 make-reservation booknow" @if(!$isBookingEnable) disabled="disabled" @endif>Book Now</button>
                </form>
            </div>
            
        </div> 
    </div>
</div>
@if(!empty($property->house_rules))
    <div id="house-rules" class="popup">
        <p>{!! $property->house_rules ?? '' !!}</p>
    </div>
    @endif
    
    @if(!empty($property->cancellation_policy))
    <div id="cancellation-policy" class="popup">
        <p>{!! $property->cancellation_policy ??
            '' !!}</p>
    </div>
    @endif

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
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}&amp;t={{$property->home_name}}"
                            target="_blank">
                            <svg style="width: 20px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                <path
                                    d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" />
                            </svg>Facebook</a>
                    </li>
                    <li>
                        <a href="https://twitter.com/intent/tweet?url={{request()->url()}}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
                            </svg>Twitter X</a>
                    </li>

                    <li>
                        <a href="https://wa.me/?text={{request()->url()}}" data-action="share/whatsapp/share"
                            target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path
                                    d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                            </svg>WhatsApp</a>
                    </li>
                    <li><a href="mailto:?body={{request()->url()}}&amp;subject={{$property->home_name}}"><svg
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M64 112c-8.8 0-16 7.2-16 16l0 22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1l0-22.1c0-8.8-7.2-16-16-16L64 112zM48 212.2L48 384c0 8.8 7.2 16 16 16l384 0c8.8 0 16-7.2 16-16l0-171.8L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64l384 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64L0 128z" />
                            </svg>Email</a></li>
                            
                    <li><a href="javascript:void(0)" data-clipboard-text="hello--sdsdsd-dsd--"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M384 336l-192 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l140.1 0L400 115.9 400 320c0 8.8-7.2 16-16 16zM192 384l192 0c35.3 0 64-28.7 64-64l0-204.1c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1L192 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64L0 448c0 35.3 28.7 64 64 64l192 0c35.3 0 64-28.7 64-64l0-32-48 0 0 32c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l32 0 0-48-32 0z"/></svg>Copy Link</a></li>
                </ul>
            </div>
        </div>
    </div>
    
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
          
         });

    </script>


<div id="enquire" class="query-popup popup" style="max-width:500px;">
    <div class="ic-info mb-4 pb-2">
        <h5>Make an enquiry</h5>
        <h3 class="m-0">{{ $property->home_name ?? '' }}<em><br><small>{{ $property->location ?? '' }}</b>,{{ $property->state ?? '' }}</small></em></h3>
    </div>
      <form method="post" id="enquiry_form">
         @csrf
            <div class="row gy-3 gx-5">
                    <input type ="hidden" name="checkInDate" value="@if($isBookingEnable) {{$checkInDate}} @endif">
                    <input type ="hidden" name="checkOutDate" value="@if($isBookingEnable) {{$checkOutDate}} @endif">
                    <input type ="hidden" name="property_name" value="{{$property->home_name}}">
                    <input type ="hidden" name="pId" value="{{$property->id}}">
                    <input type ="hidden" name="guest" value="{{$totGuest}}">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="mb-1">Your Name <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                    </div>
                    <!--<div class="col-12">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="mb-1">Phone Number <sup class="text-danger">*</sup></label>-->
                    <!--        <input type="text" class="form-control" name="phone" id="phone">-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    
                    <div class="col-12">
                        <div class="row gx-3">
                            <label class="mb-1">Phone Number <sup class="text-danger">*</sup></label>
                            <div class="col-auto">
                                <select name="country_code" id="country_code" class="form-control country_code" style="max-width:120px;">
                                    <?php
                                    $countries = [
                                        ["code" => "91", "name" => "India (+91)", "countryCode" => "IN"],
                                        ["code" => "47", "name" => "Norway (+47)", "countryCode" => "GB"],
                                        ["code" => "44", "name" => "UK (+44)", "countryCode" => "US"],
                                        ["code" => "213", "name" => "Algeria (+213)", "countryCode" => "DZ"],
                                        ["code" => "376", "name" => "Andorra (+376)", "countryCode" => "AD"],
                                        ["code" => "244", "name" => "Angola (+244)", "countryCode" => "AO"],
                                        ["code" => "1264", "name" => "Anguilla (+1264)", "countryCode" => "AI"],
                                        ["code" => "1268", "name" => "Antigua & Barbuda (+1268)", "countryCode" => "AG"],
                                        ["code" => "54", "name" => "Argentina (+54)", "countryCode" => "AR"],
                                        ["code" => "374", "name" => "Armenia (+374)", "countryCode" => "AM"],
                                        ["code" => "297", "name" => "Aruba (+297)", "countryCode" => "AW"],
                                        ["code" => "61", "name" => "Australia (+61)", "countryCode" => "AU"],
                                        ["code" => "43", "name" => "Austria (+43)", "countryCode" => "AT"],
                                        ["code" => "994", "name" => "Azerbaijan (+994)", "countryCode" => "AZ"],
                                        ["code" => "1242", "name" => "Bahamas (+1242)", "countryCode" => "BS"],
                                        ["code" => "973", "name" => "Bahrain (+973)", "countryCode" => "BH"],
                                        ["code" => "880", "name" => "Bangladesh (+880)", "countryCode" => "BD"],
                                        ["code" => "1246", "name" => "Barbados (+1246)", "countryCode" => "BB"],
                                        ["code" => "375", "name" => "Belarus (+375)", "countryCode" => "BY"],
                                        ["code" => "32", "name" => "Belgium (+32)", "countryCode" => "BE"],
                                        ["code" => "501", "name" => "Belize (+501)", "countryCode" => "BZ"],
                                        ["code" => "229", "name" => "Benin (+229)", "countryCode" => "BJ"],
                                        ["code" => "1441", "name" => "Bermuda (+1441)", "countryCode" => "BM"],
                                        ["code" => "975", "name" => "Bhutan (+975)", "countryCode" => "BT"],
                                        ["code" => "591", "name" => "Bolivia (+591)", "countryCode" => "BO"],
                                        ["code" => "387", "name" => "Bosnia Herzegovina (+387)", "countryCode" => "BA"],
                                        ["code" => "267", "name" => "Botswana (+267)", "countryCode" => "BW"],
                                        ["code" => "55", "name" => "Brazil (+55)", "countryCode" => "BR"],
                                        ["code" => "673", "name" => "Brunei (+673)", "countryCode" => "BN"],
                                        ["code" => "359", "name" => "Bulgaria (+359)", "countryCode" => "BG"],
                                        ["code" => "226", "name" => "Burkina Faso (+226)", "countryCode" => "BF"],
                                        ["code" => "257", "name" => "Burundi (+257)", "countryCode" => "BI"],
                                        ["code" => "855", "name" => "Cambodia (+855)", "countryCode" => "KH"],
                                        ["code" => "237", "name" => "Cameroon (+237)", "countryCode" => "CM"],
                                        ["code" => "1", "name" => "Canada (+1)", "countryCode" => "CA"],
                                        ["code" => "238", "name" => "Cape Verde Islands (+238)", "countryCode" => "CV"],
                                        ["code" => "1345", "name" => "Cayman Islands (+1345)", "countryCode" => "KY"],
                                        ["code" => "236", "name" => "Central African Republic (+236)", "countryCode" => "CF"],
                                        ["code" => "56", "name" => "Chile (+56)", "countryCode" => "CL"],
                                        ["code" => "86", "name" => "China (+86)", "countryCode" => "CN"],
                                        ["code" => "57", "name" => "Colombia (+57)", "countryCode" => "CO"],
                                        ["code" => "269", "name" => "Comoros (+269)", "countryCode" => "KM"],
                                        ["code" => "242", "name" => "Congo (+242)", "countryCode" => "CG"],
                                        ["code" => "682", "name" => "Cook Islands (+682)", "countryCode" => "CK"],
                                        ["code" => "506", "name" => "Costa Rica (+506)", "countryCode" => "CR"],
                                        ["code" => "385", "name" => "Croatia (+385)", "countryCode" => "HR"],
                                        ["code" => "53", "name" => "Cuba (+53)", "countryCode" => "CU"],
                                        ["code" => "90392", "name" => "Cyprus North (+90392)", "countryCode" => "CY"],
                                        ["code" => "357", "name" => "Cyprus South (+357)", "countryCode" => "CY"],
                                        ["code" => "42", "name" => "Czech Republic (+42)", "countryCode" => "CZ"],
                                        ["code" => "45", "name" => "Denmark (+45)", "countryCode" => "DK"],
                                        ["code" => "253", "name" => "Djibouti (+253)", "countryCode" => "DJ"],
                                        ["code" => "1809", "name" => "Dominica (+1809)", "countryCode" => "DM"],
                                        ["code" => "1809", "name" => "Dominican Republic (+1809)", "countryCode" => "DO"],
                                        ["code" => "593", "name" => "Ecuador (+593)", "countryCode" => "EC"],
                                        ["code" => "20", "name" => "Egypt (+20)", "countryCode" => "EG"],
                                        ["code" => "503", "name" => "El Salvador (+503)", "countryCode" => "SV"],
                                        ["code" => "240", "name" => "Equatorial Guinea (+240)", "countryCode" => "GQ"],
                                        ["code" => "291", "name" => "Eritrea (+291)", "countryCode" => "ER"],
                                        ["code" => "372", "name" => "Estonia (+372)", "countryCode" => "EE"],
                                        ["code" => "251", "name" => "Ethiopia (+251)", "countryCode" => "ET"],
                                        ["code" => "500", "name" => "Falkland Islands (+500)", "countryCode" => "FK"],
                                        ["code" => "298", "name" => "Faroe Islands (+298)", "countryCode" => "FO"],
                                        ["code" => "679", "name" => "Fiji (+679)", "countryCode" => "FJ"],
                                        ["code" => "358", "name" => "Finland (+358)", "countryCode" => "FI"],
                                        ["code" => "33", "name" => "France (+33)", "countryCode" => "FR"],
                                        ["code" => "594", "name" => "French Guiana (+594)", "countryCode" => "GF"],
                                        ["code" => "689", "name" => "French Polynesia (+689)", "countryCode" => "PF"],
                                        ["code" => "241", "name" => "Gabon (+241)", "countryCode" => "GA"],
                                        ["code" => "220", "name" => "Gambia (+220)", "countryCode" => "GM"],
                                        ["code" => "7880", "name" => "Georgia (+7880)", "countryCode" => "GE"],
                                        ["code" => "49", "name" => "Germany (+49)", "countryCode" => "DE"],
                                        ["code" => "233", "name" => "Ghana (+233)", "countryCode" => "GH"],
                                        ["code" => "350", "name" => "Gibraltar (+350)", "countryCode" => "GI"],
                                        ["code" => "30", "name" => "Greece (+30)", "countryCode" => "GR"],
                                        ["code" => "299", "name" => "Greenland (+299)", "countryCode" => "GL"],
                                        ["code" => "1473", "name" => "Grenada (+1473)", "countryCode" => "GD"],
                                        ["code" => "590", "name" => "Guadeloupe (+590)", "countryCode" => "GP"],
                                        ["code" => "671", "name" => "Guam (+671)", "countryCode" => "GU"],
                                        ["code" => "502", "name" => "Guatemala (+502)", "countryCode" => "GT"],
                                        ["code" => "224", "name" => "Guinea (+224)", "countryCode" => "GN"],
                                        ["code" => "245", "name" => "Guinea - Bissau (+245)", "countryCode" => "GW"],
                                        ["code" => "592", "name" => "Guyana (+592)", "countryCode" => "GY"],
                                        ["code" => "509", "name" => "Haiti (+509)", "countryCode" => "HT"],
                                        ["code" => "504", "name" => "Honduras (+504)", "countryCode" => "HN"],
                                        ["code" => "852", "name" => "Hong Kong (+852)", "countryCode" => "HK"],
                                        ["code" => "36", "name" => "Hungary (+36)", "countryCode" => "HU"],
                                        ["code" => "354", "name" => "Iceland (+354)", "countryCode" => "IS"],
                                        ["code" => "62", "name" => "Indonesia (+62)", "countryCode" => "ID"],
                                        ["code" => "98", "name" => "Iran (+98)", "countryCode" => "IR"],
                                        ["code" => "964", "name" => "Iraq (+964)", "countryCode" => "IQ"],
                                        ["code" => "353", "name" => "Ireland (+353)", "countryCode" => "IE"],
                                        ["code" => "972", "name" => "Israel (+972)", "countryCode" => "IL"],
                                        ["code" => "39", "name" => "Italy (+39)", "countryCode" => "IT"],
                                        ["code" => "1876", "name" => "Jamaica (+1876)", "countryCode" => "JM"],
                                        ["code" => "81", "name" => "Japan (+81)", "countryCode" => "JP"],
                                        ["code" => "962", "name" => "Jordan (+962)", "countryCode" => "JO"],
                                        ["code" => "7", "name" => "Kazakhstan (+7)", "countryCode" => "KZ"],
                                        ["code" => "254", "name" => "Kenya (+254)", "countryCode" => "KE"],
                                        ["code" => "686", "name" => "Kiribati (+686)", "countryCode" => "KI"],
                                        ["code" => "850", "name" => "Korea North (+850)", "countryCode" => "KP"],
                                        ["code" => "82", "name" => "Korea South (+82)", "countryCode" => "KR"],
                                        ["code" => "965", "name" => "Kuwait (+965)", "countryCode" => "KW"],
                                        ["code" => "996", "name" => "Kyrgyzstan (+996)", "countryCode" => "KG"],
                                        ["code" => "856", "name" => "Laos (+856)", "countryCode" => "LA"],
                                        ["code" => "371", "name" => "Latvia (+371)", "countryCode" => "LV"],
                                        ["code" => "961", "name" => "Lebanon (+961)", "countryCode" => "LB"],
                                        ["code" => "266", "name" => "Lesotho (+266)", "countryCode" => "LS"],
                                        ["code" => "231", "name" => "Liberia (+231)", "countryCode" => "LR"],
                                        ["code" => "218", "name" => "Libya (+218)", "countryCode" => "LY"],
                                        ["code" => "417", "name" => "Liechtenstein (+417)", "countryCode" => "LI"],
                                        ["code" => "370", "name" => "Lithuania (+370)", "countryCode" => "LT"],
                                        ["code" => "352", "name" => "Luxembourg (+352)", "countryCode" => "LU"],
                                        ["code" => "853", "name" => "Macao (+853)", "countryCode" => "MO"],
                                        ["code" => "389", "name" => "Macedonia (+389)", "countryCode" => "MK"],
                                        ["code" => "261", "name" => "Madagascar (+261)", "countryCode" => "MG"],
                                        ["code" => "265", "name" => "Malawi (+265)", "countryCode" => "MW"],
                                        ["code" => "60", "name" => "Malaysia (+60)", "countryCode" => "MY"],
                                        ["code" => "960", "name" => "Maldives (+960)", "countryCode" => "MV"],
                                        ["code" => "223", "name" => "Mali (+223)", "countryCode" => "ML"],
                                        ["code" => "356", "name" => "Malta (+356)", "countryCode" => "MT"],
                                        ["code" => "692", "name" => "Marshall Islands (+692)", "countryCode" => "MH"],
                                        ["code" => "596", "name" => "Martinique (+596)", "countryCode" => "MQ"],
                                        ["code" => "222", "name" => "Mauritania (+222)", "countryCode" => "MR"],
                                        ["code" => "269", "name" => "Mayotte (+269)", "countryCode" => "YT"],
                                        ["code" => "52", "name" => "Mexico (+52)", "countryCode" => "MX"],
                                        ["code" => "691", "name" => "Micronesia (+691)", "countryCode" => "FM"],
                                        ["code" => "373", "name" => "Moldova (+373)", "countryCode" => "MD"],
                                        ["code" => "377", "name" => "Monaco (+377)", "countryCode" => "MC"],
                                        ["code" => "976", "name" => "Mongolia (+976)", "countryCode" => "MN"],
                                        ["code" => "1664", "name" => "Montserrat (+1664)", "countryCode" => "MS"],
                                        ["code" => "212", "name" => "Morocco (+212)", "countryCode" => "MA"],
                                        ["code" => "258", "name" => "Mozambique (+258)", "countryCode" => "MZ"],
                                        ["code" => "95", "name" => "Myanmar (+95)", "countryCode" => "MN"],
                                        ["code" => "264", "name" => "Namibia (+264)", "countryCode" => "NA"],
                                        ["code" => "674", "name" => "Nauru (+674)", "countryCode" => "NR"],
                                        ["code" => "977", "name" => "Nepal (+977)", "countryCode" => "NP"],
                                        ["code" => "31", "name" => "Netherlands (+31)", "countryCode" => "NL"],
                                        ["code" => "687", "name" => "New Caledonia (+687)", "countryCode" => "NC"],
                                        ["code" => "64", "name" => "New Zealand (+64)", "countryCode" => "NZ"],
                                        ["code" => "505", "name" => "Nicaragua (+505)", "countryCode" => "NI"],
                                        ["code" => "227", "name" => "Niger (+227)", "countryCode" => "NE"],
                                        ["code" => "234", "name" => "Nigeria (+234)", "countryCode" => "NG"],
                                        ["code" => "683", "name" => "Niue (+683)", "countryCode" => "NU"],
                                        ["code" => "672", "name" => "Norfolk Islands (+672)", "countryCode" => "NF"],
                                        ["code" => "670", "name" => "Northern Marianas (+670)", "countryCode" => "NP"],
                                        ["code" => "47", "name" => "Norway (+47)", "countryCode" => "NO"],
                                        ["code" => "968", "name" => "Oman (+968)", "countryCode" => "OM"],
                                        ["code" => "680", "name" => "Palau (+680)", "countryCode" => "PW"],
                                        ["code" => "507", "name" => "Panama (+507)", "countryCode" => "PA"],
                                        ["code" => "675", "name" => "Papua New Guinea (+675)", "countryCode" => "PG"],
                                        ["code" => "595", "name" => "Paraguay (+595)", "countryCode" => "PY"],
                                        ["code" => "51", "name" => "Peru (+51)", "countryCode" => "PE"],
                                        ["code" => "63", "name" => "Philippines (+63)", "countryCode" => "PH"],
                                        ["code" => "48", "name" => "Poland (+48)", "countryCode" => "PL"],
                                        ["code" => "351", "name" => "Portugal (+351)", "countryCode" => "PT"],
                                        ["code" => "1787", "name" => "Puerto Rico (+1787)", "countryCode" => "PR"],
                                        ["code" => "974", "name" => "Qatar (+974)", "countryCode" => "QA"],
                                        ["code" => "262", "name" => "Reunion (+262)", "countryCode" => "RE"],
                                        ["code" => "40", "name" => "Romania (+40)", "countryCode" => "RO"],
                                        ["code" => "7", "name" => "Russia (+7)", "countryCode" => "RU"],
                                        ["code" => "250", "name" => "Rwanda (+250)", "countryCode" => "RW"],
                                        ["code" => "378", "name" => "San Marino (+378)", "countryCode" => "SM"],
                                        ["code" => "239", "name" => "Sao Tome & Principe (+239)", "countryCode" => "ST"],
                                        ["code" => "966", "name" => "Saudi Arabia (+966)", "countryCode" => "SA"],
                                        ["code" => "221", "name" => "Senegal (+221)", "countryCode" => "SN"],
                                        ["code" => "381", "name" => "Serbia (+381)", "countryCode" => "CS"],
                                        ["code" => "248", "name" => "Seychelles (+248)", "countryCode" => "SC"],
                                        ["code" => "232", "name" => "Sierra Leone (+232)", "countryCode" => "SL"],
                                        ["code" => "65", "name" => "Singapore (+65)", "countryCode" => "SG"],
                                        ["code" => "421", "name" => "Slovak Republic (+421)", "countryCode" => "SK"],
                                        ["code" => "386", "name" => "Slovenia (+386)", "countryCode" => "SI"],
                                        ["code" => "677", "name" => "Solomon Islands (+677)", "countryCode" => "SB"],
                                        ["code" => "252", "name" => "Somalia (+252)", "countryCode" => "SO"],
                                        ["code" => "27", "name" => "South Africa (+27)", "countryCode" => "ZA"],
                                        ["code" => "34", "name" => "Spain (+34)", "countryCode" => "ES"],
                                        ["code" => "94", "name" => "Sri Lanka (+94)", "countryCode" => "LK"],
                                        ["code" => "290", "name" => "St. Helena (+290)", "countryCode" => "SH"],
                                        ["code" => "1869", "name" => "St. Kitts (+1869)", "countryCode" => "KN"],
                                        ["code" => "1758", "name" => "St. Lucia (+1758)", "countryCode" => "SC"],
                                        ["code" => "249", "name" => "Sudan (+249)", "countryCode" => "SD"],
                                        ["code" => "597", "name" => "Suriname (+597)", "countryCode" => "SR"],
                                        ["code" => "268", "name" => "Swaziland (+268)", "countryCode" => "SZ"],
                                        ["code" => "46", "name" => "Sweden (+46)", "countryCode" => "SE"],
                                        ["code" => "41", "name" => "Switzerland (+41)", "countryCode" => "CH"],
                                        ["code" => "963", "name" => "Syria (+963)", "countryCode" => "SI"],
                                        ["code" => "886", "name" => "Taiwan (+886)", "countryCode" => "TW"],
                                        ["code" => "7", "name" => "Tajikstan (+7)", "countryCode" => "TJ"],
                                        ["code" => "66", "name" => "Thailand (+66)", "countryCode" => "TH"],
                                        ["code" => "228", "name" => "Togo (+228)", "countryCode" => "TG"],
                                        ["code" => "676", "name" => "Tonga (+676)", "countryCode" => "TO"],
                                        ["code" => "1868", "name" => "Trinidad & Tobago (+1868)", "countryCode" => "TT"],
                                        ["code" => "216", "name" => "Tunisia (+216)", "countryCode" => "TN"],
                                        ["code" => "90", "name" => "Turkey (+90)", "countryCode" => "TR"],
                                        ["code" => "7", "name" => "Turkmenistan (+7)", "countryCode" => "TM"],
                                        ["code" => "993", "name" => "Turkmenistan (+993)", "countryCode" => "TM"],
                                        ["code" => "1649", "name" => "Turks & Caicos Islands (+1649)", "countryCode" => "TC"],
                                        ["code" => "688", "name" => "Tuvalu (+688)", "countryCode" => "TV"],
                                        ["code" => "256", "name" => "Uganda (+256)", "countryCode" => "UG"],
                                        ["code" => "380", "name" => "Ukraine (+380)", "countryCode" => "UA"],
                                        ["code" => "971", "name" => "United Arab Emirates (+971)", "countryCode" => "AE"],
                                        ["code" => "598", "name" => "Uruguay (+598)", "countryCode" => "UY"],
                                        ["code" => "7", "name" => "Uzbekistan (+7)", "countryCode" => "UZ"],
                                        ["code" => "678", "name" => "Vanuatu (+678)", "countryCode" => "VU"],
                                        ["code" => "379", "name" => "Vatican City (+379)", "countryCode" => "VA"],
                                        ["code" => "58", "name" => "Venezuela (+58)", "countryCode" => "VE"],
                                        ["code" => "84", "name" => "Vietnam (+84)", "countryCode" => "VN"],
                                        ["code" => "1284", "name" => "Virgin Islands - British (+1284)", "countryCode" => "VG"],
                                        ["code" => "1340", "name" => "Virgin Islands - US (+1340)", "countryCode" => "VI"],
                                        ["code" => "681", "name" => "Wallis & Futuna (+681)", "countryCode" => "WF"],
                                        ["code" => "969", "name" => "Yemen (North) (+969)", "countryCode" => "YE"],
                                        ["code" => "967", "name" => "Yemen (South) (+967)", "countryCode" => "YE"],
                                        ["code" => "260", "name" => "Zambia (+260)", "countryCode" => "ZM"],
                                        ["code" => "263", "name" => "Zimbabwe (+263)", "countryCode" => "ZW"],
                                    ];
                                    foreach ($countries as $country) {
                                        echo "<option data-countryCode='{$country['countryCode']}' value='{$country['name']}'>{$country['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" name="phone" id="phone" 
                                maxlength="12" minlength="6"  placeholder="Phone Number">
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-12">
                        <div class="form-group">
                            <label class="mb-1">Email Address <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" name="email" id="email">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label class="mb-1">Message <sup class="text-danger">*</sup></label>
                            <textarea name="message" id="message" cols="" rows="3" class="form-control" ></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <button id="submitEnquiry" class="btn btn-primary rounded-pill fw-bold w-100 submit_enquiry" >Submit Enquiry
                            
                            &nbsp;&nbsp; <span id="spinner_new_form" class="spinner-border spinner-border-sm " style="color: #fff; display: none;" role="status"  aria-hidden="true" ></span>
                           </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="successMessage" class="alert alert-success d-none" role="alert">
                            Your enquiry has been submitted successfully!
                        </div>
                    </div>
                </div>
                <div class="row text-center mt-3">
                    <div class="col-12">
                        <p class="d-none d-xl-block">Or speak to a vacation manager on the phone:</p>
                        <a href="tel:+91 87999 15100" class="btn d-none d-xl-block border border-secondary border-1 btn-outline-secondary rounded-pill d-flex align-items-center justify-content-center">
                            <i class="icon-phone-call fs-5 me-2 align-self-center"></i>  +91 87999 14701
                        </a>
                    </div>
                </div>
        </div>
     </form>
</div>


<style>
    .review-full-content img{
        mix-blend-mode: multiply;
        height: 21px;
    }
    .review-full-content >.row{
        margin-top: auto;
    }
    
</style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            setTimeout(function() {
                let pricePerNight = 0; 
    
                let priceElement = document.querySelector(".PricePerNight");
        
                if (priceElement) {
                    let priceText = priceElement.textContent.trim(); 
                    if (priceText) {
                        pricePerNight = parseFloat(priceText.replace(/[^0-9]/g, "")) || 0; 
                    }
                }

                fbq('track', 'ViewContent', {
                    currency: 'INR', 
                    country: "India",
                    value: pricePerNight, 
                    checkin_date: "{{ isset($checkInDate) ? date('jS M', strtotime($checkInDate)) : 'Add dates' }}", 
                    checkout_date: "{{ isset($checkOutDate) ? date('jS M', strtotime($checkOutDate)) : 'Add dates' }}", 
                    property_type: "{{ addslashes($property->home_type) }}",
                    state: "{{ $property->state ?? '' }}", 
                    destination: "{{ $property->location ?? '' }}", 
                    num_adults: {{ $adult ?? 1 }}, 
                    num_children: {{ $child ?? 0 }}, 
                    num_infants: 0
                });
            }, 1000);
    

        });
    </script>

    
    <script>
        let minStay = 1;
        let minStayArray =  JSON.parse('{!! json_encode($minStayArray) !!}');
        let maximum_number_of_guests = Number("{{ $property->maximum_number_of_guests }}");
        document.addEventListener("DOMContentLoaded", function () {
            const readMores = document.querySelectorAll('.js-read-smore')
            const RMs = readSmore(readMores).init();
            
        
            
            $(document).on("click", ".read-smore__link", function(){
                setTimeout(function(){
                    ScrollTrigger.refresh(true);
                },10)
            })
            
           let previouslyBookedCheckoutDates = JSON.parse('{!! json_encode($previouslyBookedCheckoutDates) !!}');
           let disabledDates = JSON.parse('{!! json_encode($propertyUnavailableDates) !!}');
           disabledDates = disabledDates.filter(date => !previouslyBookedCheckoutDates.includes(date));
            
            let parentElClass = '.detail-page-search';    
            let inputCalendar = document.getElementById('detail-page-calendar');
            function resetDateInput(){
             $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
             datepickerHero.clear();
            }
            function updateMinStay(datepicker){
                const isSelecting = datepicker.start && !datepicker.end;               
                const allDays = datepicker.datepicker.getElementsByTagName("td");
                if(isSelecting){
                    let getStartDate = fecha.format(datepicker.start, `YYYY-MM-DD`);
                    let nights = minStayArray[getStartDate];
                    for (let i = 0; i < allDays.length; i++) {
                        if ($(allDays[i]).hasClass("datepicker__month-day--first-day-selected")){
                            for (let j = 1; j < nights; j++) {                                
                                $(allDays[i + j]).addClass("datepicker__month-day--invalid ss")
                            }
                        }
                    }
                }
            }     
           window.datepickerHero = new HotelDatepicker(inputCalendar, {
               inline: true,
               moveBothMonths: true,           
               // clearButton: true,
               // minNights: 4,
               clearButton: true,
               showTopbar: true,
               topbarPosition: 'bottom',
               enableCheckout: true,
               disabledDates: disabledDates,
               onSelectRange: function() {
                   let startDate = fecha.format(this.start, `Do MMM`);
                   let endDate = fecha.format(this.end, `Do MMM`);
                   $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
                   $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);   
                   //calendarBtn.toggle();             
               } ,
               onDayClick: function() {    
                   updateMinStay(datepickerHero)  
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
                       
                    
                       $('.booknow').removeAttr('disabled');
                       //$('.booknowFilter').removeClass('d-none');
                       
                       let startDateMobile = fecha.format(this.start, 'Do MMM');
                        let endDateMobile = fecha.format(this.end, 'Do MMM');
                        

                        const checkIn_chechout_mobile_display = $('.checkIn_chechout_mobile_display');
                        checkIn_chechout_mobile_display.text(`${startDateMobile} - ${endDateMobile}`);






                       
                       let days = datepickerHero.getNights();
                       ci_date= fecha.format(this.start, `YYYY-MM-DD`);
                       co_date= fecha.format(this.end, `YYYY-MM-DD`);
                      // tot_guest= $('#totalGuests').val();
                       tot_no_of_days= days;
                        $(".promo-code-tr").remove();
                        $('#coupon-href').removeClass('d-none');
                        $('#coupon_code').val('');
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
           
           $(document).on("click",".datepicker__month-button", function(){
                //console.log("clicked");
                setTimeout(() => {                
                    updateMinStay(datepickerHero)
                }, 0);
            })
            $(document).on("click",'.datepicker__month-day--valid.datepicker__month-day--invalid', function(){
                let $timeStamp = $(this).attr('time');    
                let getDate = fecha.format(datepickerHero.start, `YYYY-MM-DD`);            
                //minStayArray[getStartDate];
                alert("Minimum stay can't be less than "+ minStayArray[getDate]+" Nights")
                       
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
let property_id = "{{ $property->id  }}";
let couponCode = 0;
let maximum_number_of_guests = "{{ $property->maximum_number_of_guests  }}";
let adultsCount = parseInt(adults);
let childrenCount = parseInt(children);

    // initializeGuestCounterDetail();
    // $(document).on('click', '[data-type-detail]', function () {
    //     let dataTypeDetail = $(this).data('type-detail');
    //     counterdetail($(this), dataTypeDetail);
    // });
    // function initializeGuestCounterDetail() {
    //     let adultsCountDetail = $('.adultsCountDetail').val() || 1; 
    //     let childrenCountDetail = $('.childrenCountDetail').val() || 0;
    //     $('.adultsCountDetail').val(adultsCountDetail);
    //     $('.childrenCountDetail').val(childrenCountDetail);
    //     $('.adultsCountDetail').parent().find('.count-val-detail').text(adultsCountDetail);
    //     $('.childrenCountDetail').parent().find('.count-val-detail').text(childrenCountDetail);
    //     $('.adultsCountDetail').val() > 1 
    //         ? $('[data-type-detail="adults"][data-minus-detail]').removeClass('disabled') 
    //         : $('[data-type-detail="adults"][data-minus-detail]').addClass('disabled');
    //     updateTotalGuestsDetail();
    // }

    // function counterdetail(el, dataTypeDetail) {
    //     let inputDetailEl = el.parent().find(`input[data-${dataTypeDetail}-value]`);
    //     let minusEl = el.parent().find('[data-minus-detail]');
    //     let plusEl = el.parent().find('[data-plus-detail]');

    //     let inputValue = parseInt(inputDetailEl.val()) || 0;
    //     if (el.hasClass('c-plus-detail')) {
    //         if (dataTypeDetail === 'children' && inputValue >= 2) return; 
    //         inputValue++;
    //     } else {
    //         inputValue--;
    //     }
    //     inputValue = Math.max(0, inputValue); 
    //     if (dataTypeDetail === 'adults' && inputValue < 1) {
    //         inputValue = 1; 
    //     }
    //     inputDetailEl.val(inputValue);
    //     el.parent().find('.count-val-detail').text(inputValue);
    //     if (dataTypeDetail === 'adults') {
    //         inputValue > 1 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
    //     } else {
    //         inputValue > 0 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
    //     }
    //     if (dataTypeDetail === 'children') {
    //         inputValue >= 2 ? plusEl.addClass('disabled') : plusEl.removeClass('disabled');
    //     }
    //     updateTotalGuestsDetail();
    // }
    // function updateTotalGuestsDetail() {
    //      adultsCount = $('.adultsCountDetail').val() || 0;
    //      childrenCount = $('.childrenCountDetail').val() || 0;
    //     let totalGuest = tot_guest = Number(adultsCount) + Number(childrenCount);
    //     const totalGuestInputText = totalGuest === 0 
    //         ? 'Add guests' 
    //         : totalGuest > 1 
    //         ? `${totalGuest} Guests` 
    //         : `${totalGuest} Guest`;

    //     $('[total-guests-detail]').text(totalGuestInputText);
    //     getPropertyPrice();
    // }

initializeGuestCounterDetail();
    $(document).on('click', '[data-type-detail]', function () {
        let dataTypeDetail = $(this).data('type-detail');
        counterdetail($(this), dataTypeDetail);
    });
    function initializeGuestCounterDetail() {
    let adultsCountDetail = parseInt($('.adultsCountDetail').val()) || 1; 
    let childrenCountDetail = parseInt($('.childrenCountDetail').val()) || 0;

    $('.adultsCountDetail').val(adultsCountDetail);
    $('.childrenCountDetail').val(childrenCountDetail);

    $('.adultsCountDetail').parent().find('.count-val-detail').text(adultsCountDetail);
    $('.childrenCountDetail').parent().find('.count-val-detail').text(childrenCountDetail);

    // Enable/disable minus buttons
    adultsCountDetail > 1 
        ? $('[data-type-detail="adults"][data-minus-detail]').removeClass('disabled') 
        : $('[data-type-detail="adults"][data-minus-detail]').addClass('disabled');

    // Check if maximum guests reached
    updateTotalGuestsDetail();
}
    function counterdetail(el, dataTypeDetail) {
    let inputDetailEl = el.parent().find(`input[data-${dataTypeDetail}-value]`);
    let minusEl = el.parent().find('[data-minus-detail]');
    let plusEl = el.parent().find('[data-plus-detail]');

    let inputValue = parseInt(inputDetailEl.val()) || 0;
    if (el.hasClass('c-plus-detail')) {
       // if (dataTypeDetail === 'children' && inputValue >= 2) return; 
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
    adultsCount = parseInt($('.adultsCountDetail').val()) || 0;
    childrenCount = parseInt($('.childrenCountDetail').val()) || 0;
   // let totalGuest = tot_guest = adultsCount + childrenCount;
    let totalGuest = tot_guest = adultsCount;

    // if (totalGuest >= maximum_number_of_guests) {
    //     $('[data-plus-detail]').each(function () {
    //         console.log(inputValue,"inputValue");
    //         $(this).addClass('disabled');
    //     });
    // } else {
    //     $('[data-plus-detail]').each(function () {
    //         $(this).removeClass('disabled');
    //     });
    // }

    if (totalGuest >= maximum_number_of_guests) {
        $('[data-plus-detail][data-type-detail="adults"]').addClass('disabled');
    } else {
        $('[data-plus-detail][data-type-detail="adults"]').removeClass('disabled');
    }


    updateTotalGuestsDetail();
}
function updateTotalGuestsDetail() {
    adultsCount = parseInt($('.adultsCountDetail').val()) || 0;
    childrenCount = parseInt($('.childrenCountDetail').val()) || 0;
   // let totalGuest = adultsCount + childrenCount;
    let totalGuest = adultsCount;
    const totalGuestInputText = totalGuest === 0 
        ? 'Add guests' 
        : totalGuest > 1 
        ? `${totalGuest} Guests` 
        : `${totalGuest} Guest`;

    $('[total-guests-detail]').text(totalGuestInputText);

    // if (totalGuest >= maximum_number_of_guests) {
    //     $('[data-plus-detail]').each(function () {
    //         $(this).addClass('disabled');
    //     });
    // } else {
    //     $('[data-plus-detail]').each(function () {
    //         $(this).removeClass('disabled');
    //     });
    // }

    if (totalGuest >= maximum_number_of_guests) {
        $('[data-plus-detail][data-type-detail="adults"]').addClass('disabled');
    } else {
        $('[data-plus-detail][data-type-detail="adults"]').removeClass('disabled');
    }


    $(".promo-code-tr").remove();
    $('#coupon-href').removeClass('d-none');
    $('#coupon_code').val('');
    getPropertyPrice();
}


    function getPropertyPrice(){
        $.ajax({
            url: "/get/ajax/property/price",
            type: 'get',
            headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
            data: {
                adults:adultsCount,
                children:childrenCount,
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
                if(couponCode !=''){
                    console.log(couponCode,"couponCode");
                    applyCouponCode(couponCode, 'Apply ');
                }

                let formData = {
                price_per_night_num_formatted: res.data.price_per_night_num_formatted,
                total_price_multiple: res.data.total_price_multiple,
                tax: res.data.tax,
                formatted_total_taxable_amount: res.data.formatted_total_taxable_amount,
                num_formatted_tot_price: res.data.num_formatted_tot_price,
                tot_no_of_days: tot_no_of_days,
                adultsCount: adultsCount,
                childrenCount: childrenCount,
                ci_date: ci_date,
                co_date: co_date,
                tot_guest: tot_guest,
                additionalChargesAmount: res.data.total_additional_charges,
                extra_guest_charge: res.data.extra_guest_charge,
                total_extra_guest_charge: res.data.total_extra_guest_charge,
                additional_charges_name: res.data.additional_charges_name,
            };
                $('.dynamic-hidden-input').remove();
                $.each(formData, function(key, value) {
                    let input = $('<input>').attr({
                        type: 'hidden',
                        name: key,
                        value: value
                    }).addClass('dynamic-hidden-input');
                    $('form').append(input);
                });
            },
            error: function(res) {
            }
        });
    }


    $("#coupon_code").keyup(function(e){
        $('.coupon_error').text('').removeClass('text-danger');
        if(e.target.value.length <= 2){
            $('.apply_coupon').prop('disabled', true);
        }
        else{
            $('.apply_coupon').prop('disabled', false);
        }
        
    });
    $(document).on('click', '.apply_coupon', function (e) {
        e.preventDefault();
        applyCouponCode($('#coupon_code').val(), $(this).text());
    });
    
    $(document).on('click', '.cus-link', function(e) {
        discountAmount = 0;
        discountCode = '';
        tax = initialTax;
        taxAmount = initialTaxAmount;
        totalPayableAmount = totalPayableInitialAmount;
        $('.tax').text(tax)
        $('.taxAmount').text(formatted_total_taxable_amount)
        $('.TotalAmount').text(totalPayableAmount)
        $(".promo-code-tr").remove();
        $('#coupon-href').removeClass('d-none');
        $('#coupon_code').val('');
        $('.apply_coupon').prop('disabled', true);
    });


    function applyCouponCode(code, text){
      //  alert(3443);
        $('.coupon_error').text('');
        $('.coupon_error').removeClass('text-danger');
        if(code !=''){
            if(text == 'Apply '){
                $.ajax({
                    url: "/booking/apply/coupon/code",
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
                    data: {
                        coupon_code: code,
                        checkin_date:ci_date,
                        checkout_date:co_date,
                        propertyId:property_id
                    },

                    success: function(res) {
                        discountCode = $('#coupon_code').val();
                        let totAmount = amountBeforeTax;
                        if(res.status){
                            if(res.discount_type =='percentage'){
                                discountAmount = Math.round(totAmount*(res.discount/100));
                            }
                            else{
                                discountAmount = res.discount;
                            }
                            let amountAfterDiscount = totAmount - discountAmount;
                            let tax = 12;
                            if(amountAfterDiscount > 7500){
                                tax = 18;
                            }
                            taxAmount = Math.round((amountAfterDiscount*tax)/100);
                          //  totalPayableAmount = amountAfterDiscount  + taxAmount;
                            let formattedTotalAmount = amountAfterDiscount  + taxAmount;
                            totalPayableAmount = formattedTotalAmount.toLocaleString();


                            $('.tax').text(tax)
                            $('.taxAmount').text(taxAmount)
                            $('.TotalAmount').text(totalPayableAmount)
                    
                            let discountTr = '<tr class="promo-code-tr"><td class="text-link">Offer Code '+discountCode+'</td><td align="right" class="text-link">-&#8377;'+discountAmount+'</td><td align="right"><a href="javascript:void(0);" class="cus-link"><i class="icon-minus"></i> Remove</a></td></tr>';
                            $(discountTr).insertAfter('.second-tr');
                            $('#coupon-href').addClass('d-none');
                            Fancybox.close();
                            /* let formData = { 
                                tax: tax,
                                num_formatted_tot_price: totalPayableAmount,
                                formatted_total_taxable_amount: taxAmount,
                            };
                            $.each(formData, function(key, value) {
                                let input = $('<input>').attr({
                                    type: 'hidden',
                                    name: key,
                                    value: value
                                }).addClass('dynamic-hidden-input');
                                $('form').append(input);
                            }); */
                            let formData = {
                                tax: tax,
                                num_formatted_tot_price: totalPayableAmount,
                                formatted_total_taxable_amount: taxAmount,
                                discountCode: discountCode,
                                discountAmount: discountAmount,
                            };
                            $.each(formData, function(key, value) {
                                let existingInput = $('form').find('input[name="' + key + '"]');
                                if (existingInput.length > 0) {
                                    existingInput.val(value);
                                } else {
                                    let input = $('<input>').attr({
                                        type: 'hidden',
                                        name: key,
                                        value: value
                                    }).addClass('dynamic-hidden-input');
                                    $('form').append(input);
                                }
                            });


                        }
                        else{
                            $('.coupon_error').text(res.message).addClass('text-danger');
                        } 
                    },
                    error: function(res) {
                        $('.coupon_error').text(res.message).addClass('text-danger');
                    }
                });
            }  
        }
        else{
            $('.coupon_error').text('Enter Coupon Code').addClass('text-danger');
        }  
    }
    
    
    /* enquire start */
$('#submitEnquiry').on('click', function(e) {
        e.preventDefault();  
        var button = document.getElementById('submitEnquiry');
        var spinner = document.getElementById('spinner_new_form');
        spinner.style.display = 'inline-block';
        button.disabled = true;
        let formdata = $('#enquiry_form').serialize();
        $.ajax({
            url: "{{ route('enquire') }}",  
            type: "POST",  
            data: formdata,  
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  
            },
            success: function(response) {
                if (response['status'] == true) {
                    spinner.style.display = 'none';
                    button.disabled = false;
                     
                    const successMessage = $('#successMessage');
                    successMessage.removeClass('d-none').text('Thank You for your query. We will get back to you soon.!');
                    
                    fbq('track', 'Lead');
                    gtag('event', 'conversion', {'send_to': 'AW-16482594363/i0BUCKemtpcaELvcwbM9'});
                    
                    setTimeout(() => {
                        Fancybox.close();
                        successMessage.addClass('d-none');
                    }, 5000); // Hide after 5 seconds
                     
                    
                    $('#enquiry_form')[0].reset();
                    
                    
                }
                else {
                    var errors = response['errors']; 
                    if (errors) {
                        $.each(errors, function(key, value) {
                            console.log(value[0], "value[0]"); 
                            var elementId = key.replace(/\./g, '_'); 
                            console.log(elementId, "key");
                            var inputElement = $('#' + elementId);
                            console.log(inputElement, "inputElement"); 
                            inputElement.addClass('border-danger');  
                           // inputElement.next('p').addClass('text-danger').html(value[0]);
                        });
                    }
                    spinner.style.display = 'none';
                    button.disabled = false;
                }
            },
        });
    });

function toggleButtonState() {
        var phoneNumber = $('#phone').val();
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '').slice(0, 12);
        if(phoneNumber[0] == '0'){
            $('#phone').addClass('border-danger');
            $('#phone').siblings('p').addClass('text-danger').html("Phone number can't start with 0.");
            
        }else{
            $('#phone').siblings('p').addClass('text-danger').html("");
            $('#phone').removeClass('border-danger').html('');
        }
        $('#phone').val(phoneNumber);
    }
    $('#phone').on('input', function() {
        toggleButtonState();
    }); 
    
    $(document).ready(function() {
        $('#name').on('input', function() {
            $('#name').removeClass('border-danger').html('');
        });
        
        $('#email').on('input', function() {
            $('#email').removeClass('border-danger').html('');
        });
        $('#phone').on('input', function() {
            $('#phone').removeClass('border-danger').html('');
        });
        $('#message').on('input', function() {
            $('#message').removeClass('border-danger').html('');
        });
       
    });

/* enquire end */
    

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
                pagination: {
                    el: ".section-mobile-gallery .swiper-pagination",
                    dynamicBullets: true,
                },
            })

        $(".book-link").on("click", function(){
            $("body").addClass("detail-booking-active");
        })


        $(".detail-page-search .close-btn").on("click", function(){
            $("body").removeClass("detail-booking-active");
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

                
                filterButtons.forEach(btn => btn.parentElement.classList.remove("active"));
                this.parentElement.classList.add("active");

                
                reviews.forEach(slide => {
                    const reviewSite = slide.getAttribute("data-review-site");
                    if (source === "all" || reviewSite === source) {
                        slide.style.display = ""; 
                    } else {
                        slide.style.display = "none"; 
                    }
                });

                
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
            //const location = { lat: 15.55967851741905, lng: 73.75130487361152 }; 
            // Create the map, centered at the location
            
            let lat = Number("{{ $property->map_latitude }}");
            let long = Number("{{ $property->map_longitude }}");
            const location = { lat: lat, lng: long }; 
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
                label: {
                    text: '{{$property->home_name}}', 
                    color: '#00423c',
                    fontWeight: '500', 
                    fontSize: '16px', 
                    className: 'marker-label',
                },
                icon: {
                     
                url: "/assets/images/map.svg",
                scaledSize: new google.maps.Size(46, 46),
                anchor: new google.maps.Point(12, 20),
                labelOrigin: new google.maps.Point(10, 60)
                },
                
            });

        }
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
    
@endsection
