@php
    $location_data = getLocationData();
@endphp
<div class="main-search-outer hero-search">
    <div class="main-search">
        <div class="row gy-3 gx-4">
            <div class="col-12 col-lg-3 d-flex field-col dropdown position-relative">
                <button class="btn form-select text-start search-field" type="button"  data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5"  data-bs-display="static">
                    <span class="data-text data-text-location">Location</span>
                    <input type="hidden" id="locationID"  name="locationID">
                    <input type="hidden" id="checkin_date"  name="checkin_date">
                    <input type="hidden" id="checkout_date"  name="checkout_date">
                </button>
                <div class="dropdown-menu">
                   <ul class="list-unstyled mb-0">
                        @if (!empty($location_data) && $location_data->isNotEmpty())
                        @foreach ($location_data as $location)
                            <li>
                                <a class="dropdown-item location-item fw-bold text-danger" href="#" data-locationid="{{ $location->id ?? '' }}" data-locationname="{{ $location->location_name ?? '' }}">
                                    Goa
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item location-item" href="#" data-locationid="{{ $location->id ?? '' }}" data-locationname="{{ $location->location_name ?? '' }}">
                                    {{ $location->location_name ?? '' }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                   </ul>
                </div>
            </div>
            <div class="col-12 col-lg field-col d-flex position-relative date-column">
                <button class="btn text-start btn-checkin search-field form-control">
                    <div class="search-field-value js-checkin-text">Check-In</div>                     
                </button>
                <button class="btn text-start btn-checkout search-field form-control">                   
                    <div class="search-field-value js-checkout-text">Check-Out</div>
                </button>
                <div class="custom-dropdown calendar-dropdown">
                     <input id="hero-calendar" type="text" style="display:none;" />
                </div>
            </div>
            <div class="col-12 col-lg field-col position-relative">
                <button class="btn text-start search-field search-field-guest form-select">
                    <div class="search-field-value" total-guests>Guests</div>
                </button>  
                <div class="custom-dropdown guests-counter guests-dropdown pHStatic">
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
                                        <a href="javascript:void(0)" class="btn counter-col c-minus disabled" data-type="adults" data-minus>
                                            <span class="icon-minus"></span>
                                        </a>
                                        <div class="counter-col">
                                            <input type="hidden" class="adults-count adultsCount" name="" value="1" data-adults-value>
                                            <strong class="count-val">1</strong>
                                        </div>
                                        <a href="javascript:void(0)" class="btn counter-col c-plus" data-type="adults" data-plus>
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
                                        <a href="javascript:void(0)" class="btn counter-col disabled c-minus" data-type="children" data-minus>
                                            <span class="icon-minus"></span>
                                        </a>
                                        <div class="counter-col">
                                            <input type="hidden" class="children-count childrenCount" name="" value="" data-children-value>
                                            <strong class="count-val">0</strong>
                                        </div>
                                        <a href="javascript:void(0)" class="btn counter-col c-plus" data-type="children" data-plus>
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
                <button class="btn btn-primary btn-search btn-search-first w-100">
                    <i class="icon-search"></i><span>Search</span>
                </button> 
            </div>           
        </div>
    </div>
</div>
<script>
     document.addEventListener("DOMContentLoaded", function () {
        let parentElClass = '.hero-search';    
        let inputCalendar = document.getElementById('hero-calendar');
        function resetDateInput(){
          $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
          datepickerHero.clear();
          datepickerHero.clearDatepicker();
          $('#checkin_date').val('');
          $('#checkout_date').val('');
        }        
       
        window.datepickerHero = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,           
            // minNights: 4,
            clearButton: true,
            showTopbar: true,
            topbarPosition: 'bottom',
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
                $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);   
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
                    const checkin_date = fecha.format(this.start, `Do MMM YYYY`);
                    const checkout_date = fecha.format(this.end, `Do MMM YYYY`);
                    $('#checkin_date').val(checkin_date);
                    $('#checkout_date').val(checkout_date);

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

/* Calendar */
        const dropdownItems = document.querySelectorAll('.location-item');
        const dataTextSpan = document.querySelector('.data-text-location'); 
        const locationIDField = $('#locationID')
        dropdownItems.forEach(item => {
            item.addEventListener('click', function (event) {
                event.preventDefault(); 
                const locationName = this.getAttribute('data-locationname'); 
                dataTextSpan.textContent = locationName; 
                const locationID = this.getAttribute('data-locationid');
                locationIDField.val(locationID); 
            });
        });

        initializeGuestCounter();
        $(document).on('click', '[data-type]', function () {
            let dataType = $(this).data('type');
            counter($(this), dataType);
        });
        function initializeGuestCounter() {
            let adultsCount = $('.adultsCount').val() || 1; 
            let childrenCount = $('.childrenCount').val() || 0;
            $('.adultsCount').val(adultsCount);
            $('.childrenCount').val(childrenCount);
            $('.adultsCount').parent().find('.count-val').text(adultsCount);
            $('.childrenCount').parent().find('.count-val').text(childrenCount);
            $('.adultsCount').val() > 1 
                ? $('[data-type="adults"][data-minus]').removeClass('disabled') 
                : $('[data-type="adults"][data-minus]').addClass('disabled');
            updateTotalGuests();
        }

        function counter(el, dataType) {
            let inputEl = el.parent().find(`input[data-${dataType}-value]`);
            let minusEl = el.parent().find('[data-minus]');
            let plusEl = el.parent().find('[data-plus]');

            let inputValue = parseInt(inputEl.val()) || 0;
            if (el.hasClass('c-plus')) {
                if (dataType === 'children' && inputValue >= 2) return; 
                inputValue++;
            } else {
                inputValue--;
            }
            inputValue = Math.max(0, inputValue); 
            if (dataType === 'adults' && inputValue < 1) {
                inputValue = 1; 
            }
            inputEl.val(inputValue);
            el.parent().find('.count-val').text(inputValue);
            if (dataType === 'adults') {
                inputValue > 1 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
            } else {
                inputValue > 0 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
            }
            if (dataType === 'children') {
                inputValue >= 2 ? plusEl.addClass('disabled') : plusEl.removeClass('disabled');
            }
            updateTotalGuests();
        }
        function updateTotalGuests() {
            let adultsCount = $('.adultsCount').val() || 0;
            let childrenCount = $('.childrenCount').val() || 0;
            let totalGuest = Number(adultsCount) + Number(childrenCount);
            let totalGuestInputText = totalGuest === 0 
                ? 'Add guests' 
                : totalGuest > 1 
                ? `${totalGuest} Guests` 
                : `${totalGuest} Guest`;

            $('[total-guests]').text(totalGuestInputText);
        }

        $(document).on('click', '.btn-search-first', function(e){
            e.preventDefault();
            const location_name  = $('.data-text-location').text();
            const locationID  = $('#locationID').val();
            const checkin_date  = $('#checkin_date').val();
            const checkout_date  = $('#checkout_date').val();

            const adultsCount = $('.adultsCount').val() || 0;
            const childrenCount = $('.childrenCount').val() || 0;
            const totalGuest = Number(adultsCount) + Number(childrenCount);

            if(locationID ==''){
                alert('Please Select Destination');
                return false;
            }else{
                const data = {
                    location_name: location_name,
                    city_id: locationID,
                    checkin_date: checkin_date,
                    checkout_date: checkout_date,
                    adultsCount: adultsCount,
                    childrenCount: childrenCount,
                    total_guests: totalGuest,
                };
                dynamicAjaxRequest(data);
            }  
        });
        // function dynamicAjaxRequest(data) {
        //     $.ajax({
        //         url: "/location-session-save", 
        //         method: 'GET', 
        //         data: data,
        //         success: function(response) {
        //             var redirectUrl = "/property-list/" + data.location_name; 
        //             window.location.href = redirectUrl;
        //         },
        //         error: function(xhr) {
        //             alert('An error occurred while processing your request.');
        //         }
        //     });
        // }
        
        function dynamicAjaxRequest(data) {
            const searchURL = "{{ route('property-list') }}?" + $.param(data);
                    window.location.href = searchURL; 
                        fbq('track', 'FindLocation', { 
                        location : data.location_name ,
                        checkin_date: $('#checkin_date').val(), 
                        checkout_date: $('#checkout_date').val(), 
                    });
                    
                   fbq('track', 'Search', { 
                        location : data.location_name ,
                        checkin_date: $('#checkin_date').val(), 
                        checkout_date: $('#checkout_date').val(), 
                        num_adults: $('.adultsCount').val() || 0, 
                        num_children: $('.childrenCount').val() || 0, 
                        num_infants: 0
                    });
        }
    });

</script>