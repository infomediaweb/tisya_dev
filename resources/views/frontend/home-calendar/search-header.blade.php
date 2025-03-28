@php
   
@endphp
<div class="main-search-outer py-5 header-calendar-search" data-main-search>
    <span role="button" class="ms-auto close-btn lh-1 fs-1 fw-light ps-3"><i class="icon-close"></i></span>
    <div class="main-search">
        <div class="row gy-3 gx-4">
           
            <div class="col-12 col-lg-3 d-flex field-col dropdown position-relative">
                <button class="btn form-select text-start search-field" type="button"  data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5"  data-bs-display="static" data-search-id="location">
                    <span class="data-text data-text-location-sec">{{ $location_name ?? 'Location' }} </span>
                    <input type="hidden" id="locationID_sec" value="{{ $filter_type ?? '' }}" name="locationID_sec">
                    <input type="hidden" id="location_id_sec" value="{{ $location_name ?? '' }}" name="location_id_sec">
                    <input type="hidden" id="checkin_date_sec" value="{{ $checkin_date ?? '' }}" name="checkin_date_sec">
                    <input type="hidden" id="checkout_date_sec" value="{{ $checkout_date ?? '' }}"  name="checkout_date_sec">
                </button>
                <div class="dropdown-menu">
                   <ul class="list-unstyled mb-0">
                    @foreach($states_with_locations as $state)
                                <li class="li-heading">
                                    <a class="location-item-sec" href="#" data-locidSec="{{ $state->name ?? '' }}" data-locationid="state" data-locationname="{{ $state->name ?? '' }}">
                                        {{ $state->name ?? '' }} (All Properties)
                                    </a>
                                </li>
                            @foreach($state->locations as $location)
                                <li>
                                    <a class="dropdown-item location-item-sec" data-locidSec="{{ $location->location_name ?? '' }}" href="#" data-locationid="location" data-locationname="{{ $location->location_name ?? '' }}">
                                        {{ $location->location_name ?? '' }}
                                    </a>
                                </li>
                            @endforeach
                    @endforeach
                   </ul>
                </div>
            </div>
            <div class="col-12 col-lg field-col d-flex position-relative date-column">
                <button class="btn text-start btn-checkin search-field form-control"  data-search-id="dates">
                    <div class="search-field-value js-checkin-text">
                    @if (!empty($checkin_date))
                            
                            {{ date("jS M", strtotime($checkin_date))  }}
                    @else
                        Check-In
                    @endif
                    </div>                     
                </button>
                <button class="btn text-start btn-checkout search-field form-control" data-search-id="dates">                   
                    <div class="search-field-value js-checkout-text">
                        @if (!empty($checkout_date))
                            {{ date("jS M", strtotime($checkout_date))  }}
                        @else
                        Check-Out
                        @endif
                    </div>
                </button>
                <div class="custom-dropdown calendar-dropdown">
                     <input id="header-calendar" type="text" style="display:none;"  value="@if(session('checkin_date')){{ date('Y-m-d', strtotime(session('checkin_date'))) . ' - ' . date('Y-m-d', strtotime(session('checkout_date'))) }}@endif"/>
                </div>
            </div>
            <div class="col-12 col-lg field-col position-relative">
                <button class="btn text-start search-field search-field-guest form-select">
                    <div class="search-field-value" total-guests-sec data-search-id="guests">
                        @if (!empty($total_guests))
                        {{ $total_guests }} {{ $total_guests == 1 ? 'Guest' : 'Guests' }}
                        @else
                        Guests 
                        @endif
                    </div>
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
                                        <a href="javascript:void(0)" class="btn counter-col c-minus-sec disabled" data-type-sec="adults" data-minus-sec>
                                            <span class="icon-minus"></span>
                                        </a>
                                        <div class="counter-col">
                                            <input type="hidden" class="adults-count adultsCountSec" name="" value="{{ $adultsCount ?? 1 }}" data-adults-value>
                                            <strong class="count-val count-val-sec">{{ $adultsCount ?? 1 }}</strong>
                                        </div>
                                        <a href="javascript:void(0)" class="btn counter-col c-plus c-plus-sec" data-type-sec="adults" data-plus-sec>
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
                                        <a href="javascript:void(0)" class="btn counter-col c-minus-sec " data-type-sec="children" data-minus-sec>
                                            <span class="icon-minus"></span>
                                        </a>
                                        <div class="counter-col">
                                            <input type="hidden" class="children-count childrenCountSec" name="" value="{{ $childrenCount ??  0 }}" data-children-value>
                                            <strong class="count-val count-val-sec">{{ $childrenCount ??  0 }}</strong>
                                        </div>
                                        <a href="javascript:void(0)" class="btn counter-col c-plus c-plus-sec" data-type-sec="children" data-plus-sec>
                                            <span class="icon-plus"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>                        
                    </ul>                    
                </div>
            </div> 
            <div class="col-12 col-lg-auto field-col position-relative">
                <button class="btn btn-primary btn-search btn-search-sec w-100">
                    <i class="icon-search"></i><span>Search</span>
                </button> 
            </div>           
        </div>
    </div>
