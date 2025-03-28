
@php

   $pagename = Route::currentRouteName();

@endphp

<footer class="footer-main py-0 section">
    
    <div class="footer-bottom mt-0">
        <div class="container">
            <div class="row">
                <div class="col">&copy; 2025 Tisya Stays | Designed by <a targe="_blank"  href="https://www.iws.in/">IWS</a></div>
                <div class="col-auto"><button class="btn" type="button">Back to Top <i class="icon-arrow-top"></i></button></div>
            </div>
        </div>
    </div>
</footer>




<div class="section-fancybox half-fancybox mxw-600" id="coupon" style="display: none;">
    <div class="row">
        <div class="col-12">
            <div class="fancy-heading">
                <div class="row">
                    <div class="col">
                        <h3>Apply Promo Code</h3>
                    </div>
                    <div class="col-auto">
                        <a href="javascript:void(0)" class="fancy-close" onclick="Fancybox.close()">
                            <i class="icon-close"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <form action="">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="form-group cs-input">
                            <label for="">Enter your promo code</label>
                            <input type="text" class="form-control">
                            <span class="error justify-content-center">Incorrect promo code</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group cs-input bottom-toolbar">
                            <div class="row align-items-center">
                                <div class="col-auto d-md-none">
                                    <a href="javascript:void(0)" class="clear-btn">Clear</a>
                                </div>
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-primary icon-link icon-link-hover w-md-100" disabled>Apply <span class="d-none d-md-block"><i class="bi icon-chevron-right"></i></span></button> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<a href="https://wa.me/8799915100?text=Send a quote" class="whatsapp-link d-none d-lg-flex">
    <svg xmlns="http://www.w3.org/2000/svg" width="78" height="78" viewBox="0 0 78 78">
    <g id="Group_23" data-name="Group 23" transform="translate(-1809 -798)">
        <path id="Path_79" data-name="Path 79" d="M39,0A39,39,0,1,1,0,39,39,39,0,0,1,39,0Z" transform="translate(1809 798)" fill="#26a800"/>
        <path id="whatsapp-svgrepo-com" d="M41.515,10.4A21.653,21.653,0,0,0,26.068,4,21.9,21.9,0,0,0,7.089,36.8L4,48.135,15.586,45.1A21.854,21.854,0,0,0,41.515,10.4ZM26.068,44.08A18.149,18.149,0,0,1,16.8,41.543l-.662-.414L9.269,42.949l1.821-6.7-.441-.69a18.224,18.224,0,1,1,33.791-9.682A18.371,18.371,0,0,1,26.068,44.08Zm9.958-13.627c-.552-.276-3.227-1.6-3.724-1.765s-.883-.276-1.241.276a24.837,24.837,0,0,1-1.738,2.124c-.3.386-.634.414-1.186,0a14.7,14.7,0,0,1-7.42-6.482c-.579-.965.552-.91,1.6-2.979a1.048,1.048,0,0,0,0-.965c0-.276-1.241-2.979-1.683-4.055s-.883-.91-1.241-.938H18.316a1.958,1.958,0,0,0-1.462.69,6.041,6.041,0,0,0-1.821,4.662,10.537,10.537,0,0,0,2.234,5.655,24.522,24.522,0,0,0,9.351,8.275,10.622,10.622,0,0,0,6.565,1.379,5.517,5.517,0,0,0,3.669-2.593,4.468,4.468,0,0,0,.331-2.593A3.852,3.852,0,0,0,36.026,30.454Z" transform="translate(1823 809)" fill="#fff"/>
    </g>
    </svg>
</a>



