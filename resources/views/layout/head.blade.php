
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0, viewport-fit=cover">
    <title>Tisya Stays</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="google-site-verification" content="QHfygflq-AtTrHmpbB67cYCVoh5V4506svv9gqlW_HQ" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Tenor+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v=1.1.4">
    
     <meta property="og:title" content="{{ isset($property->home_name) ? $property->home_name : '' }}" />
    <meta property="og:url" content="{{ request()->url() }}" />
    
    <style>
        .header-main{
            z-index: 9999;
        }
    </style>
    @php
    
       $page = Route::currentRouteName();


    @endphp
    <style>
        
        @media (max-width: 1199px) {
            .active~.pStatic {
                position: static !important;
            }
            .detail-page-search .active~.pStatic {
                position: static !important;
                margin-top:4px;
            }
        }
        
        @media (max-width: 992px) {
            .hero-search .active~.pHStatic {
                position: static !important;
                margin-top:4px;
            }
        }
         .search-hide .small-search-outer{display: none !important;}
    </style>



    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YYDCRN1NM4"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-YYDCRN1NM4');
    </script>
    
    
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '879061370256995'); 
        fbq('track', 'PageView'); 
    </script>
    <noscript>
    <img height="1" width="1" 
    src="https://www.facebook.com/tr?id=879061370256995&ev=PageView
    &noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->


<!--   <script>-->
<!--!function(f,b,e,v,n,t,s)-->
<!--{if(f.fbq)return;n=f.fbq=function(){n.callMethod?-->
<!--n.callMethod.apply(n,arguments):n.queue.push(arguments)};-->
<!--if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';-->
<!--n.queue=[];t=b.createElement(e);t.async=!0;-->
<!--t.src=v;s=b.getElementsByTagName(e)[0];-->
<!--s.parentNode.insertBefore(t,s)}(window,document,'script',-->
<!--'https://connect.facebook.net/en_US/fbevents.js');  -->
<!--fbq('init', '879061370256995');-->
<!--fbq('track', 'PageView');-->
<!--@if($page =='property-list' || $page =='location-property' || $page =='properties' || $page =='property-detail')-->
<!--   fbq('track', 'Search');-->
<!--   fbq('track', 'FindLocation');-->
<!--@endif-->

<!--@if($page =='blog' || $page =='contactus' || $page =='privacypolicy' || $page =='termsandconditions' || $page =='cancellationandrefund' || $page =='cancellationandrefund')-->
<!--   fbq('track', 'ViewContent');-->
<!--@endif-->

<!--@if($page =='website.customer.property.book' || $page =='property-detail')-->
<!--   fbq('track', 'Schedule');-->
<!--@endif-->

<!--@if($page =='property-detail')-->
<!--   fbq('track', 'Lead');-->
<!--@endif-->

<!--@if($page =='becomeahost' || $page =='property-detail')-->
<!--   fbq('track', 'SubmitApplication');-->
<!--@endif-->

<!--@if($page =='contactus')-->
<!--   fbq('track', 'Contact');-->
<!--@endif-->
<!--</script>-->
<!--<noscript>-->
<!--<img height="1" width="1" -->
<!--src="https://www.facebook.com/tr?id=879061370256995&ev=PageView-->
<!--&noscript=1"/>-->
<!--</noscript>  -->

<!-- CLARITY -->
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "qd6uxnd6hq");
</script>
