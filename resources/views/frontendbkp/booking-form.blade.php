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
                        <span class="data-text">Destination</span>
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
                            <span>Arrival</span>
                            <i class="icon-calendar-start"></i>
                        </button>
                    </div>
                    <div class="col-6 col-xl">
                        <button class="btn btn-end-date btn-control toggle-date" type="button">
                            <span>Departure</span>
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
            <input id="arrival_date" name="arrival_date"   hidden>
            <input id="departure_date" name="departure_date"   hidden>
            
                <div class="col-12 col-xl">
                    <div class="dropdown dropdown-guests">
                        <button class="btn guests-btn btn-control" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,5" data-bs-auto-close="outside" data-bs-display="static" type="button">
                            <span>Guests  0</span>
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
                                                        <input type="hidden" id="guestCount{{$guestOptionKey}}" class="counter-input" name="{{$guestOptionValue['name']}}" data-counter-type="children" max="8" value="{{$guestOptionValue['count']}}">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Get the form element
        var form = document.querySelector('form');

        // Reset the form when the page is loaded
        form.reset();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('form');

    form.addEventListener('submit', function(event) {
        var arrivalDateInput = document.getElementById('arrival_date');
        var departureDateInput = document.getElementById('departure_date');

        if (!arrivalDateInput.value || !departureDateInput.value) {
            event.preventDefault(); // Prevent form submission
            alert('Please select both Arrival and Departure dates.');
        }

        // setTimeout(() => {
        //      if(arrivalDateInput.value || departureDateInput.value){
        //     form.reset();
        //    }
        // }, 1000);
       
        
    });
});

</script>

<script>
    // function clearGuests() {
    //     // Loop through each guest option and reset the count to 0
    //     @foreach($guestOptions as $guestOptionKey => $guestOptionValue)
    //         document.getElementById('guestCount{{$guestOptionKey}}').value = 0;
    //         document.getElementById('guestCountShow{{$guestOptionKey}}').innerText = 0;
    //         document.getElementById('guestCountShow{{$guestOptionKey}}').innerText = 0;
    //     @endforeach
    // }
    function clearGuests() {
    // Loop through each guest option and reset the count to 0
    @foreach($guestOptions as $guestOptionKey => $guestOptionValue)
        document.getElementById('guestCount{{$guestOptionKey}}').value = 0;
        document.getElementById('guestCountShow{{$guestOptionKey}}').innerText = 0;
    @endforeach

    // Reset the total guests span
    document.querySelector('.guests-btn span').innerText = 'Guests: 0';

    // Reset the value of the guests button
    document.querySelector('.guests-btn').setAttribute('value', '0');
}

   
</script>

<script>
    function updateGuestCount(type, i) {
    let guestCountInput = document.getElementById('guestCount' + i);
    let guestCountDisplay = document.getElementById('guestCountShow' + i);

    let guestCount = parseInt(guestCountInput.value);

    if (type === 'plus') {
        guestCount = Math.min(guestCount + 1, 8); // Ensure guest count doesn't exceed 8
    } else {
        guestCount = Math.max(guestCount - 1, 0); // Ensure guest count doesn't go below 0
    }

    guestCountInput.value = guestCount;
    guestCountDisplay.innerHTML = guestCount;

    // Update total guest count
    updateTotalGuests();
}

function updateTotalGuests() {
    let totalGuests = 0;
    document.querySelectorAll('.counter-input').forEach(function(input) {
        totalGuests += parseInt(input.value);
    });
    document.querySelector('.guests-btn span').innerText = 'Guests: ' + totalGuests;
}

</script>

<script>

 
// function updateGuestCount(type, i){
//     let guestCount = document.getElementById('guestCount'+i).value;
//     if(type =='plus'){
//        guestCount = parseInt(guestCount) + 1;
//     }
//     else{
//         if(guestCount > 0){
//             guestCount = parseInt(guestCount) - 1;
//         }
//     }
//     document.getElementById('guestCount'+i).value = guestCount; 
//     document.getElementById('guestCountShow'+i).innerHTML  = guestCount;
// }     




 
document.addEventListener("DOMContentLoaded", function () {

// Booking form scripts

// destination button
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

});



</script>