@if($pagename != 'property-detail' && $pagename != 'payment.thankYou' && $pagename != 'payment-failure')
<div class="fixed-mob-contact d-lg-none">
    <div class="row g-0">
        <div class="col">
            <a href="tel:+91 84529 92240" class="btn btn-primary" onclick="fbq('track', 'Contact'); gtag('event', 'conversion', {'send_to': 'AW-16482594363/oLBgCMjMqpcaELvcwbM9'});" target="_blank">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="style=linear"> <g id="call"> <path id="vector" d="M21.97 18.33C21.97 18.69 21.89 19.06 21.72 19.42C21.55 19.78 21.33 20.12 21.04 20.44C20.55 20.98 20.01 21.37 19.4 21.62C18.8 21.87 18.15 22 17.45 22C16.43 22 15.34 21.76 14.19 21.27C13.04 20.78 11.89 20.12 10.75 19.29C9.6 18.45 8.51 17.52 7.47 16.49C6.44 15.45 5.51 14.36 4.68 13.22C3.86 12.08 3.2 10.94 2.72 9.81C2.24 8.67 2 7.58 2 6.54C2 5.86 2.12 5.21 2.36 4.61C2.6 4 2.98 3.44 3.51 2.94C4.15 2.31 4.85 2 5.59 2C5.87 2 6.15 2.06 6.4 2.18C6.66 2.3 6.89 2.48 7.07 2.74L9.39 6.01C9.57 6.26 9.7 6.49 9.79 6.71C9.88 6.92 9.93 7.13 9.93 7.32C9.93 7.56 9.86 7.8 9.72 8.03C9.59 8.26 9.4 8.5 9.16 8.74L8.4 9.53C8.29 9.64 8.24 9.77 8.24 9.93C8.24 10.01 8.25 10.08 8.27 10.16C8.3 10.24 8.33 10.3 8.35 10.36C8.53 10.69 8.84 11.12 9.28 11.64C9.73 12.16 10.21 12.69 10.73 13.22C11.27 13.75 11.79 14.24 12.32 14.69C12.84 15.13 13.27 15.43 13.61 15.61C13.66 15.63 13.72 15.66 13.79 15.69C13.87 15.72 13.95 15.73 14.04 15.73C14.21 15.73 14.34 15.67 14.45 15.56L15.21 14.81C15.46 14.56 15.7 14.37 15.93 14.25C16.16 14.11 16.39 14.04 16.64 14.04C16.83 14.04 17.03 14.08 17.25 14.17C17.47 14.26 17.7 14.39 17.95 14.56L21.26 16.91C21.52 17.09 21.7 17.3 21.81 17.55C21.91 17.8 21.97 18.05 21.97 18.33Z" stroke="#ffffff" stroke-width="1.5" stroke-miterlimit="10"></path> </g> </g> </g></svg>
                <span>CALL</span>
            </a>
        </div>
        <div class="col">
            <a href="https://wa.me/918452992240?text=Send a quote" class="btn btn-light" onclick="fbq('track', 'Contact'); gtag('event', 'conversion', {'send_to': 'AW-16482594363/oLBgCMjMqpcaELvcwbM9'});" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="78" height="78" viewBox="0 0 78 78">
                <g id="Group_23" data-name="Group 23" transform="translate(-1809 -798)">
                    <path id="Path_79" data-name="Path 79" d="M39,0A39,39,0,1,1,0,39,39,39,0,0,1,39,0Z" transform="translate(1809 798)" fill="#26a800"/>
                    <path id="whatsapp-svgrepo-com" d="M41.515,10.4A21.653,21.653,0,0,0,26.068,4,21.9,21.9,0,0,0,7.089,36.8L4,48.135,15.586,45.1A21.854,21.854,0,0,0,41.515,10.4ZM26.068,44.08A18.149,18.149,0,0,1,16.8,41.543l-.662-.414L9.269,42.949l1.821-6.7-.441-.69a18.224,18.224,0,1,1,33.791-9.682A18.371,18.371,0,0,1,26.068,44.08Zm9.958-13.627c-.552-.276-3.227-1.6-3.724-1.765s-.883-.276-1.241.276a24.837,24.837,0,0,1-1.738,2.124c-.3.386-.634.414-1.186,0a14.7,14.7,0,0,1-7.42-6.482c-.579-.965.552-.91,1.6-2.979a1.048,1.048,0,0,0,0-.965c0-.276-1.241-2.979-1.683-4.055s-.883-.91-1.241-.938H18.316a1.958,1.958,0,0,0-1.462.69,6.041,6.041,0,0,0-1.821,4.662,10.537,10.537,0,0,0,2.234,5.655,24.522,24.522,0,0,0,9.351,8.275,10.622,10.622,0,0,0,6.565,1.379,5.517,5.517,0,0,0,3.669-2.593,4.468,4.468,0,0,0,.331-2.593A3.852,3.852,0,0,0,36.026,30.454Z" transform="translate(1823 809)" fill="#fff"/>
                </g>
                </svg>
                <span>WHATSAPP</span>
            </a>
        </div>
    </div>
</div>
@endif
<script src="{{ asset('landingassets/js/app.js') }}"></script>
<script src="{{ asset('landingassets/js/hamburger.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        $('.goTop').on("click", function(){
          $("body,html").animate({scrollTop: 0},1200);
        })
        
        $('.dropdown-mobile-wrapper .dropdown-item').on('click', event => {  
           $(".menu-toggle").trigger("click");
        })
                
        
        Fancybox.bind("[data-custom-fancy]", {
            hideScrollbar: true,
            closeButton: false,
        })
    });

    
</script>

</body>
</html>