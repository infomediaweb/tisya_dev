@php
    // Fetch data from tbl_location
    $locations = DB::table('tbl_location')
                ->whereNull('deleted_at') // Exclude soft deleted records
                ->where('status', 1)      // Exclude records with status 0
                ->get();

@endphp

@php
    $form_data = session()->get('request_data');
@endphp



<div class="booking-form">
    <form action="{{ route('booking') }}" method="POST">
        @csrf
        <div class="row ff-row g-0">
            <div class="col-12 col-xl">
                <div class="dropdown dropdown-select">
                    <input type="text" name="destination" class="d-none select-input">
                    <button class="btn destination-btn btn-control" type="button"  data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5"  data-bs-display="static">
                        <span class="data-text">@if(session()->has('search_parameters') && session('search_parameters')['destination'] !='') {{ session('search_parameters')['destination'] }} @else Destination  @endif</span>
                        <i class="icon-location"></i>
                    </button>
                    <ul class="dropdown-menu">
                        @if(isset($form_data['location']))
                            <li><a style="font-style: normal;" class="dropdown-item" href="javascript:void(0)" data-value="{{ $form_data['location'] }}">{{ $form_data['location'] }}</a></li>
                        @else
                            @foreach($locations as $location)
                                <li><a style="font-style: normal;" class="dropdown-item" href="javascript:void(0)" data-value="{{ $location->location_name }}">{{ $location->location_name }}</a></li>
                            @endforeach
                        @endif
                    </ul>

                </div>
            </div>
            <div class="col-12 col-xl-auto calendar-column dropdown">
                <div class="row g-0">
                    <div class="col-6 col-xl">
                        <button class="btn btn-start-date btn-control toggle-date" type="button">
                            <span>@if(session()->has('search_parameters') && session('search_parameters')['arrival_date'] !='') {{ session('search_parameters')['arrival_date'] }} @else Arrival  @endif </span>
                            <i class="icon-calendar-start"></i>
                        </button>
                    </div>
                    <div class="col-6 col-xl">
                        <button class="btn btn-end-date btn-control toggle-date" type="button">
                            <span>@if(session()->has('search_parameters') && session('search_parameters')['departure_date'] !='') {{ session('search_parameters')['departure_date'] }} @else Departure  @endif </span>
                            <i class="icon-calendar-end"></i>
                        </button>
                    </div>
                </div>
                <button class="w-100 d-none calendar-btn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5" data-bs-auto-close="outside" data-bs-reference="parent" type="button" data-bs-display="static"></button>
                <div class="dropdown-menu p-0">
                    <input id="input-calendar"  type="text" class="d-none">
                </div>
            </div>
            <!-- Hidden input fields for arrival and departure dates -->
            <input id="arrival_date" name="arrival_date" value="@if(session()->has('search_parameters') && session('search_parameters')['arrival_date'] !='') {{ session('search_parameters')['arrival_date'] }}  @endif"   hidden>
            <input id="departure_date" name="departure_date" value="@if(session()->has('search_parameters') && session('search_parameters')['departure_date'] !='') {{ session('search_parameters')['departure_date'] }}  @endif"   hidden>

                <div class="col-12 col-xl">
                    <div class="dropdown dropdown-guests">
                        <button class="btn guests-btn btn-control" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5" data-bs-auto-close="outside" data-bs-display="static" type="button">
                            <span>@if(session()->has('search_parameters') && session('search_parameters')['total_guests'] !='') {{ session('search_parameters')['total_guests'] }} @if(session('search_parameters')['total_guests'] ==1) Guest @else Guests @endif @else 1 Guest @endif </span>
                            <i class="icon-guests"></i>
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
                                                        <input type="hidden" id="guestCount{{$guestOptionKey}}" class="counter-input" name="{{$guestOptionValue['name']}}" data-counter-type="children" max="8"  value="{{$guestOptionValue['count']}}">
                                                        <strong style="font-style: normal;" class="count-val" id="guestCountShow{{$guestOptionKey}}">{{$guestOptionValue['count']}}</strong>
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


            <div class="col-12 col-xl-auto d-none d-xl-block">
                <div class="submit-wrap">
                    <button class="btn p-xl-0 btn-secondary">
                        <i class="icon-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-12 col-xl-auto d-none d-xl-block">
                <div class="btn-call-wrap">
                    <a style="font-style: normal;" class="btn border-0 btn-control" href="tel:+91 98100 74777"><i class="icon-phone-call"></i> +91 98100 74777</a>
                </div>
            </div>
        </div>

        <div class="submit-wrap submit-wrap-mob pe-0 d-xl-none">
            <button type="submit" class="btn py-3 btn-secondary">
                <i class="icon-search"></i> SEARCH
            </button>
        </div>
    </form>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        var form = document.querySelector('form');
        // Reset the form when the page is loaded
        form.reset();

        $(".dropdown-select .dropdown-item").on("click", function () {
            let el = $(this).parents(".dropdown").find(".btn");
            let dataText = $(this).data("value");
            if (el.find(".data-text").length) {
                el.find(".data-text").text(dataText);
            } else {
                el.text(dataText);
            }
            $(this).parents(".dropdown").find(".select-input").val(dataText);
        });

        function resetDateInput() {
            $(".btn-start-date span").text("Arrival Date");
            $(".btn-end-date span").text("Departure Date");
        }

        let destinationBtn = new bootstrap.Dropdown(".destination-btn");
        let calendarBtn = new bootstrap.Dropdown(".calendar-btn");
        let guestsBtn = new bootstrap.Dropdown(".guests-btn");
        let inputCalendar = document.getElementById('input-calendar');

        window.datepicker = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,
            clearButton: true,
            topbarPosition: 'bottom',
            format:'DD-MM-YYYY',

            onSelectRange: function () {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(".btn-start-date span").text(startDate);
                $(".btn-end-date span").text(endDate);
                calendarBtn.toggle();
                // Set hidden input values
                $("#arrival_date").val(startDate);
                $("#departure_date").val(endDate);
            },
            onDayClick: function () {
                if (this.start) {
                    $(".btn-end-date span").text("Departure");
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(".btn-start-date span").text(startDate);
                }
                if (this.end) {
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $(".btn-end-date span").text(endDate);
                }
                if (!this.start && !this.end) {
                    resetDateInput()
                }
            }
        });

        $("#clear-input-calendar").on("click", function () {
            $(".btn-start-date span").text("Arrival");
            $(".btn-end-date span").text("Departure");
            // Reset hidden input values
            $("#arrival_date").val("");
            $("#departure_date").val("");
        });

        $(".toggle-date").on("click", function (e) {
            e.stopPropagation();
            destinationBtn.hide();
            guestsBtn.hide();
            calendarBtn.toggle();
        });

        var form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            var arrivalDateInput = document.getElementById('arrival_date');
            var departureDateInput = document.getElementById('departure_date');
            if (!arrivalDateInput.value || !departureDateInput.value) {
                event.preventDefault(); // Prevent form submission
                alert('Please select both Arrival and Departure dates.');
            }

        });
    });

    function updateGuestCount(type, i) {
        let guestCountInput = document.getElementById('guestCount' + i);
        let guestCountDisplay = document.getElementById('guestCountShow' + i);
        let guestCount = parseInt(guestCountInput.value);
        if(type === 'plus') {
            guestCount = Math.min(guestCount + 1, 8); // Ensure guest count doesn't exceed 8
        }
        else{
            guestCount = Math.max(guestCount - 1, 0); // Ensure guest count doesn't go below 0
        }

        if(i==0){
            if(guestCount !=0){
                guestCountInput.value = guestCount;
                guestCountDisplay.innerHTML = guestCount;
            }
        }
        else{
            guestCountInput.value = guestCount;
            guestCountDisplay.innerHTML = guestCount;
        }
        // Update total guest count
        updateTotalGuests();
    }

    function updateTotalGuests() {
        let totalGuests = 0;
        document.querySelectorAll('.counter-input').forEach(function(input) {
            totalGuests += parseInt(input.value);
        });
        let sufix = '';
        if(totalGuests !=1){
            sufix = 's';
        }
        document.querySelector('.guests-btn span').innerText = totalGuests+' Guest'+sufix;
    }


    function clearGuests() {
        @foreach($guestOptions as $guestOptionKey => $guestOptionValue)
            document.getElementById('guestCount{{$guestOptionKey}}').value = 0;
            document.getElementById('guestCountShow{{$guestOptionKey}}').innerText = 0;
        @endforeach
        document.querySelector('.guests-btn span').innerText = 'Guests: 0';
        document.querySelector('.guests-btn').setAttribute('value', '0');
    }
</script>