<script>
    document.addEventListener('DOMContentLoaded', ()=> {

        (function($) {

    // Define your plugin function
    $.fn.guestCounter = function(options) {
        // Default options
        let settings = $.extend({           
            maxGuests: options.maxGuests || null,
            totalGuests: 0,
            guestsObj: {},
            onInit: function() {},
            onChange: function() {},
            onIncrement: function() {},
            onDecrement: function() {},
            resetCounter: function() {}            
        }, options);

        // Iterate over each element in the jQuery collection
        return this.each(function() {
            let element = $(this);          
            let counter = element.find(".counter");     
            let clearBtn = element.parent().find(".clear-btn"); 
            // Your plugin logic goes here
           
           
            counter.each(function(){
                let counterEl = $(this);
                let incrementBtn = counterEl.find(".c-plus");
                let decrementBtn = counterEl.find(".c-minus");               
                let input = counterEl.find(".counter-input");
                let countText = counterEl.find(".count-val");
                let maxValueCount = parseInt(input.attr("max")) || null;
                let counterType = input.attr("data-counter-type") || null;
                let valueCount = parseInt(input.val()) || 0;
                
                counterEl.find("[data-total-guests]").parents('.counter').addClass("ignore-total");

                countText.text(valueCount);
                if(counterType){
                    settings.guestsObj = {...settings.guestsObj, [counterType]:valueCount};
                }

              
                if(input.attr("data-total-guests") !== "false" || undefined){
                    settings.totalGuests += valueCount;
                }


                // methods
                incrementBtn.on("click", function(){
                    valueCount++;                   

                    if(maxValueCount && valueCount >= maxValueCount){
                        incrementBtn.addClass("disabled");    
                    }

                    if(input.attr("data-total-guests") !== "false" || undefined){
                        settings.totalGuests++;

                        if(settings.totalGuests >= settings.maxGuests){
                            incrementBtn.addClass("disabled");    
                            counter.not('.ignore-total').find(".c-plus").addClass("disabled");
                        }else{
                            incrementBtn.removeClass("disabled");    
                            counter.not('.ignore-total').find(".c-plus").removeClass("disabled");
                        }

                    }
                    if(counterType){
                        settings.guestsObj = {...settings.guestsObj, [counterType]:valueCount};
                    }
                    decrementBtn.removeClass("disabled");
                    input.val(valueCount);
                    countText.text(valueCount);                    
                    settings.onIncrement.call(valueCount);
                    settings.onChange.call(this, element, settings.totalGuests, settings.guestsObj);
                });

                decrementBtn.on("click", function(){
                    valueCount--;  
               
                    if(valueCount <= 0){   
                        valueCount = 0;
                        decrementBtn.addClass("disabled");                         
                    }

                    if(input.attr("data-total-guests") !== "false" || undefined){
                        settings.totalGuests--;

                         if(settings.totalGuests >= settings.maxGuests){
                            incrementBtn.addClass("disabled");    
                            counter.not('.ignore-total').find(".c-plus").addClass("disabled");
                        }else{
                            incrementBtn.removeClass("disabled");    
                            counter.not('.ignore-total').find(".c-plus").removeClass("disabled");
                        }

                    }

                    if(counterType){                        
                        settings.guestsObj = {...settings.guestsObj, [counterType]:valueCount};
                    }
                    incrementBtn.removeClass("disabled");  
                    input.val(valueCount);
                    countText.text(valueCount); 
                    settings.onDecrement.call(element, valueCount);
                    settings.onChange.call(this, element, settings.totalGuests, settings.guestsObj);
                });
               

                clearBtn.on("click", function(){ 
                    console.log("clearBtn clicked");
                    counter.find(".counter-input").val(0);
                    counter.find(".count-val").text(0);
                    settings.guestsObj = {};
                    counter.find(".c-minus").addClass("disabled"); 
                    counter.find(".c-plus").removeClass("disabled");    
                    valueCount = 0;      
                    settings.totalGuests = 0;      
                    settings.onChange.call(this, element, settings.totalGuests, settings.guestsObj);
                }); 
           })
                    
            settings.onInit.call(this, element,  settings.totalGuests, settings.guestsObj);
        });
    };
}(jQuery));


    })
   


</script>


