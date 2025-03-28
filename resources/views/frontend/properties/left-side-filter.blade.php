@php


@endphp

<aside class="col-12 col-xl-auto align-self-start">
    <div class="card filter-card" id="sidebar">
        <div class="card-header py-3"><i class="icon-filter"></i>Filters <span role="button" class="d-lg-none ms-auto close-btn lh-1 fs-3 fw-light ps-3">&times;</span></div>
        <div class="card-header bg-primary text-white py-3 "><span class="mlocation">
            @if($filter_type == 'location' && !empty($location_name))
            {{ $location_name ?? '' }}
        @endif
        
        </span></div>
        <div class="card-body p-0">
            <div class="nano filter-content-wrap">
                <div class="nano-content">
                    <div class="filter-box fb-price">
                        <h3>Price Range</h3>
                        <div class="price-range">
                            <div class="range-outer mt-3">
                                <div id="range-slider"></div>
                            </div>

                            <div class="row gx-2 mt-4">
                                <div class="col">
                                    <ul class="nav">
                                        <li>Min. &#8377;</li>
                                        <li class="pi-min-val"><span></span></li>
                                    </ul>
                                </div>
                                <div class="col">
                                    <ul class="nav">
                                        <li>Max. &#8377;</li>
                                        <li class="pi-max-val"><span></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter-box fb-rooms">
                        <h3>Rooms</h3>
                        <div class="row gx-2 align-items-center">
                            <div class="col">
                                <h4><small>Number of Bedrooms</small></h4>
                            </div>
                            <div class="col-auto">
                                <ul class="list-unstyled mb-0 d-flex align-items-center counter-box" ng-cloak>
                                <li>
                                    <button class="btn rooms-decrement" id="bedRoomMinus">-</button>
                                </li>
                                <li class="rc-value">{{ request()->has('rooms') ? request()->input('rooms') : 1 }}</li>
                                <li>
                                    <button class="btn rooms-increment" id="bedRoomPlus">+</button>
                                </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="filter-box fb-nav">
                        <h3>Property Type</h3>
                        <ul class="nav-list list-unstyled mb-0">
                            <li>
                                <div class="ch-box">
                                    <input type="checkbox" id="homeTypeAll" name="homeType[]" value="propertyType"
                                        @if(request()->has('property_type') && in_array('propertyType', explode(',', request()->input('property_type')))) checked @endif />
                                    <label for="homeTypeAll">All Homes</label>
                                </div>
                            </li>
                            @foreach(App\Models\TblHomeType::where('status', 1)->get() as $homeTypeKey => $homeTypeDetail)
                                <li>
                                    <div class="ch-box">
                                        <input type="checkbox" id="homeType{{$homeTypeKey+2}}" name="homeType[]" value="{{$homeTypeDetail->url_key}}"
                                            @if(request()->has('property_type') && in_array($homeTypeDetail->url_key, explode(',', request()->input('property_type')))) checked @endif />
                                        <label for="homeType{{$homeTypeKey+2}}">{{$homeTypeDetail->name}}</label>
                                    </div>
                                </li>
                            @endforeach    
                        </ul>
                    </div>

                    <div class="filter-box fb-nav">
                        <h3>Location</h3>
                        <ul class="nav-list list-unstyled mb-0">
                            <li>
                                <div class="ch-box">
                                    <input type="checkbox" id="locationId1" name="location[]" value="all" class="loc"
                                        @if(request()->has('location') && in_array('all', explode(',', request()->input('location')))) checked @endif />
                                    <label for="locationId1">All Locations</label>
                                </div>
                            </li>
                            @foreach(App\Models\TblLocation::where('status', 1)->get() as $locationKey => $locationDetail)
                                <li>
                                    <div class="ch-box">
                                        <input type="checkbox" id="locationId{{$locationKey+2}}" name="location[]" value="{{$locationDetail->location_name}}" class="loc"
                                            @if(request()->has('location') && in_array($locationDetail->location_name, explode(',', request()->input('location')))) checked @endif />
                                        <label for="locationId{{$locationKey+2}}">{{$locationDetail->location_name}}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <div class="filter-box fb-nav">
                        <h3>Top Filters</h3>
                        <ul class="nav-list list-unstyled mb-0">
                            @foreach(App\Models\TblAmenities::where('show_on_filter', 1)->where('status', 1)->get() as $tagKey => $tagDetail)
                                <li>
                                    <div class="ch-box">
                                        <input type="checkbox" id="tag{{$tagKey+1}}" 
                                               value="{{$tagDetail->id}}" 
                                               name="amenities[]" 
                                               @if(request()->has('amenities') && in_array($tagDetail->id, explode(',', request()->input('amenities')))) 
                                                   checked 
                                               @endif
                                        />
                                        <label for="tag{{$tagKey+1}}">{{$tagDetail->amenities_name}}</label>
                                    </div>
                                </li>
                            @endforeach    
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="card-footer p-0 border-bottom d-xl-none">
            <div class="row g-0">
                <div class="col">
                    <a href="javascript:void(0)" class="btn rounded-0 w-100 btn-light close-btn clear-filter" style="border:0!important; ">
                        CLEAR
                    </a>
                </div>
                <div class="col">
                    <button class="btn close-btn rounded-0 w-100 btn-primary">
                        DONE
                    </button>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
    let total_guests = "@isset($total_guests) {{ $total_guests }} @endisset";
    let adultsCount = "@isset($adultsCount) {{ $adultsCount }} @endisset";
    let childrenCount = "@isset($childrenCount) {{ $childrenCount }} @endisset";
    let guestCount = "@isset($guestCount) {{ $guestCount }} @endisset";
    let minPrice = 3000; 
    let maxPrice = 150000;
    let roomCount = 1;
    document.addEventListener('DOMContentLoaded', () => {
       
$(document).ready(function () {
    function addCommasNew(nStr) {
            nStr += '';
            var x = nStr.split('.');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }
        
        if($('#range-slider').length){
            window.rslider = document.getElementById('range-slider');
            noUiSlider.create(rslider, {
                start: [3000, 150000], //60000
                connect: true,
                step: 500,
                range: {
                    'min': 3000,
                    'max': 150000
                }
            });
            rslider.noUiSlider.on('update', function (values, handle, unencoded, tap, positions, noUiSlider) {
                var minVal = parseInt(values[0]).toFixed(0);
                var maxVal = parseInt(values[1]).toFixed(0);
                $(".pi-min-val span").text(addCommasNew(minVal));
                $(".pi-max-val span").text(addCommasNew(maxVal));
                $("#min_val").val(minVal);
                $("#max_val").val(maxVal);
            });

            rslider.noUiSlider.on('change', function (values) {
                minPrice = parseInt(values[0]).toFixed(0);
                maxPrice = parseInt(values[1]).toFixed(0);
                type = 'filter';
               // page = 1;
                updateFilters();
            }); 
        }

    $("#bedRoomPlus").click(function () {
        roomCount++;
        $(".rc-value").text(roomCount);
        updateFilters();
    });

    $("#bedRoomMinus").click(function () {
        if (roomCount > 1) {
            roomCount--;
            $(".rc-value").text(roomCount);
            updateFilters();
        }
    });

        
    $(document).on("change", "input[name='homeType[]'], input[name='location[]'], input[name='amenities[]']", function () {
        updateFilters();
    });


    $(document).on("change", "#homeTypeAll", function () {
    if ($(this).is(":checked")) {
        $("input[name='homeType[]']").not(this).prop("checked", false);
    }
    updateFilters();
});

        $(document).on("click",'.highToLow', function(){
           sort_by = 'high_to_low';
           //type ='sorting';
           $('.sortText').text('High To Low');
           updateFilters();
        })
        
        $(document).on("click",'.lowToHigh', function(){
           sort_by = 'low_to_high';
           //type ='sorting';
           $('.sortText').text('Low To High');
           updateFilters();
        })


// Uncheck "All Homes" when any other checkbox is selected
$(document).on("change", "input[name='homeType[]']:not(#homeTypeAll)", function () {
    if ($(this).is(":checked")) {
        $("#homeTypeAll").prop("checked", false);
    }
    updateFilters();
});


$(document).on("change", "#locationId1", function () {
    if ($(this).is(":checked")) {
        $("input[name='location[]']").not(this).prop("checked", false);
    }
    updateFilters();
});

// Uncheck "All Locations" when any other checkbox is selected
$(document).on("change", "input[name='location[]']:not(#locationId1)", function () {
    if ($(this).is(":checked")) {
        $("#locationId1").prop("checked", false);
    }
    updateFilters();
});


    
$(document).on("click", ".loadPaginationP", function (e) {
    e.preventDefault();
    let page = $(this).data("page");
    let queryString = window.location.search.replace("?", "") + "&page=" + page;

    fetchProperties(queryString, true); 
});
    
});
})

