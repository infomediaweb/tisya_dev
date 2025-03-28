
@extends('layout.main')
@section('content')
<style>
    .booking-information .booking-amount .input-group .btn.btn-danger{
        background-color: #dc3545 !important
    }
    .booking-details{
        color: #726659;
    }
    .booking-details > h3{
        color: #000;
        margin-bottom: 2rem;
    }

    .bd-box > *{
       display: block;
    }
    @media(min-width: 992px){
        .booking-details .row > div +  div{
            border-left: 1px dashed #ccc;
        }
    }
</style>
<section class="section section-top section-booking pb-5">
    <div class="container" style="max-width: 900px;">
        <div class="title text-center ">
            <h3 class="fs-4 fw-normal">Thank You!</h3><br>
            <h4>Your booking is confirmed. Your booking id is <strong>{{ $PropertyBooking_data->booking_id }}.</strong></h4>
        </div>

        <div class="booking-details">
            <h3>Booking Information</h3>
            <div class="booking-information p-0 mb-5 rounded-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-4">
                        <a href="{{ route('index') }}" class="swiper m-0 card-slider rounded-3">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <span>
                                        @if($prorpertyAssetsDetail)
                                            <img class="g-img" src="{{ asset($prorpertyAssetsDetail->filename) }}" width="230" height="" alt="Casa Y’na – A Perfect Luxury Retreat" border="0" style="width: 100%; max-width: 230px; background: #dddddd; font-family: Arial, sans-serif; font-size: 14px; line-height: 15px; color: #000000;">
                                        @else
                                            <img class="g-img" src="{{ asset('assets/images/noimage-property.jpg') }}" width="230" height="" alt="Casa Y’na – A Perfect Luxury Retreat" border="0" style="width: 100%; max-width: 230px; background: #dddddd; font-family: Arial, sans-serif; font-size: 14px; line-height: 15px; color: #000000;">
                                        @endif
                                    </span>
                                </div>
                                </div>
                            <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
                            <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
                            <div class="swiper-pagination"></div>
                        </a>
                    </div>
                    <div class="col-12 col-lg border-0">
                        <div class="booking-info-text p-3 p-lg-0">
                            <div class="property-name mb-3">
                                <h3 class="h1 fw-bold mb-2">{{$PropertyBooking_data->property->home_name}}</h3>
                                <p>{{$PropertyBooking_data->property->location}}, {{$PropertyBooking_data->property->state}}</p>
                            </div>
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-3">
                                    <div class="row gy-3 gx-5">
                                        <div class="col-6 col-md-auto">
                                            Arrival<br> <strong>{{$PropertyBooking_data->checkin_date}}</strong>
                                        </div>
                                        <div class="col-6 col-md-auto">
                                            Departure<br><strong>{{$PropertyBooking_data->checkout_date}}</strong>
                                        </div>
                                        <div class="col-6 col-md-auto">
                                         No. of Nights<br><strong>{{ $PropertyBooking_data->no_of_nights }}</strong>
                                        </div>
                                        <div class="col-6 col-md-auto">
                                            No. of Guests<br><strong>{{$PropertyBooking_data->tot_guests}}</strong>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Payment Details</h3>
            <div class="booking-information p-4 mb-5 rounded-4">
                <div class="row gx-5 gy-3 ">
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Payment ID:</small>
                            <strong>{{ $PropertyBooking_data->transcation_id}}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Date:</small>
                            <strong>{{ date('d M Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Payment Method:</small>
                            <strong>Payu</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Total:</small>
                            <strong>INR {{ number_format($PropertyBooking_data->payable_amount) }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Status:</small>
                            <strong>Paid</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($prorpertyAssetsDetail)
<script>
    fbq('track', 'Purchase', {
        currency: 'INR',
        country: "India",
        value: "{{ number_format($PropertyBooking_data->payable_amount) }}",
        property_name: "{{ addslashes($PropertyBooking_data->property->home_name) }}",
      });
</script>

<!-- Event snippet for Booking Completed conversion page -->
<script>
    gtag('event', 'conversion', {
        'send_to': 'AW-16482594363/DuirCOesqpcaELvcwbM9',
        'value': '{{ number_format($PropertyBooking_data->payable_amount) }}',
        'currency': 'INR',
        'country': 'India',
        'property_name': '{{ addslashes($PropertyBooking_data->property->home_name) }}',
    });
</script>

@endif

@endsection
