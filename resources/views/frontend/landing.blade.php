@extends('layout.landing.mainladingpage')
@section('content')
<style>
    .form-wrap{
        background:rgba(var(--bs-white-rgb),.5);
    }
</style>

<style>
    
    #success-message {
        display: none;
        margin-top: 10px;
    }
</style>
<div id="success-message" class="alert alert-success"></div>
    <section class="section flex-wrap section-hero section-bg align-items-center">
            <div class="section-bg">
                <div class="swiper hero-banner-swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                           <img src="https://www.tisyastays.com/storage/home_banner/6798ce631862c_202A0917%20(1).jpg">
                        </div>
                    </div>
                </div>
            </div>
            <div class="container hero-content-container position-relative">
                <div class="row justify-content-end">
                    <div class="col-12 col-lg-5">
                        <div class="hero-card form-wrap p-4 rounded-5">
                        <form action="{{route('landingenquire')}}" method="POST"  id="ajaxForm">
                               @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" placeholder="Name*"  @error('name') is-invalid @enderror"  name="name" value="{{ old('name') }}" />
                                </div>
                                <div class="col-12">
                                    <input class="form-control @error('phone') is-invalid @enderror" type="number" placeholder="Mobile*" name="phone"  value="{{ old('phone') }}"/>
                                      </div>
                                <div class="col-12">
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="Email Address*" name="email"  value="{{ old('email') }}"/>
                                </div>
                                <div class="col-6">
                                <label class="text-black">Check-in*</label>
                                <input class="form-control @error('check-in') is-invalid @enderror " type="date"  name="check-in" value="{{ old('check-in') }}" />
                                </div>
                                <div class="col-6">
                                   <label class="text-black">Check-out*</label>
                                    <input class="form-control  @error('check-out') is-invalid @enderror " type="date"  name="check-out" value="{{old('check-out')}}" />
                                </div>
                                 <div class="col-md-12">
            
                            <div class="form-group form-captcha">
                                <div class="row gx-3 mb-4 align-items-center">
                                    <div class="col col-lg">
                                        <input type="text" name="captcha" id="captcha"
                                            class="form-control {{ $errors->has('captcha') ? 'is-invalid' : '' }}"
                                            placeholder="Captcha*" value="{{ old('captcha') }}">
                                    </div>
                                    <div class="col-auto">
                                        <div class="row g-0 align-items-center">
                                            <div class="col">
                                                <div class="captcha-box bg-primary p-2" id="captcha_div" style="width:80px; border-radius:4px; text-align:center">
                                                    <span class="captcha-text" id="captcha_text" style="user-select: none;">{{ old('captcha_text') }}</span>
                                                    <input type="hidden" name="captcha_hidden" value="{{ old('captcha_hidden') }}"
                                                        id="captcha_hidden" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="refreshBtn btn" id="captcha_reload" >
                                                    <svg style="width: 16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M142.9 142.9c-17.5 17.5-30.1 38-37.8 59.8c-5.9 16.7-24.2 25.4-40.8 19.5s-25.4-24.2-19.5-40.8C55.6 150.7 73.2 122 97.6 97.6c87.2-87.2 228.3-87.5 315.8-1L455 55c6.9-6.9 17.2-8.9 26.2-5.2s14.8 12.5 14.8 22.2l0 128c0 13.3-10.7 24-24 24l-8.4 0c0 0 0 0 0 0L344 224c-9.7 0-18.5-5.8-22.2-14.8s-1.7-19.3 5.2-26.2l41.1-41.1c-62.6-61.5-163.1-61.2-225.3 1zM16 312c0-13.3 10.7-24 24-24l7.6 0 .7 0L168 288c9.7 0 18.5 5.8 22.2 14.8s1.7 19.3-5.2 26.2l-41.1 41.1c62.6 61.5 163.1 61.2 225.3-1c17.5-17.5 30.1-38 37.8-59.8c5.9-16.7 24.2-25.4 40.8-19.5s25.4 24.2 19.5 40.8c-10.8 30.6-28.4 59.3-52.9 83.8c-87.2 87.2-228.3 87.5-315.8 1L57 457c-6.9 6.9-17.2 8.9-26.2 5.2S16 449.7 16 440l0-119.6 0-.7 0-7.6z"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-dark" type="submit">SUBMIT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>



    <section class="section section-swiper property-section">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-sm">
                        <h2> {{  $home_banner->subtitle ?? '' }}</h2>
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
                                        <a 
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
                                            <a  class="badge z-1 text-decoration-none 
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
                                        <a 
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
        </div>
    </section>
    
    
    <section class="section bg-primary text-white">
        <div class="container">
            <div class="row justify-content-center g-3 gx-5">
                    <div class="col-12 col-lg-auto">
                        <div class="row align-items-center g-2">
                            <div class="col-auto">
                                <h2 class="m-0 text-white">150k+</h2>
                            </div>     
                            <div class="col text-white">
                                Guest Hosted
                            </div>                 
                        </div>
                    </div>
                    <div class="col-12 col-lg-auto">
                        <div class="row align-items-center g-2">
                            <div class="col-auto">
                                <h2 class="m-0 text-white">30k+</h2>
                            </div>     
                            <div class="col text-white">
                                Bookings
                            </div>                 
                        </div>
                    </div>
                    <div class="col-12 col-lg-auto">
                        <div class="row align-items-center g-2">
                            <div class="col-auto">
                                <h2 class="m-0 text-white">4.8 <i class="bi bi-star-fill"></i></h2>
                            </div>     
                            <div class="col text-white">
                                Avg. Rating
                            </div>                 
                        </div>
                    </div>
                    <div class="col-12 col-lg-auto">
                        <div class="row align-items-center g-2">
                            <div class="col-auto">
                                <h2 class="m-0 text-white">30%</h2>
                            </div>     
                            <div class="col text-white">
                                Repeated Guests
                            </div>                 
                        </div>
                    </div>
                </div>
        </div>
    </section>
    
    
     
    <section class="section section-testimonials">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-12">
                    <div class="swiper testimonials">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                              <div class="testimonialBox">
                                  <div class="row">
                                      <div class="col-auto">
                                            <img src="{{ asset('storage/review/images/6768f85c48f3e_google.png') }}"
                                          style="border-radius:50%; width:80px; border:1px solid #999;" />
                                        </div>
                                      <div class="col">
                                        <p>Nestled within Assagao, it’s a beautiful, cozy villa done up wonderfully. The team at Tisya was very helpful not only with the stay but also in helping plan our days and helping with the cab. Overall an excellent experience.
                                         The rooms are very comfortable with one on the ground floor and the other up a few stairs. The kitchen is fully functional and the common places are fantastically furnished and great to lounge in. There’s a lot of attention to small details both in the stay and hospitality. Highly recommend this stay.</p>
                                        <h5>Eshan</h5>
            
                                      </div>
                                  </div>
                              </div>
                              <div class="testimonialBox">
                                  <div class="row">
                                      <div class="col-auto">
                                            <img src="{{ asset('storage/review/images/6768f805e0b54_airbnb.png') }}"
                                          style="border-radius:50%; width:80px; border:1px solid #999;" />
                                        </div>
                                      <div class="col">
                                        <p>The interiors are fabulous and is exactly as depicted in the images. The villa property in itself is great with the pool being a lovely add on! Was great to spend the weekend here. Tajal & her team did a great job of taking care of us really well. Cant wait to get back to tisyaa!!</p>
                                        <h5>Rohan Singh</h5>
            
                                      </div>
                                  </div>
                              </div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
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
                           <img loading="lazy" src="{{ asset('assets/images/mig.jpg') }}" alt="">
                        </div>
                        <div class="col-auto me-auto">
                           <img loading="lazy" src=" {{ asset('assets/images/mw.jpg') }}" alt="">
                        </div>
                        <div class="col-auto me-auto">
                           <img loading="lazy" src="{{ asset('assets/images/theman.jpg') }}" alt="">
                        </div>
                        <div class="col-auto me-auto">
                           <img loading="lazy" src="{{ asset('assets/images/ad.jpg') }}" alt="">
                        </div>
                    </div>
                </div>                   
            </div>
        </div>
    </section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
    document.addEventListener('DOMContentLoaded', () => { 
        $(".offer-code .btn").each(function(){
            var clipboard = new ClipboardJS(this);
            clipboard.on('success', function(e) {
                $(e.trigger).addClass("active");

                setTimeout(() => {
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

        const testmonialsSwiper = new Swiper('.testimonials', {
            loop: true,
            slidesPerView: 2,
            spaceBetween: 50,
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
<script>
        $(document).ready(function() {
            setTimeout(function() {
                $("#success-message").fadeOut("300");
            }, 2000);  
        });
    </script>
<script>
   $(document).ready(function () {
    function generateCaptcha() {
        const digits = Math.floor(Math.random() * 900) + 100; 
        let characters = '';
        const alphanumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for (let i = 0; i < 3; i++) {
            characters += alphanumeric.charAt(Math.floor(Math.random() * alphanumeric.length));
        }
        return digits + characters;
    }

    function updateCaptcha() {
        const newCaptcha = generateCaptcha();
        $('#captcha_text').text(newCaptcha);  
        $('#captcha_hidden').val(newCaptcha); 
    }

    updateCaptcha(); 

    $("#captcha_reload").on("click", function (e) {
        e.preventDefault(); 
        updateCaptcha(); 
    });

    $("#ajaxForm").on("submit", function (e) {
        e.preventDefault(); 

        let form = $(this);
        let formData = form.serialize();

        $(".is-invalid").removeClass("is-invalid");

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val()
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        form[0].reset(); 
                        updateCaptcha(); 
                    });
                } else {
                    updateCaptcha(); 
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, message) {
                        let inputField = $('[name="' + field + '"]');
                        inputField.addClass("is-invalid");
                    });

                    if (errors.hasOwnProperty("captcha")) {
                        updateCaptcha(); 
                    }
                }
            }
        });
    });

    $("input, textarea").on("blur", function () {
        if ($(this).hasClass("is-invalid")) {
            $(this).removeClass("is-invalid");
        }
    });
});

</script>



<script>
$(document).ready(function() {
            $(".date-input").on("focus", function() {
                $(this).attr("type", "date");
            }).on("blur", function() {
                if (!$(this).val()) {
                    $(this).attr("type", "text");
                }
            });
        });
        

    $(document).ready(function () {
        $(".card-property").on("click", function (event) {
            event.preventDefault(); // Prevents navigation if it's a link
            $("html, body").animate({ scrollTop: 0 }, "slow");
        });
    });
</script>












@endsection