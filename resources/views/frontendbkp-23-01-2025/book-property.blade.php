
@extends('components.layouts.app')

@section('content')
<section class="section section-top section-booking">
    <div class="container">
        <form action="{{ route('customer.property.book.form.post') }}" method="post">
            @csrf
            <div class="row g-5">
                <div class="col-12 col-lg-7">
                     <div class="form-wrapper pt-lg-4">
                        <h2>Guest Information</h2>
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>First Name <sup>*</sup></label>
                                    <input type="text" class="form-control @error('first_name') border-danger  @enderror" name="first_name" id="first_name"  value="{{ old('first_name') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Last Name <sup>*</sup></label>
                                    <input type="text" class="form-control @error('last_name') border-danger  @enderror" name="last_name" id="last_name" value="{{ old('last_name') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Phone Number <sup>*</sup></label>
                                    <input type="text" class="form-control @error('phone_number') border-danger  @enderror" name="phone_number" id="phone_number" value="{{ old('phone_number') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Email Address <sup>*</sup></label>
                                    <input type="text" class="form-control @error('email') border-danger  @enderror" name="email" id="email" value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>State <sup>*</sup></label>
                                    <input type="text" class="form-control @error('state') border-danger  @enderror" name="state" id="state" value="{{ old('state') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>City <sup>*</sup></label>
                                    <input type="text" class="form-control @error('city') border-danger  @enderror" name="city" id="city" value="{{ old('city') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="addresss" id="addresss" value="{{ old('addresss') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="checked" id="company_info" name="company_info" @if(old('company_info')) checked="checked"  @endif>
                                        <label class="form-check-label" for="company_info">
                                            Check this if you wish to enter company info
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>

                     <div class="form-wrapper pt-5 mt-5 border-top border-top-1 border-secondary-light">
                        <h2>Company Information</h2>
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>Company Name <sup>*</sup></label>
                                    <input type="text" class="form-control @error('company_name') border-danger  @enderror" name="company_name" id="company_name" value="{{ old('company_name') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>GST No. <sup>*</sup></label>
                                    <input type="text" class="form-control @error('gst_no') border-danger  @enderror" name="gst_no" id="gst_no" value="{{ old('gst_no') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>State <sup>*</sup></label>
                                    <input type="text" class="form-control @error('company_state') border-danger  @enderror" name="company_state" id="company_state" value="{{ old('company_state') }}">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>City <sup>*</sup></label>
                                    <input type="text" class="form-control @error('company_city') border-danger  @enderror" name="company_city" id="company_city" value="{{ old('company_city') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="company_address" id="company_address" value="{{ old('company_address') }}">
                                </div>
                            </div>
                        </div>
                     </div>

                     <div class="form-wrapper pt-5">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input @error('consent') border-danger  @enderror" type="checkbox" value="" id="consent" name="consent" id="consent" >
                                        <label class="form-check-label" for="consent">
                                            I certify that the information provided above is correct and this can be considered as my signature.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input @error('check_terms') border-danger  @enderror" type="checkbox" value="" id="check_terms"  name="check_terms" id="check_terms">
                                        <label class="form-check-label" for="check_terms">
                                            I accept all the <a href="">terms and conditions</a>.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="booking-information py-4 rounded-5">
                        <h3>Booking Information</h3>
                        <div class="swiper card-slider rounded-5">
                            <div class="swiper-wrapper">
                                @foreach($properties->imagesVideos->where('type', 'image') as $imagesKey=>$images)
                                    <div class="swiper-slide">
                                        <span><img class="w-100" src="{{ asset($images->filename) }}"  loading="lazy" alt=""></span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
                            <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
                            <div class="swiper-pagination"></div>
                        </div>

                        <div class="booking-info-text">
                            <div class="property-name mb-3">
                                <h3 class="h1 fw-bold mb-2">{{ $properties->home_name }}</h3>
                                <p>{{ $properties->home_type }}, {{ $properties->state }}</p>
                            </div>
                            <ul class="list-unstyled">
                                <li>Checkin - {{ $bookingInfo['check_in_date'] }}</li>
                                <li>Checkout - {{ $bookingInfo['check_out_date'] }}</li>
                                <li>No. of Nights - {{ $bookingInfo['total_night'] }} @if($bookingInfo['total_night'] >1)nights @else night @endif</li>
                                <li>No. of Guests - {{ $bookingInfo['age_18_plus'] }} @if($bookingInfo['age_18_plus'] >1){{'adults'}}@if($bookingInfo['age_6_17'] >0),@endif @else{{'adult'}}@if($bookingInfo['age_6_17'] >0),@endif @endif @if($bookingInfo['age_6_17'] >0){{ $bookingInfo['age_6_17'] }} @if($bookingInfo['age_6_17'] >1)children @else child @endif @endif</li>
                            </ul>
                            <input type="hidden" name="check_in_date" id="checkin_date" value="{{ $bookingInfo['check_in_date'] }}">
                            <input type="hidden" name="check_out_date" id="check_out_date" value="{{ $bookingInfo['check_out_date'] }}">
                            <input type="hidden" name="total_night" id="total_night" value="{{ $bookingInfo['total_night'] }}">
                            <input type="hidden" name="age_18_plus" id="age_18_plus" value="{{ $bookingInfo['age_18_plus'] }}">
                            <input type="hidden" name="age_6_17" id="age_6_17" value="{{ $bookingInfo['age_6_17'] }}">
                            <input type="hidden" name="total_price" id="total_price" value="{{ $bookingInfo['total_price'] }}">
                            <input type="hidden" name="property_id" id="property_id" value="{{ $properties->id }}">
                        </div>

                        <div class="booking-amount">
                            <table class="table table-borderless">
                                <tr>
                                    <td>INR {{ number_format($properties->cprice->price) }}  x {{ $bookingInfo['total_night'] }} nights</td>
                                    <td class="fw-bold" align="right">INR {{ $bookingInfo['total_price']  }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Coupon code">
                                            <button class="btn btn-secondary" type="button">Apply</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tax (12%)</td>
                                    <td align="right">INR 8,400</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td align="right">INR {{ $bookingInfo['total_price']  }}</td>
                                </tr>
                            </table>
                        </div>
                        <button class="btn btn-primary rounded-pill fw-bold w-100" type="submit">PAY NOW</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", function(){

    })
</script>
@endsection