</div>

<script>
     document.addEventListener("DOMContentLoaded", function () {
        let parentElClass = '.header-calendar-search';    
        let inputCalendar = document.getElementById('header-calendar');
        function resetDateInput(){
            $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
          datepickerHeader.clear();
          $('#checkin_date_sec').val('');
          $('#checkout_date_sec').val('');        
        }        
       
        window.datepickerHeader = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,           
            // clearButton: true,
            // minNights: 4,
            clearButton: true,
            showTopbar: true,
            topbarPosition: 'bottom',
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(parentElClass).find(".js-checkin-text").text(startDate);
                $(parentElClass).find(".js-checkout-text").text(endDate);   
                //calendarBtn.toggle();             
            } ,
            onDayClick: function() {                
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
                    $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
                    const checkin_date_sec = fecha.format(this.start, `Do MMM YYYY`);
                    const checkout_date_sec = fecha.format(this.end, `Do MMM YYYY`);
                    $('#checkin_date_sec').val(checkin_date_sec);
                    $('#checkout_date_sec').val(checkout_date_sec);
                }
                if(!this.start && !this.end){
                    resetDateInput()
                }
            }         
        });

        $(datepickerHeader.datepicker).find(".datepicker__clear-button").text("Clear Dates")
        $(datepickerHeader.datepicker).find(".datepicker__buttons").append('<button type="button" class="btn btn-link close-datepicker">Close</button>');

        $(document).on("click",".close-datepicker", function(){
            $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
        })

        inputCalendar.addEventListener(
            "afterClear",
            function () {
                resetDateInput();
            },
            false
        );

        $(".clear-dates").on("click",function(e){
            e.stopPropagation();
            resetDateInput();
        });


        $(document).on('click', '[data-small-search]', function(e){
            let dataType = $(e.target).data('type')
            setTimeout(() => {
                if(dataType == 'search'){
                    $(`[data-search-id="location"]`).trigger('click')
                }
                else{
                    if(dataType == 'dates'){
                        $(`[data-search-id="${dataType}"]`).first().trigger('click')
                    }
                    else{
                        $(`[data-search-id="${dataType}"]`).trigger('click')
                    } 
                } 
            }, 100)
        })
       
