

@extends('layout.main')
@section('content')
<section class="section properties-listing">
    
    <div class="container">
        <div class="section-heading">
            <h1>Properties in <span class="mlocation">{{ $locations->location_name ?? "" }}</span></h1>
            <div class="total-stays">
               
               <span class="totStayCount"> {{ $totalStays }} {{ $totalStays == 1 ? 'Stay' : 'Stays' }} </span> 
               
            </div>
        </div>
        @if($properties->count() >0)
        <div class="row gx-5">
            @include('frontend.properties.left-side-filter')
            <div class="col-12 col-xl properties-col" style="min-width: 0">
                <div class="row align-items-center filter-row">
                    <div class="col filter-col">
                        <a href="javascript:void(0)" class="mch-filter"><span class="icon-filter"></span> Filter</a>
                    </div>
                    <div class="col-auto">
                        <div class="fl-group">
                          
                            <ul class="m-0 d-flex list-unstyled">
                                <li class="dropdown">
                                    <a class="btn dropdown-toggle px-3" href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="true">
                                       <span class="sortText"> {{ $sortOrder === 'high_to_low' ? 'Price: High to Low' : 'Price: Low to High' }} </span>
                                        <span class="icon-chevron-down"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <ul class="list-unstyled mb-0">
                                            <li>
                                                <a class="dropdown-item {{ $sortOrder === 'low_to_high' ? 'fw-bold' : '' }} lowToHigh"
                                                   href="javascript:void(0)">
                                                    Price: Low to High
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ $sortOrder === 'high_to_low' ? 'fw-bold' : '' }} highToLow"
                                                   href="javascript:void(0)">
                                                    Price: High to Low
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="items">
                    <div class="properties-listing">
                        @if(!empty($properties) && $properties->count() >0)
                            @foreach($properties as $property)
                                <div  class="property-item" target="_blank">
                                    <div class="row">
                                        <div class="col-12 col-lg-5 position-relative col-xxl-4">
                                         <a href="{{ route('property-detail', ['home_type' => strtolower($property->home_type), 'slug' => $property->url_key]) }}" target="_blank" class="swiper swiper-property-image">
                                                <div class="swiper-wrapper">
                                                    @if($property->homeImageVideo->isNotEmpty())
                                                    @foreach ($property->homeImageVideo->where('type', 'image') as $media)
    
                                                            <div class="swiper-slide">
                                                                <div class="imgBox">
                                                                    <img loading="lazy" src="{{ isset($media->website_image) ? asset($media->website_image) : '' }}" class="w-100" alt="{{ $media->title ?? "" }}">
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
                                        </div>
                                        <div class="col-12 col-lg align-self-center py-3">
                                            <h2>{{ $property->home_name }}</h2>
                                            <div class="location-state">
                                                        {{ $property->locationData->location_name ?? '' }}, {{ $property->state }}
                                                    </div>
                                            <ul class="nav property-short-info my-3 my-lg-4">
                                               <li><span class="icon-users"></span>Upto {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }}</li>
                                                <li><span class="icon-bed"></span>{{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Room' : $property->no_of_bedrooms . ' Rooms' }}</li>
                                                 <li><span class="icon-bath"></span>{{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}</li>
                                            </ul>
                                            <ul class="amenities-list list-unstyled m-0">
                                                @foreach ($property->amenities->take(4) as $amenity)
                                                    <li>
                                                        <div class="amenities-small-icon">
                                                            <img src="{{ asset('storage/amenities/' . $amenity->amenities_image) }}"
                                                                alt="{{ $amenity->amenities_name }}">
                                                        </div>
                                                        <span>{{ $amenity->amenities_name }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            @if ($property->amenities && $property->amenities->count() > 4)
                                                <div class="more text-primary pt-3 pt-lg-4 fw-bold">
                                                    +{{ $property->amenities->count() - 4 }} more
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-12 col-lg-auto">
                                            <div class="card price-card h-100">
                                                <div class="card-body">
                                                     <h3>From ₹{{ number_format($property->per_night_price * $property->noOfNights) }}</h3>
                                            
                                                 
                                                    <small>per night  +  taxes</small>
                                                </div>
                                                <div class="card-footer">
                                                    
                                             <a href="{{ route('property-detail', ['home_type' => strtolower($property->home_type), 'slug' => $property->url_key]) }}" target="_blank"  class="btn btn-primary">
                                                        View Detail</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if($properties->hasMorePages())
                        <div class="properties-loader text-center mt-5">
                            <a class="btn btn-link loadPaginationProperty" data-page="2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12.827" height="16.137" viewBox="0 0 12.827 16.137">
                                    <g id="arrow-down" transform="translate(-8.5 -4.5)">
                                        <path id="Path_88" data-name="Path 88" d="M14.924,4.5a.621.621,0,0,1,.62.621l-.009,13.4,4.733-4.724a.621.621,0,1,1,.877.879l-5.793,5.782a.621.621,0,0,1-.877,0L8.682,14.673a.621.621,0,1,1,.877-.879l4.735,4.726L14.3,5.12A.621.621,0,0,1,14.924,4.5Z" transform="translate(0 0)" fill="#003a4c" fill-rule="evenodd"/>
                                    </g>
                                </svg>
                                <span>Load more stays</span>
                            </a>
                        </div>
                    @endif
                </div>    

            </div>


        </div>
        @else 
        <div class="container">
            <div class="section-heading">
                <div class="text-center">
                    No Property Found!
                </div>
            </div>
        </div>
    @endif
    </div>
    
</section>


<script>
    locationId = "{{$locations->id}}";
    document.addEventListener('DOMContentLoaded', () => {
        

        new Swiper('.swiper-property-image', {
            spaceBetween: 30,
            pagination: {
                el: ".swiper-property-image .swiper-pagination",
                dynamicBullets: true,
                clickable: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },
            mousewheel: {
                enabled: true,
                forceToAxis: true
            },
        });

        ScrollTrigger.matchMedia({
            "(min-width: 1300px)": function () {
                ScrollTrigger.create({
                    pin: '.filter-card',
                    start: 'top top+=120px',
                    end: function(){
                        let h = ($(".properties-col").height() - $('.filter-card').height()) - 120;
                        return `${h}px top`
                    },
                   //markers: true,
                });
                $(".nano").nanoScroller({ alwaysVisible: true });

            },
            "(max-width: 991px)": function () {
                $(".nano").nanoScroller({ destroy: true });
            }
        })

        $(document).on("click",'.mch-filter', function(){
            $('body').addClass('mch-filter-open');
        })

        $(document).on("click",'.filter-card .close-btn', function(){
            $('body').removeClass('mch-filter-open');
        })
        
        //------------------------filter & sorting code--------------------//
        $(document).on("click",'.highToLow', function(){
           sort_by = 'high_to_low';
           type ='sorting';
           $('.sortText').text('High To Low');
           getPropertyList();
        })
        
        $(document).on("click",'.lowToHigh', function(){
           sort_by = 'low_to_high';
           type ='sorting';
           $('.sortText').text('Low To High');
           getPropertyList();
        })
        
        $(document).on("click",'.loadPaginationProperty', function(){
           filterWithSorting =  'no'; 
           page = $(this).data('page');
           type ='pagination';
           getPropertyList();
           $(this).remove();
        })
    })
</script>
@endsection
