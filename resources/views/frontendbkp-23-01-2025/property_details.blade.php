
@extends('components.layouts.app')
@section('content')
    <style>
        @media max-width(1199px){
            .footer-main {
                padding-bottom: 200px;
            }


        }
    </style>
<svg width="0" height="0" class="d-none">
  <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22" id="play">
    <g id="Group_327" transform="translate(-768 -451)">
      <g id="Ellipse_78" transform="translate(768 451)" fill="none" stroke="#f7f2ec" stroke-width="1.2">
        <circle cx="11" cy="11" r="11" stroke="none"></circle>
        <circle cx="11" cy="11" r="10.4" fill="none"></circle>
      </g>
      <g id="play-sharp" transform="translate(776.137 456.821)">
        <path id="Path_1312" d="M9,16.359l7.615-5.179L9,6Z" transform="translate(-9 -6)" fill="#f7f2ec"></path>
      </g>
    </g>
  </symbol>
  <symbol id="gallery" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12.533 12.533">
    <g id="Group_116">
      <path id="Path_1275" d="M14,14h2.507v2.507H14Z" transform="translate(-14 -14)" fill="#f2e9df"></path>
      <path id="Path_1276" d="M22,14h2.507v2.507H22Z" transform="translate(-16.987 -14)" fill="#f2e9df"></path>
      <path id="Path_1277" d="M32.507,14H30v2.507h2.507Z" transform="translate(-19.974 -14)" fill="#f2e9df"></path>
      <path id="Path_1278" d="M14,22h2.507v2.507H14Z" transform="translate(-14 -16.987)" fill="#f2e9df"></path>
      <path id="Path_1279" d="M24.507,22H22v2.507h2.507Z" transform="translate(-16.987 -16.987)" fill="#f2e9df"></path>
      <path id="Path_1280" d="M30,22h2.507v2.507H30Z" transform="translate(-19.974 -16.987)" fill="#f2e9df"></path>
      <path id="Path_1281" d="M16.507,30H14v2.507h2.507Z" transform="translate(-14 -19.974)" fill="#f2e9df"></path>
      <path id="Path_1282" d="M22,30h2.507v2.507H22Z" transform="translate(-16.987 -19.974)" fill="#f2e9df"></path>
      <path id="Path_1283" d="M32.507,30H30v2.507h2.507Z" transform="translate(-19.974 -19.974)" fill="#f2e9df"></path>
    </g>
  </symbol>
</svg>

<section class="section header-pad-top pt-0 section-detail-header bg-secondary-light3 pb-0  d-none d-xl-block">
    <div class="container py-2 animFade">
        <div class="row align-items-center">
           <div class="col-auto">
               <div class="property-name">
                    <h1>{{ $property->home_name }}</h1>
                    <p>{{ $property->home_type }}, {{ $property->state }}</p>
               </div>
           </div>
           <div class="col">
                <nav class="detail-page-nav">
                    <ul class="nav" id="detail-navbar">
                        <li class="nav-item"><a href="#nav-images" class="nav-link">Images</a></li>
                        <li class="nav-item"><a href="#nav-description" class="nav-link">Description</a></li>
                        @if($property->homeFeatures->count() > 0)
                           <li class="nav-item"><a href="#nav-features" class="nav-link">Features</a></li>
                        @endif
                        @if($property->homeReviews->count() > 0)
                            <li class="nav-item"><a href="#nav-reviews" class="nav-link">Reviews</a></li>
                        @endif
                        @if($property->house_rules)
                            <li class="nav-item"><a href="#nav-homeRules" class="nav-link">Home Rules</a></li>
                        @endif
                        @if($property->direction_how_to_get_there)
                            <li class="nav-item"><a href="#nav-location" class="nav-link">Location</a></li>
                        @endif
                    </ul>
                </nav>
           </div>
           <div class="col-auto">
               <button class="btn btn-link share-btn" data-fancybox data-close-button="false" data-src="#share">
                    <i class="bi bi-box-arrow-up  mt-n1"></i> SHARE
               </button>
           </div>
       </div>
    </div>
</section>


