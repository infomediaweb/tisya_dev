@extends('layout.main')
@section('content')
@if(session('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
    </div>
@endif
<section class="section become-a-host">
    <div class="container">
        <div class="row gy-5">
            <div class="col-12 col-lg">
                <div class="section-heading">
                    <h1 class="fs-2 mb-3">Corporate Contact</h1> 
                   
                </div>

                <div class="hosted-info">
                    <div class="row gx-3">
                        <div class="col-auto">
                            <img src={{asset('assets/images/contact.png')}} alt="">
                        </div>    
                        <div class="col">
                            <h4 class="fw-bold text-primary">Contact</h4>
                            <p class="mb-1"><a class="text-decoration-none" href="tel:+91 87999 15100">+91 87999 15100</a>, <a class="text-decoration-none" href="tel:+91 87999 14701">+91 87999 14701</a></p>
                            <p class="mb-1"><a class="text-decoration-none" href="mailto:reservations@tisyastays.com">reservations@tisyastays.com</a></p>
                            <p class="mb-1"><span>Time : </span>9:30 to 6:30</p>
                        </div>
                    </div>
                    <div class="row gx-3">
                        <div class="col-auto">
                            <img src="{{ asset('assets/images/svgviewer-png-output (1).png') }}" alt="">
                        </div>    
                        <div class="col">
                            <h4 class="fw-bold text-primary">Address</h4>
                            <address>
                                TISYA STAYS PRIVATE LIMITED<br>House No Plot No B Chalta No 9 P.T.S.149, <br>Next to Hotel Blue Bay, Miramar, Panaji, North Goa, <br>Goa, India, 403001
                            </address>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg">
                <div class="form-box p-4 bg-white shadow rounded-4">
                     <h3 class="fw-bold text-primary mb-3">Contact Us</h3>
                    
                     <form action="{{ route('contactus.submit') }}" method="POST" id="ajaxForm">
                        @csrf
                    
                       
                        <div class="form-group mb-3">
                            <input type="text" class="form-control" placeholder="Full Name*" name="name" value="{{ old('name') }}">
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    
                
                        <div class="form-group mb-3">
                            <input type="email" class="form-control" placeholder="Email Id*" name="email" value="{{ old('email') }}">
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    
                   
                        <div class="form-group mb-3">
                            <input type="number" class="form-control" placeholder="Mobile no.*" name="mobile" value="{{ old('mobile') }}">
                            @error('mobile')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <textarea name="message" rows="5" class="form-control h-auto" placeholder="Message*">{{ old('message') }}</textarea>
                            @error('message')
                            <span class="text-danger">{{ $message }}</span>
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