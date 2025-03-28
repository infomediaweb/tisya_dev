@php


@endphp

<aside class="col-12 col-xl-auto align-self-start">
    <div class="card filter-card" id="sidebar">
        <div class="card-header py-3"><i class="icon-filter"></i>Filters <span role="button" class="d-lg-none ms-auto close-btn lh-1 fs-3 fw-light ps-3">&times;</span></div>
        <div class="card-header bg-primary text-white py-3 "><span class="mlocation">{{ $locations->location_name ?? "" }}</span></div>
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
                                <li class="rc-value">1</li>
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
                                    <input type="radio" id="homeType1" name="homeType" value="all"  />
                                    <label for="homeType1">All Homes</label>
                                </div>
                            </li>
                            @foreach(App\Models\TblHomeType::where('status', 1)->get() as $homeTypeKey=>$homeTypeDetail)
                                <li>
                                    <div class="ch-box">
                                        <input type="radio" id="homeType{{$homeTypeKey+2}}" name="homeType" id="homeType" value="{{$homeTypeDetail->id}}" />
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
                                    <input type="radio" id="locationId1" name="location"  value="all"  class="loc" />
                                    <label for="locationId1">All Locations</label>
                                </div>
                            </li>
                            @foreach(App\Models\TblLocation::where('status', 1)->get() as $locationKey=>$locationDetail)
                                <li>
                                    <div class="ch-box">
                                        <input type="radio" id="locationId{{$locationKey+2}}" name="location"  value="{{$locationDetail->id}}" class="loc"/>
                                        <label for="locationId{{$locationKey+2}}">{{$locationDetail->location_name}}</label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>


                    <div class="filter-box fb-nav">
                        <h3>Top Filters</h3>
                        <ul class="nav-list list-unstyled mb-0">
                            @foreach(App\Models\TblAmenities::where('show_on_filter', 1)->where('status', 1)->get() as $tagKey=>$tagDetail)
                                <li>
                                    <div class="ch-box">
                                        <input type="radio" id="tag{{$tagKey+1}}" value="{{$tagDetail->id}}" id="tagId" name="tagId">
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
    let propertyType = 'all';
    let minPrice =  3000;
    let maxPrice =150000;
    let noOfBedrooms = 1;
    let tag = 'all';
    let locationId = 'all';
    let sort_by = 'low_to_high';
    let checkin_date = '';
    let checkout_date = '';
    let guest = '';
    let page = 1;
    let type = 'filter';
    let state = '';
    let collectionId = '';
 
    let total_guests = "@isset($total_guests) {{ $total_guests }} @endisset";
    let adultsCount = "@isset($adultsCount) {{ $adultsCount }} @endisset";
    let childrenCount = "@isset($childrenCount) {{ $childrenCount }} @endisset";
    let guestCount = "@isset($guestCount) {{ $guestCount }} @endisset";

 
 
    document.addEventListener('DOMContentLoaded', () => {
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
                page = 1;
                getPropertyList();
            });
        }
        
        $("input[name='homeType']").change(function () {
            propertyType = $("input[name='homeType']:checked").val();
            type = 'filter';
            getPropertyList();
        });
        
        
        $("input[name='location']").change(function () {
            type = 'filter';
            page = 1;
            locationId = $("input[name='location']:checked").val();
            getPropertyList(); 
        });
        
        $("input[name='tagId']").change(function () {
            tag = $("input[name='tagId']:checked").val();
            type = 'filter';
            getPropertyList(); 
        });
        
        $("#bedRoomPlus").click(function () {
            noOfBedrooms = noOfBedrooms + 1;
            $('.rc-value').text(noOfBedrooms);
            type = 'filter';
            getPropertyList();
        });
        
        $("#bedRoomMinus").click(function () {
            if(noOfBedrooms > 1){
                noOfBedrooms = noOfBedrooms - 1;
                $('.rc-value').text(noOfBedrooms);
                type = 'filter';
                getPropertyList();
            }
        });
    })

    function getPropertyList(){
        let params = {
            propertyType: typeof propertyType !== 'undefined' ? propertyType : '',
            minPrice: typeof minPrice !== 'undefined' ? minPrice : '',
            maxPrice: typeof maxPrice !== 'undefined' ? maxPrice : '',
            tag: typeof tag !== 'undefined' ? tag : '',
            location: typeof locationId !== 'undefined' ? locationId : '',
            noOfBedrooms:noOfBedrooms,
            sort_by:sort_by,
            checkin_date:checkin_date,
            checkout_date:checkout_date,
            page:page,
            type:type,
            state:state,
            guest:guest,
            collection:collectionId,
            total_guests:total_guests,
            adultsCount:adultsCount,
            childrenCount:childrenCount,
            guestCount:guestCount
        };
        $.ajax({
            url: "{{ URL('/') }}/ajax-filter-property",
            type: 'get',
            data: params,
            success: function(res) {
                if(res.type =='filter' || res.type =='sorting'){
                    $('.items').empty('').html(res.html);
                    $('.mlocation').text(res.location_name);
                }
                else{
                    $('.items').append(res.html);
                }
                //$('.totStayCount').text(res.propertyCount);
                $('.totStayCount').text(
                    res.propertyCount + ' ' + (res.propertyCount == 1 ? 'Stay' : 'Stays')
                );
                ScrollTrigger.refresh();
            },
            error: function(xhr, status, error) {
                console.error('Error in AJAX request:', error);
            }
        });
        
        $(".clear-filter").click(function () {
            propertyType = 'all';
            minPrice =  3000;
            maxPrice =150000;
            noOfBedrooms = 1;
            tag = 'all';
            locationId = 'all';
            sort_by = 'low_to_high';
            checkin_date = '';
            checkout_date = '';
            guest = '';
            page = 1;
            type = 'filter';
            state = '';
            collectionId = '';
            getPropertyList();
            $('#sidebar input[type="radio"], #sidebar input[type="checkbox"]').prop("checked", false);
        });
    }
</script>