function fetchProperties(queryString, append = false) {
    fetch("{{ URL('/') }}/ajax-filter-property?" + queryString, {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.html) {
            if (append) {
               /// $('.items').append(data.html); 
                $('.items').empty('').html(data.html);

            } else {
                $('.items').html(data.html); 
            }

            $(".totStayCount").html(
                `${data.propertyCount} out of ${data.total} ${data.total === 1 ? 'Stay' : 'Stays'}`
            );

            if(data.total == data.propertyCount){
                $(".loadPaginationP").hide(); 
            }else{
                if (data.nextPage) {
                $(".loadPaginationP")
                    .data("page", data.nextPage)
                    .show()
                    .html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="12.827" height="16.137" viewBox="0 0 12.827 16.137">
                    <g id="arrow-down" transform="translate(-8.5 -4.5)">
                        <path id="Path_88" data-name="Path 88" d="M14.924,4.5a.621.621,0,0,1,.62.621l-.009,13.4,4.733-4.724a.621.621,0,1,1,.877.879l-5.793,5.782a.621.621,0,0,1-.877,0L8.682,14.673a.621.621,0,1,1,.877-.879l4.735,4.726L14.3,5.12A.621.621,0,0,1,14.924,4.5Z" transform="translate(0 0)" fill="#003a4c" fill-rule="evenodd"/>
                    </g>
                </svg>
                <span>Load more stays</span>
            `);
            } 
            }
            
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
        } else {
            console.error("Invalid response format", data);
            $('.items').html("<p class='text-center'>No properties found.</p>"); 
        }
    })
    .catch(error => console.error("Error fetching data:", error));
}





function updateFilters() {
        let selectedTypes = [];
        let selectedLocations = [];
        let selectedAmenities = [];
        let newParams = new URLSearchParams(window.location.search);

        // Get selected Home Types
        $("input[name='homeType[]']:checked").each(function () {
            selectedTypes.push($(this).val());
        });

        // Get selected Locations
        $("input[name='location[]']:checked").each(function () {
            selectedLocations.push($(this).val());
        });

        $("input[name='amenities[]']:checked").each(function () {
        selectedAmenities.push($(this).val());
    });
        
        // Set parameters
        if (selectedTypes.length > 0) {
            newParams.set("property_type", selectedTypes.join(","));
        } else {
            newParams.delete("property_type");
        }

        if (selectedLocations.length > 0) {
            newParams.set("location", selectedLocations.join(","));
        } else {
            newParams.delete("location");
        }

    if (selectedAmenities.length > 0) {
        newParams.set("amenities", selectedAmenities.join(",")); // Multiple checked
    } else {
        newParams.delete("amenities");
    }
    if (typeof sort_by !== "undefined" && sort_by !== "") {
        newParams.set("sort_by", sort_by);
    } else {
        newParams.delete("sort_by");
    }

        // newParams.set("min_price", minPrice);
        // newParams.set("max_price", maxPrice);
        // newParams.set("rooms", roomCount);

    let defaultMinPrice = 3000; // Set your default min price
    let defaultMaxPrice = 150000; // Set your default max price
    let defaultRooms = 1; // Default number of rooms

    if (minPrice !== defaultMinPrice) {
        newParams.set("min_price", minPrice);
    } else {
        newParams.delete("min_price");
    }

    if (maxPrice !== defaultMaxPrice) {
        newParams.set("max_price", maxPrice);
    } else {
        newParams.delete("max_price");
    }

    if (roomCount !== defaultRooms) {
        newParams.set("rooms", roomCount);
    } else {
        newParams.delete("rooms");
    }



        let newUrl = window.location.pathname + "?" + newParams.toString();
        window.history.pushState(null, "", newUrl);

        fetchProperties(newParams.toString());
    }

    

</script>