/* Calendar */
        const dropdownItems = document.querySelectorAll('.location-item-sec');
        const dataTextSpan = document.querySelector('.data-text-location-sec'); 
        const locationIDField = $('#locationID_sec')
        const location_id_sec = $('#location_id_sec')
        dropdownItems.forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault(); 
                const locationName = this.getAttribute('data-locationname'); 
                dataTextSpan.textContent = locationName; 
                const locationID = this.getAttribute('data-locationid');
                locationIDField.val(locationID); 
                const locatIDSec = this.getAttribute('data-locidSec');
                console.log(locatIDSec,"locatIDSec");
                location_id_sec.val(locatIDSec); 
            });
        });

    initializeGuestCounterSec();
    $(document).on('click', '[data-type-sec]', function () {
        let dataType = $(this).data('type-sec');
        countersec($(this), dataType);
    });
    function initializeGuestCounterSec() {
        let adultsCountSec = $('.adultsCountSec').val() || 1; 
        let childrenCountSec = $('.childrenCountSec').val() || 0;
        $('.adultsCountSec').val(adultsCountSec);
        $('.childrenCountSec').val(childrenCountSec);
        $('.adultsCountSec').parent().find('.count-val-sec').text(adultsCountSec);
        $('.childrenCountSec').parent().find('.count-val-sec').text(childrenCountSec);
        $('.adultsCountSec').val() > 1 
            ? $('[data-type-sec="adults"][data-minus-sec]').removeClass('disabled') 
            : $('[data-type-sec="adults"][data-minus-sec]').addClass('disabled');
        updateTotalGuestsSec();
    }

    function countersec(el, dataType) {
        let inputEl = el.parent().find(`input[data-${dataType}-value]`);
        console.log(inputEl,"inputEl");
        let minusEl = el.parent().find('[data-minus-sec]');
        let plusEl = el.parent().find('[data-plus-sec]');

        let inputValue = parseInt(inputEl.val()) || 0;
        if (el.hasClass('c-plus-sec')) {
           // if (dataType === 'children' && inputValue >= 2) return; 
            inputValue++;
        } else {
            inputValue--;
        }
        inputValue = Math.max(0, inputValue); 
        if (dataType === 'adults' && inputValue < 1) {
            inputValue = 1; 
        }
        inputEl.val(inputValue);
        el.parent().find('.count-val-sec').text(inputValue);
        if (dataType === 'adults') {
            inputValue > 1 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
        } else {
            inputValue > 0 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
        }
        // if (dataType === 'children') {
        //     inputValue >= 2 ? plusEl.addClass('disabled') : plusEl.removeClass('disabled');
        // }
        updateTotalGuestsSec();
    }
    function updateTotalGuestsSec() {
        let adultsCountSec = $('.adultsCountSec').val() || 0;
        let childrenCountSec = $('.childrenCountSec').val() || 0;
        //let totalGuest = Number(adultsCountSec) + Number(childrenCountSec);
        let totalGuest = Number(adultsCountSec);
        let totalGuestInputText = totalGuest === 0 
            ? 'Add guests' 
            : totalGuest > 1 
            ? `${totalGuest} Guests` 
            : `${totalGuest} Guest`;

        $('[total-guests-sec]').text(totalGuestInputText);
    }



        $(document).on('click', '.btn-search-sec', function(e){
            e.preventDefault();
            const location_name_sec  = $('.data-text-location-sec').text();
            const locationID_sec  = $('#locationID_sec').val();
            const location_id_sec  = $('#location_id_sec').val();
            const checkin_date_sec  = $('#checkin_date_sec').val();
            const checkout_date_sec  = $('#checkout_date_sec').val();

            const adultsCountSec = $('.adultsCountSec').val() || 0;
            const childrenCountSec = $('.childrenCountSec').val() || 0;
           // const totalGuestSec = Number(adultsCountSec) + Number(childrenCountSec);
            const totalGuestSec = Number(adultsCountSec);

            if(location_name_sec ==''){
                alert('Please Select Destination');
                return false;
            }else{
                const data = {
                    location_name: location_name_sec,
                    filter_type: locationID_sec,
                    location: location_id_sec,
                    type: 'listPropertiesSearch',
                    checkin_date: checkin_date_sec,
                    checkout_date: checkout_date_sec,
                    adultsCount: adultsCountSec,
                    childrenCount: childrenCountSec,
                    total_guests: totalGuestSec,
                };
                dynamicAjaxRequestSec(data);
            }  
        });
        // function dynamicAjaxRequestSec(data) {
        //     $.ajax({
        //         url: "/location-session-save", 
        //         method: 'GET', 
        //         data: data,
        //         success: function(response) {
        //             let baseUrl = "/";
        //             console.log(baseUrl,"baseUrl");
        //             var redirectUrl = baseUrl + "property-list/" + data.location_name; 
        //             window.location.href = redirectUrl;

        //             fbq('track', 'FindLocation', { 
        //                 location : data.location_name ,
        //                 checkin_date: $('#checkin_date_sec').val(), 
        //                 checkout_date: $('#checkout_date_sec').val(), 
        //             });
                    
        //             fbq('track', 'Search', { 
        //                 location : data.location_name ,
        //                 checkin_date: $('#checkin_date_sec').val(), 
        //                 checkout_date: $('#checkout_date_sec').val(), 
        //                 num_adults: $('.adultsCountSec').val() || 0, 
        //                 num_children: $('.childrenCountSec').val() || 0, 
        //                 num_infants: 0
        //             });
        //         },
        //         error: function(xhr) {
        //             alert('An error occurred while processing your request.');
        //         }
        //     });
        // }
        
        function dynamicAjaxRequestSec(data) {
            const searchURL = "{{ route('property-list') }}?" + $.param(data);
                    window.location.href = searchURL; 

                    fbq('track', 'FindLocation', { 
                         location : data.location_name ,
                         checkin_date: $('#checkin_date_sec').val(), 
                         checkout_date: $('#checkout_date_sec').val(), 
                   });
                    fbq('track', 'Search', { 
                        location : data.location_name ,
                        checkin_date: $('#checkin_date_sec').val(), 
                         checkout_date: $('#checkout_date_sec').val(), 
                        num_adults: $('.adultsCountSec').val() || 0, 
                        num_children: $('.childrenCountSec').val() || 0, 
                        num_infants: 0
                    });
        }


    });
</script>

