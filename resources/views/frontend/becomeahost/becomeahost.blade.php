
@extends('layout.main')
@section('content')
@if(session('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
    </div>
@endif


<section class="section flex-wrap section-hero section-bg" style="min-height:60vh;">
        <div class="section-bg">
            <img src="{{asset('assets/images/villa.webp')}}" alt="">
        </div>
    <div class="container hero-content-container position-relative">
        <div class="row">
            <div class="col-12">
                <div class="hero-card text-center">
                  <h1>Become a Host</h1>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section become-a-host" style="background-color: #f7f7f7;">
    <div class="container">
        <div class="row gy-5 align-items-center">
            <div class="col-12 col-lg">
                <div class="section-heading">
                    <h1 class="fs-2 mb-3">Unlock the True Potential of Your Vacation Home</h1> 
                    <div class="fs-5">Partner with TisyaStays</div> 
                </div>

                <div class="hosted-info">
                    <div class="row gx-3">
                        <div class="col-auto">
                             <img src="{{asset('assets/images/hosted.png')}}" alt="">
                        </div>    
                        <div class="col">
                            <h3 class="fw-bold text-primary">1,000+</h3>
                            <p>Happy Families Hosted from Mumbai, Pune, Bangalore and NCR</p>
                        </div>
                    </div>
                    <div class="row gx-3">
                        <div class="col-auto">
                      <img src="{{asset('assets/images/vacation-home.png')}}" alt="">
                        </div>    
                        <div class="col">
                            <h3 class="fw-bold text-primary">1 in 10</h3>
                            <p>Homes make it to the Vacation Home Network</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg">
                <div class="form-box p-4 bg-white shadow rounded-4">
                     <h3 class="fw-bold text-primary">let's chat</h3>
                     <p>If you would like to see your Home in trusted hands, write to us!</p>
                   <form action="{{route('host')}}" method="POST"  id="ajaxForm">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" placeholder="Your Name*" name="name" value="{{ old('name') }}">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <input type="email" class="form-control" placeholder="Email*" name="email"  value="{{ old('email') }}">
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <input type="number" class="form-control" placeholder="Phone*" name="phone"  value="{{ old('phone') }}">
                        @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                                                <input type="text" class="form-control" placeholder="location*" name="location"  value="{{ old('location') }}">

                       <!--<select name="location" id="location" class="form-select">-->
                       <!--    <option value="" disabled {{ old('location') ? '' : 'selected' }}>Select location of home</option>-->
                       <!--               @foreach($locations as $location)-->
                       <!--         <option value="{{ $location->location_name }}" -->
                       <!--     @if(old('location') == $location->id) selected @endif>-->
                       <!--   {{ $location->location_name }}-->
                       <!--           </option>-->
                       <!--         @endforeach-->
                       <!--           </select>-->

                        @error('location')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>
                    
                    
                    <div class="form-group mb-3">
                        <select name="home_status" id="home_status" class="form-select">
                            <option value="" disabled {{ old('home_status') == '' ? 'selected' : '' }}>Status of home</option>
                            <option value="Ready to host" {{ old('home_status') == 'Ready to host' ? 'selected' : '' }}>Ready to host</option>
                            <option value="Need upgrades" {{ old('home_status') == 'Need upgrades' ? 'selected' : '' }}>Need upgrades / repairs</option>
                            <option value="Under construction" {{ old('home_status') == 'Under construction' ? 'selected' : '' }}>Under construction</option>
                        </select>
                        @error('home_status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-3">
                        <input type="text" name="website" class="form-control" placeholder="Website / Airbnb Link (if any)"  value="{{ old('website') }}">
                        @error('website')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label>Captcha<span class="text-danger">*</span></label>
                        <div class="form-group form-captcha">
                            <div class="row gx-3 mb-4 align-items-center">
                                <div class="col col-lg">
                                    <input type="text" name="captcha" id="captcha"
                                        class="form-control {{ $errors->has('captcha') ? 'is-invalid' : '' }}"
                                        placeholder="Captcha" value="{{ old('captcha') }}">
                                    @if ($errors->has('captcha'))
                                    <div class="invalid-feedback">{{ $errors->first('captcha') }}</div>
                                    @endif
                                </div>
                                <div class="col-auto">
                                    <div class="row g-0 align-items-center">
                                        <div class="col">
                                            <div class="captcha-box" id="captcha_div">
                                                <span class="captcha-text" id="captcha_text" style="user-select: none;">{{ old('captcha_text') }}</span>
                                                <input type="hidden" name="captcha_hidden" value="{{ old('captcha_hidden') }}"
                                                    id="captcha_hidden" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="refreshBtn btn" id="captcha_reload" >
                                                <svg style="width: 16px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M142.9 142.9c-17.5 17.5-30.1 38-37.8 59.8c-5.9 16.7-24.2 25.4-40.8 19.5s-25.4-24.2-19.5-40.8C55.6 150.7 73.2 122 97.6 97.6c87.2-87.2 228.3-87.5 315.8-1L455 55c6.9-6.9 17.2-8.9 26.2-5.2s14.8 12.5 14.8 22.2l0 128c0 13.3-10.7 24-24 24l-8.4 0c0 0 0 0 0 0L344 224c-9.7 0-18.5-5.8-22.2-14.8s-1.7-19.3 5.2-26.2l41.1-41.1c-62.6-61.5-163.1-61.2-225.3 1zM16 312c0-13.3 10.7-24 24-24l7.6 0 .7 0L168 288c9.7 0 18.5 5.8 22.2 14.8s1.7 19.3-5.2 26.2l-41.1 41.1c62.6 61.5 163.1 61.2 225.3-1c17.5-17.5 30.1-38 37.8-59.8c5.9-16.7 24.2-25.4 40.8-19.5s25.4 24.2 19.5 40.8c-10.8 30.6-28.4 59.3-52.9 83.8c-87.2 87.2-228.3 87.5-315.8 1L57 457c-6.9 6.9-17.2 8.9-26.2 5.2S16 449.7 16 440l0-119.6 0-.7 0-7.6z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="btn-wrap">
                        <button class="btn btn-primary">Submit</button>
                    </div>

                </form>

                </div>
            </div>
        </div>        
    </div>
</section>
<section class="section become-a-host">
    <div class="container">
        <div class="section-heading">
            <h2 class="fs-2">What's In It For You</h2> 
        </div>

        <div class="host-includes">
            <div class="row g-4 g-lg-5">
                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">
                            <h3>Flexible Revenue Models</h3>
                            <p>We help you earn from your prized possession, and turn it into a self-sustaining asset</p>
                        </div>
                   </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">
                            <h3>Pioneering 200-Point Checklist</h3>
                            <p>We audit each home thoroughly before we accept it into the Vacation Home Network</p>
                        </div>
                   </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">                           
                            <h3>Dependable Sales Channels</h3>
                            <p>We exclusively manage reservations across: Website, OTAs, Travel Agents, Event Managers</p>
                        </div>
                   </div>
                </div>


                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">                          
                            <h3>Dedicated Account Managers</h3>
                            <p>A team committed to ensuring that you have a hassle-free hosting experience from on-boarding to sales functions</p>
                        </div>
                   </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">
                            <h3>Specialised Marketing & Branding</h3>
                            <p>Our Homes, Company and Founders are regularly featured across trusted media publications, portals and channels</p>
                        </div>
                   </div>
                </div>


                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">                          
                            <h3>Complete Operations Support</h3>
                            <p>We curate the entire Guest Experience, train staff to industry-standard processes and assist you in procuring products</p>
                        </div>
                   </div>
                </div>


                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">                          
                            <h3>Training of Hospitality Staff</h3>
                            <p>Our Pan-India L&D team conducts regular in-situ and central training sessions for skill improvement</p>
                        </div>
                   </div>
                </div>


                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">                          
                            <h3>Insurance Against Damages</h3>
                            <p>We’re passionate about your vacation home and insure it against significant damages and claims</p>
                        </div>
                   </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                   <div class="hi-card card">                        
                        <div class="card-body">                           
                            <h3>Complete Trust & Transparency</h3>
                            <p>We host discerning families and friends who respect your home as much as you do</p>
                        </div>
                   </div>
                </div>



            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function generateCaptcha() {
        const digits = Math.floor(Math.random() * 900) + 100; 
        let characters = '';
        const alphanumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for (let i = 0; i < 3; i++) {
            characters += alphanumeric.charAt(Math.floor(Math.random() * alphanumeric.length));
        }
        const captcha = digits + characters;
        return captcha;
    }
    function setCaptcha(captcha) {
        document.getElementById('captcha_text').innerText = captcha;
        document.getElementById('captcha_hidden').value = captcha;
    }
    function refreshCaptcha() {
        const newCaptcha = generateCaptcha();
        setCaptcha(newCaptcha);
    }
    document.getElementById('captcha_reload').addEventListener('click', function() {
        refreshCaptcha();
    });
    document.addEventListener('DOMContentLoaded', function() {
        refreshCaptcha();
    });
    </script>
<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('#success-message').fadeOut();
        }, 1000);  
    });
</script>
<script>
    $(document).ready(function() {
        $('#ajaxForm').on('input change', 'input, select, textarea', function() {
            $(this).closest('.form-group').find('.text-danger').remove();
        });
    });
</script>

@endsection