<div data-bs-spy="scroll" data-bs-target="#detail-navbar" data-bs-root-margin="0px 0px -65%" data-bs-smooth-scroll="true" tabindex="0">
    <div id="nav-images" class="section nav-images py-0 mb-4">
        <div class="container-fluid px-0 position-relative">
           <div class="swiper detail-image-slider">
                <div class="swiper-wrapper">
                    @foreach($property->imagesVideos->where('type', 'image') as $imagesKey=>$images)
                        <div class="swiper-slide">
                            <span><img class="w-100" src="{{ asset($images->filename) }}"  loading="lazy" alt=""></span>
                        </div>
                    @endforeach
                </div>

                <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
                <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
                <button class="btn p-0 ps-3 fs-5 text-white btn-link share-btn d-block d-xl-none" data-fancybox data-close-button="false" data-src="#share">
                    <i class="bi bi-box-arrow-up  mt-n1"></i>
                </button>
           </div>
            <div class="gallery-buttons">
                <ul class="list-unstyled mb-0">
                    @if($property->imagesVideos->where('type', 'video')->count() !=0)
                        <li>
                            <button class="btn video-btn">
                                <svg class="svg-icon-play"><use xlink:href="#play"></use></svg> Video walkthrough
                            </button>
                            <div id="vid-pop" class="popup popup-video" style="display: none;">
                                <div class="mb-3 fs-5 fw-bold">Video Walkthrough</div>
                                <video class="w-auto mw-100" src="{{ asset($property->imagesVideos->where('type', 'video')[0]->filename) }}" controls></video>
                            </div>
                        </li>
                    @endif
                    <li>
                        <button class="btn gallery-btn"><svg class="svg-icon-gallery"><use xlink:href="#gallery"></use></svg> <span>Show all gallery</span></button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="m-property-info mb-4 d-xl-none">
            <div class="property-name mb-4">
                <div class="h1">{{ $property->home_name }}</div>
                <p>{{ $property->home_type }}, {{ $property->state }}</p>


            </div>
        </div>

        <div class="row">
            <div class="col-12 order-2 order-xl-0">
                <div class="key-features-wrap  mb-xl-4">
                    <ul class="key-features justify-content-around pt-xl-0 nav mb-0">
                        <li><span><img src="assets/images/occupancy.svg" alt="8 Guests"></span><span><strong>{{ $property->maximum_number_of_guests }}</strong> <br>Guests</span></li>
                        <li><span><img src="assets/images/bedrooms.svg" alt="3 Bedrooms"></span><span><strong>{{ $property->no_of_bedrooms }}</strong> <br>Bedrooms</span></li>
                        <li><span><img src="assets/images/bathrooms.svg" alt="3 Bathrooms"></span><span><strong>{{ $property->no_of_bathrooms }}</strong> <br>Bathrooms</span></li>
                        <li><span><img src="assets/images/staff.svg" alt="2 Staff"></span><span><strong>{{ $property->no_of_staff }}</strong> <br>Staff</span></li>
                        @if($property->pet_friendly)
                           <li><span><img src="assets/images/pet.svg" alt="Pet Friendly"></span><span><strong>Pet</strong> <br>Friendly</span></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col col-xl page-detail-column order-3 order-xl-0">
                <section id="nav-description" class="nav-content nav-description pb-5 pt-3">
                    <div class="content">
                        {!! $property->description !!}
                    </div>
                </section>
                @if($property->homeFeatures->count() > 0)
                    <section id="nav-features" class="nav-content nav-features pb-5">
                        <div class="nav-content-title">
                            <div class="row align-items-center gy-2">
                                <div class="col">
                                    <h2>Features</h2>
                                </div>
                                <div class="col-auto">
                                    <button class="btn py-2 btn-secondary expand-btn fw-medium">
                                        <i class="bi bi-arrows-fullscreen me-1"></i>
                                        <span>COLLAPSE ALL</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="accordion accordion-primary accordion-flush" id="featuresAccordion">
                            @if($property->homeFeatures->count() > 0)
                                @foreach($property->homeFeatures as $featuresKey=>$homeFeatures)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#c{{ $featuresKey }}">
                                            {{ ucfirst($homeFeatures->title) }}
                                        </button>
                                        </h2>
                                        <div id="c{{ $featuresKey }}" class="accordion-collapse collapse show" >
                                            <div class="accordion-body">
                                                {!! $homeFeatures->detail !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </section>
                @endif
                @if($property->homeReviews->count() > 0)
                    <section id="nav-reviews" class="nav-content nav-reviews pb-5">
                        <div class="nav-content-title pb-5">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-auto">
                                <h2>Reviews</h2>
                            </div>
                            <div class="col-12 col-md">
                                <div class="comments-logo text-end pt-0">
                                    <img src="{{ asset('assets/images/sites-logo.jpg') }}" alt="">
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="reviews-wrap">
                            <div class="row g-4 g-xl-5">
                                @if($property->homeReviews->count() > 0)
                                    @foreach($property->homeReviews as $homeReviewsKey=>$homeReviews )
                                        <div class="col-12 col-lg-6">
                                            <div class="reviews-box" id="r1">
                                                <div class="row gx-3 align-items-center review-info">
                                                    <div class="col-auto">
                                                        <img src="{{ asset('assets/images/'.$homeReviews->img) }}" alt="{{ $homeReviews->guest_name }}">
                                                    </div>
                                                    <div class="col">
                                                        <h4>{{ $homeReviews->guest_name }}</h4>
                                                        <p>{{ $property->home_type }}, {{ $property->state }}</p>
                                                    </div>
                                                </div>
                                                <div class="row gx-2 mb-2 align-items-center">
                                                    <div class="col-auto">
                                                        <div class="rating-box">
                                                            @for($i =1; $i<=$homeReviews->rating['full_rating']; $i++)
                                                                <i class="bi bi-star-fill"></i>
                                                            @endfor
                                                            @if($homeReviews->rating['half_rating'] !='')
                                                                <i class="bi bi-star-half"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="review-date">{{ date('F, Y', strtotime($homeReviews->review_date)) }}</div>
                                                    </div>
                                                </div>
                                                <div class="review-content">
                                                    <div class="review-excerpt">
                                                        <p>{{ $homeReviews->comment }}
                                                        <a href="javascript:void(0)" data-src="#r1" data-type="clone" data-fancybox>more</a>
                                                        </p>
                                                    </div>
                                                    <div class="review-full-content">
                                                        <p>{{ $homeReviews->comment }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </section>
                @endif
                @if($property->house_rules)
                    <section id="nav-homeRules" class="nav-content nav-home-rules pb-5">
                        <div class="nav-content-title pb-3 border-1 border-bottom">
                            <h2>Home Rules</h2>
                        </div>
                        <div class="content">
                            {!! $property->house_rules !!}
                        </div>
                    </section>
                @endif
                @if($property->direction_how_to_get_there)
                    <section id="nav-location" class="nav-content nav-location pb-5">
                        <div class="nav-content-title pb-3 border-1 border-bottom">
                            <h2>Location</h2>
                        </div>
                        <div class="content">
                            <div id="map" class="mb-5"></div>
                            <h4>Driving Directions from {{ $property->location }}</h4>
                            {!! $property->direction_how_to_get_there !!}
                        </div>
                    </section>
                @endif
            </div>

            <aside class="col-12 pb-4 pb-xl-0 col-xl-auto sidebar-column order-1 order-xl-0">
                <div class="sidebar-outer">
                    <div class="sidebar-box">
                        <form action="{{ route('customer.property.book') }}" method="get">
                            <input type="hidden" name="id" value="{{ $property->id }}">
                            <input type="hidden" name="maxAllowedGuests" id="maxAllowedGuests" value="{{ $property->maximum_number_of_guests }}">
                            <div class="price-box">
                                <div class="row gx-3">
                                    <div class="col">
                                        <strong>INR <span class="totalPrice">{{ number_format($total_price)}}</span> + taxes</strong>
                                        <input type="hidden" name="total_price" id="total_price"  value="{{ number_format($total_price) }}"/>
                                        <input type="hidden" name="c_price" id="c_price"  value="{{ number_format($property->cprice->price) }}"/>
                                        <input type="hidden" name="total_night" id="total_night"  value="{{ $no_of_nights }}"/>
                                        <input type="hidden" name="total_guest" id="total_guest"  value="{{ $total_guest_count }}"/>
                                        <ul class="list-unstyled mb-0">
                                            <li><span class="totalNight">{{ $no_of_nights }}</span>&nbsp;<span class="noOfNightText">@if($no_of_nights ==1)night @else nights @endif</span></li>
                                            <li><span class="totalGuest">{{ $total_guest_count }}</span>&nbsp;<span class="totalGuestText">@if($total_guest_count==1)guest @else guests @endif</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-auto">
                                        INR {{ number_format($property->cprice->price) }}/night
                                    </div>
                                </div>
                            </div>

                            <div class="form-box mt-0">
                                <div class="row">
                                    <div class="col-12 calendar-column dropdown">
                                        <div class="row g-0">
                                            <div class="col-6">
                                                <button class="btn btn-start-date btn-control toggle-date" type="button">
                                                    <span>Arrival</span>
                                                    <strong>{{  date('jS F', strtotime($checkInDate)) }}</strong>
                                                    <input type="hidden" name="check_in_date" id="check_in_date" value="{{  $checkInDate }}">
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button class="btn btn-end-date btn-control toggle-date" type="button">
                                                    <span>Departure</span>
                                                    <strong>{{  date('jS F', strtotime($checkOutDate)) }}</strong>
                                                    <input type="hidden" name="check_out_date" id="check_out_date" value="{{ $checkOutDate }}">
                                                </button>
                                            </div>
                                        </div>
                                        <button class="w-100 d-none calendar-btn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5" data-bs-auto-close="outside" data-bs-reference="parent" type="button"></button>
                                        <div class="dropdown-menu p-0  dropdown-menu-end">
                                            <input id="input-calendar" type="text" class="d-none">
                                        </div>
                                    </div>

                                    <div class="col-12 col-xl">
                                        <div class="dropdown dropdown-guests">
                                            <button class="btn guests-btn btn-control" type="button"  data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5"  data-bs-auto-close="outside"  data-bs-display="static" type="button">
                                                <span>{{ $total_guest_count }} @if($total_guest_count==1)guest @else guests @endif</span>
                                                {{-- <strong><span>5</span> Adults |  <span>5</span> Children</strong> --}}
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="list-unstyled m-0 guestCounter">
                                                    @foreach($guestOptions as $guestOptionKey=>$guestOptionValue)

                                                        <li>
                                                            <div class="row flex-nowrap align-items-center">
                                                                <div class="col">
                                                                    <div class="guests-title">
                                                                        <strong style="font-style: normal;">{{$guestOptionValue['title']}}</strong>
                                                                        <small style="font-style: normal;">{{$guestOptionValue['sub_title']}}</small>
                                                                    </div>
                                                                </div>

                                                                <div class="col-auto">
                                                                    <div class="counter">
                                                                        <a href="javascript:void(0)" class="btn counter-col c-minus">
                                                                            <span class="icon-minus" onclick="updateGuestCount('minus', {{$guestOptionKey}})"></span>
                                                                        </a>

                                                                        <div class="counter-col">
                                                                            <input type="hidden" id="guestCount{{$guestOptionKey}}" class="counter-input" name="{{$guestOptionValue['name']}}" data-counter-type="children" max="{{ $guestOptionValue['max']}} }}" min="{{ $guestOptionValue['min']}} }}" value="{{$guestOptionValue['count']}}">
                                                                            <strong style="font-style: normal;" class="count-val" id="guestCountShow{{$guestOptionKey}}" >{{$guestOptionValue['count']}}</strong>
                                                                        </div>

                                                                        <a href="javascript:void(0)" class="btn counter-col c-plus">
                                                                            <span class="icon-plus"  onclick="updateGuestCount('plus', {{$guestOptionKey}})"></span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="dropdown-action">
                                                    <a href="javascript:void(0)" style="font-style: normal;" class="clear-btn" onclick="clearGuests()">Clear guests</a>
                                                    {{-- <a href="javascript:void(0)" class="close-dropdown" onclick="closeDropdown()"><i class="bi bi-x-lg"></i></a> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 pt-2">
                                        <div class="submit-wrap">
                                            <button class="btn btn-primary rounded-pill w-100" type="submit">BOOK NOW</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <div class="form-links d-none d-xl-block">
                          <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('faqs') }}">FAQ's</a></li>
                            <li><a href="javascript:void(0)" data-fancybox data-src="#enquire">Enquire Now</a></li>
                          </ul>
                        </div>
                    </div>
                    <p class="d-none d-xl-block">Or speak to a travel advisor on the phone:</p>
                    <a href="tel:+91 98100 74777" class="btn d-none d-xl-block border border-secondary border-1 btn-outline-secondary rounded-pill d-flex align-items-center justify-content-center">
                      <i class="icon-phone-call fs-5 me-2 align-self-center"></i>  +91 98100 74777
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>
<div id="share" class="share-pop popup w-100" style="display: none;max-width: 484px;">
    <div class="pop-heading">
        <div class="row">
            <div class="col">
                <h2>Share this place</h2>
            </div>
            <div class="col-auto">
                <button onclick="Fancybox.close()" class="close-btn btn fs-5 lh-1"><div class="bi-x"></div></button>
            </div>
        </div>
    </div>

    <div class="pop-content clearfix">
        <ul class="list-unstyled share-link-list m-0">
            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}&amp;t=La Avila - Villa - 09" target="_blank"><i class="bi bi-facebook"></i> Facebook</a></li>
            <li><a href="https://twitter.com/intent/tweet?url={{ url()->current() }}" target="_blank"><i class="bi bi-twitter-x"></i> Twitter X</a></li>
            <li><a href="https://wa.me/?text={{ url()->current() }}" data-action="share/whatsapp/share" target="_blank"><i class="bi bi-whatsapp"></i> WhatsApp</a></li>
            <li><a href="mailto:?body={{ url()->current() }}&amp;subject=V are Family - Bliss Cottage - Nainital Hills, Uttarakhand"><i class="bi bi-envelope"></i> Email</a></li>
        </ul>
    </div>
</div>

<style>
    @media (max-width:1199px){
        .footer-main {
            padding-bottom: 200px;
        }
    }
</style>
<div class="m-fixed-info d-xl-none">
    <div class="container">
        <div class="m-booking-info">
            <div class="row  align-items-center">
                <div class="col">
                    <button class="btn">
                        <div class="btn-col">
                            <i class="icon-calendar-start"></i><span  class="m-departure">{{  date('jS F', strtotime($checkInDate)) }}</span>
                        </div>
                        <div class="btn-col">
                            <i class="icon-calendar-end"></i><span class="m-arrival">{{  date('jS F', strtotime($checkOutDate)) }}</span>
                        </div>
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn p-0 ps-3 fs-5 text-secondary btn-link share-btn d-block d-xl-none" data-fancybox data-close-button="false" data-src="#share">
                        <i class="bi bi-box-arrow-up  mt-n1"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="price-box px-0">
            <div class="row gx-3">
                <div class="col">
                    <strong>INR <span class="totalPrice">{{ number_format($total_price) }}</span> + taxes</strong>
                    <ul class="list-unstyled mb-0">
                        <li><span class="totalNight">{{ $no_of_nights }}</span>&nbsp;<span class="noOfNightText">@if($no_of_nights ==1)night @else nights @endif</span></li>
                        <li><span class="totalGuest">{{ $total_guest_count }}</span> &nbsp;<span class="totalGuestText">@if($total_guest_count==1) guest @else guests @endif</span></li>
                    </ul>
                </div>
                <div class="col-auto">
                    INR {{ number_format($property->cprice->price) }}/night
                </div>
            </div>
        </div>


        <div class="row g-0">
            <div class="col">
                <button class="btn px-2 w-100 btn-primary">BOOK NOW</button>
            </div>
            <div class="col">
                <button class="btn px-2 w-100 btn-secondary" href="javascript:void(0)" data-fancybox data-src="#enquire">ENQUIRE NOW</button>
            </div>
        </div>

    </div>
</div>
</form>


<div id="enquire" class="query-popup popup" style="max-width:500px;">
    <div class="ic-info mb-4 pb-2">
        <h5>Make an enquiry</h5>
        <h3 class="m-0">{{ $property->home_name }} <em><small>{{ $property->home_type }}, {{ $property->state }}</small></em></h3>
    </div>
    <div class="row gy-4 gx-5">
        <div class="col-12">
            <form id="enquiry_form">
                @csrf
                <div class="row g-3">
                    <input type="hidden" class="form-control" name="location_id" id="location_id" value="{{ $property->location_id }}">
                    <input type="hidden" class="form-control" name="property_id" id="property_id" value="{{ $property->id }}">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="mb-1">Your Name <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" name="enquiry_person_name" id="enquiry_person_name" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="mb-1">Phone Number <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" name="enquiry_person_mobile" id="enquiry_person_mobile" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="mb-1">Email Address <sup class="text-danger">*</sup></label>
                            <input type="text" class="form-control" name="enquiry_person_email" id="enquiry_person_email" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label class="mb-1">Message <sup class="text-danger">*</sup></label>
                            <textarea name="enquiry_person_message" id="enquiry_person_message" cols="" rows="3" class="form-control" autocomplete="off"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <button class="btn btn-primary rounded-pill fw-bold w-100 submit_enquiry" >Submit Enquiry</button>
                        </div>
                    </div>
                </div>
                <div class="row text-center mt-4">
                    <div class="col-12">
                        <p class="d-none d-xl-block">Or speak to a travel advisor on the phone:</p>
                        <a href="tel:+91 98100 74777" class="btn d-none d-xl-block border border-secondary border-1 btn-outline-secondary rounded-pill d-flex align-items-center justify-content-center">
                            <i class="icon-phone-call fs-5 me-2 align-self-center"></i>  +91 98100 74777
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        let slideData = []
        let sliders = Object.entries(JSON.parse('{!! json_encode($property->imagesVideos->where("type", "image")) !!}'))
        if(sliders.length !=0){
            sliders.forEach(item=>{
                slideData.push({src:item[1].filename})
            });
        }
        $(document).on("click",".gallery-btn", function() {
            let $index = $(this).index();
            // Image gallery with thumbnails
            Fancybox.show(slideData,{
               Images:{
                protected: true,
               },
               Thumbs: {
                    type: "classic",
                    showOnStart: false,
                },
            });
        });

        $(document).on("click",".video-btn", function() {
            Fancybox.show([{ src: "#vid-pop", type: "inline" }],{

            });
        });

        function resetDateInput(){
            $(".btn-start-date span").text("Arrival Date");
            $(".btn-end-date span").text("Departure Date");
        }

        let calendarBtn = new bootstrap.Dropdown(".calendar-btn");
        let guestsBtn = new bootstrap.Dropdown(".guests-btn");

        let inputCalendar = document.getElementById('input-calendar');
        window.datepicker = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,
            clearButton: true,
            // minNights: 4,
            topbarPosition: 'bottom',
            disabledDates: JSON.parse('{!! json_encode($propertyUnavailableDates) !!}'),
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(".btn-start-date strong").text(startDate);
                $(".btn-end-date strong").text(endDate);
                calendarBtn.toggle();
            } ,
            onDayClick: function() {
                if(this.start){
                    $(".btn-end-date strong").text("Departure");
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(".btn-start-date strong").text(startDate);
                    $('.m-departure').text(startDate)
                    $('#check_in_date').val(fecha.format(this.start, `Do MMM YYYY`));
                }
                if(this.end){
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $('.m-arrival').text(endDate)
                    $(".btn-end-date strong").text(endDate);
                    $('#check_out_date').val(fecha.format(this.end, `Do MMM YYYY`))
                }

                if(!this.start && !this.end){
                    resetDateInput()
                }

                if(this.start && this.end){



                    let millisecondsPerDay = 1000 * 60 * 60 * 24;
                    let millisBetween = this.end - this.start;
                    let days = millisBetween / millisecondsPerDay;


                    $('.totalNight').text(days+' ');
                    $('#total_night').val(days);

                    let price = $('#c_price').val();

                    price = parseInt(price.replace(',', ''))

                    console.log(price)
                    price = parseInt(price)*days;

                    const curr = new Intl.NumberFormat('en-IN', {
                        maximumFractionDigits: 0,
                        currency: 'INR'
                    })
                    price =  curr.format(price)

                    $('#total_price').val(price);
                    $('.totalPrice').text(price);

                    if(days ==1){
                        $('.noOfNightText').text('night');
                    }
                    else{
                        $('.noOfNightText').text('nights');
                    }

                }
            }
        });

        //console.log("datepicker", datepicker)

        $("#clear-input-calendar").on("click",function(){
            $(".btn-start-date strong").text("Arrival");
            $(".btn-end-date strong").text("Departure");
        });


        $(".toggle-date").on("click", function(e){
            e.stopPropagation();
            guestsBtn.hide();
            calendarBtn.toggle();
        })

        // Counter

        // Initialize and display the map
        // Create the script tag, set the appropriate attributes
        let script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBe52aO4If59Vz2wRpoVUzio1wDt9c_xsI&callback=initMap';
        script.async = true;
        window.initMap = function() {
            let centerCoordinates = { lat: 40.712776, lng: -74.005974 };
            let map = new google.maps.Map(document.getElementById('map'), {
                center: centerCoordinates,
                zoom: 12
            });
        }
        document.head.appendChild(script);

        $(document).on('click', '.submit_enquiry', function (e) {
            e.preventDefault();
            // Construct the URL with the search query as a parameter
            $.ajax({
                url: "customer/enquiry",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
                data: {
                    enquiry_person_name: $('#enquiry_person_name').val(),
                    enquiry_person_mobile: $('#enquiry_person_mobile').val(),
                    enquiry_person_email: $('#enquiry_person_email').val(),
                    enquiry_person_message: $('#enquiry_person_message').val(),
                    checkin_date: $('#check_in_date').val(),
                    checkout_date: $('#check_out_date').val(),
                    total_price: $('#total_price').val(),
                    no_of_nights: $('#total_night').val(),
                    total_guest: $('#total_guest').val(),
                    location_id: $('#location_id').val(),
                    property_id: $('#property_id').val(),
                },
                success: function(res) {
                    toastr.success(res.message);
                    Fancybox.close();
                    $('#enquiry_form .form-control').val('');
                },
                error: function(res) {
                    $('#enquiry_form .form-control').removeClass('border-danger');
                    let errors = JSON.parse(JSON.stringify(res.responseJSON.errors));
                    Object.entries(errors).forEach(error=>{
                        $('#'+error[0]).addClass('border-danger');
                    })
                }
            });
        });

    })

    function updateGuestCount(type, i) {
        if($('#total_guest').val() >=1 ){
            let guestCountInput = document.getElementById('guestCount' + i);
            let guestCountDisplay = document.getElementById('guestCountShow' + i);
            let guestCount = parseInt(guestCountInput.value);
            if (type === 'plus') {
                let currentGuestCunt  = parseInt($('#total_guest').val()) ;
                if(currentGuestCunt <=  parseInt($('#maxAllowedGuests').val())){
                    guestCount = Math.min(guestCount + 1, parseInt($('#maxAllowedGuests').val()));
                    guestCountInput.value = guestCount;
                    guestCountDisplay.innerHTML = guestCount;
                    // Update total guest count
                    updateTotalGuests();
                }

            }
            else {
                let c  = parseInt($('#total_guest').val()) - 1;
                if(c>=1){
                    guestCount = Math.max(guestCount - 1, 0);
                    guestCountInput.value = guestCount;
                    guestCountDisplay.innerHTML = guestCount;
                    // Update total guest count
                    updateTotalGuests();
                }
            }
        }
    }

    function updateTotalGuests() {
        let totalGuests = 0;
        document.querySelectorAll('.counter-input').forEach(function(input) {
            totalGuests += parseInt(input.value);

        });
        if(totalGuests >0){
            document.querySelector('.guests-btn span').innerText = (totalGuests ==1)?totalGuests+' guest': totalGuests+' guests';
            $('#total_guest').val(totalGuests);
            $('.totalGuest').text(totalGuests);
            $('.totalGuestText').text(totalGuests ==1?'guest':'guests');
        }
    }
</script>
<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBe52aO4If59Vz2wRpoVUzio1wDt9c_xsI&callback=initMap" async defer></script> -->

@endsection
