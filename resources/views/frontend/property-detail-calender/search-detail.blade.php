<div class="main-search-outer detail-page-search ms-0">
    <div class="main-search p-0">
        <div class="row g-3 align-items-center">
            <div class="col-12">
                <div class="bh-price fw-bold">
                    <strong>&#8377;44,600</strong> <small class="fw-normal">per night + taxes</small>
                </div>
            </div>
            <div class="col-12 field-col d-flex position-relative">
                <button class="btn text-start btn-checkin search-field">
                    <small>Check in</small>
                    <div class="search-field-value js-checkin-text">Add dates</div>
                </button>
                <button class="btn text-start btn-checkout search-field">
                    <small>Check out</small>
                    <div class="search-field-value js-checkout-text">Add dates</div>
                </button>
                <div class="custom-dropdown calendar-dropdown">
                     <input id="detail-page-calendar" type="text" style="display:none;" />
                </div>
            </div>
            <div class="col-12 field-col position-relative">
                <button class="btn text-start search-field search-field-guest">
                    <small>Guests</small>
                    <div class="search-field-value">Add guests</div>
                </button>   
                <div class="custom-dropdown guests-counter guests-dropdown pStatic">
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
                                        <a href="javascript:void(0)" class="btn counter-col c-minus disabled">
                                            <span class="icon-minus"></span>
                                        </a>
                                        <div class="counter-col">
                                            <input type="hidden" class="adults-count" name="" value="">
                                            <strong class="count-val">0</strong>
                                        </div>
                                        <a href="javascript:void(0)" class="btn counter-col c-plus">
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
                                        <a href="javascript:void(0)" class="btn counter-col disabled c-minus">
                                            <span class="icon-minus"></span>
                                        </a>
                                        <div class="counter-col">
                                            <input type="hidden" class="children-count" name="" value="">
                                            <strong class="count-val">0</strong>
                                        </div>
                                        <a href="javascript:void(0)" class="btn counter-col c-plus">
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
                        <tr>
                            <td>&#8377;11,150 x 4 nights</td>
                            <td align="right">&#8377;44,600</td>
                        </tr>
                        <tr>
                            <td>Taxes</td>
                            <td align="right">&#8377; 341</td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Total incl. taxes</td>
                            <td align="right">&#8377;44,941</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="col-12">
                <button class="btn w-100 border bg-transparent text-primary py-2 small border-primary btn-outline-primary">Check offers and discounts</button>
            </div>
            <div class="col-12">
                <button type="submit" class="btn py-3 fw-bold w-100 btn-primary">Book Now</button>
            </div>

            <div class="col-12">
                <a href="#" class="note-link">Booking & Cancellation Policy</a>
            </div>


        </div>
    </div>
</div>

<script>
     document.addEventListener("DOMContentLoaded", function () {
        let parentElClass = '.detail-page-search';    
        let inputCalendar = document.getElementById('detail-page-calendar');
        function resetDateInput(){
          $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
          datepickerHero.clear();
        }        
       
        window.datepickerHero = new HotelDatepicker(inputCalendar, {
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
                $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
                $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);   
                //calendarBtn.toggle();             
            } ,
            onDayClick: function() {    
                this.minNights = 3
                let currentDate = new Date(fecha.format(this.start, `YYYY-MM-DD`))

                let previousDates = [];

                // Loop to get the three previous dates
                // for (let i = 1; i <= 3; i++) {
                //     // Create a new date object to avoid modifying the original currentDate
                //     let previousDate = new Date(currentDate);
                //     previousDate.setDate(currentDate.getDate() + i);
                    
                //     // Format the date and add it to the array
                //     previousDates.push(fecha.format(previousDate, `YYYY-MM-DD`));
                // }

                // window.datepickerHero.setOptions({ disabledDates: previousDates });

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

        // datepickerHero.addEventListener(
        //     "afterClear",
        //     function () {
        //         resetDateInput();
        //     },
        //     false
        // );

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









       

    });
</script>