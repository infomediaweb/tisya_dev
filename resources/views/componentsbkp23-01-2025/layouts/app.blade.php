<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        <base href="{{URL::to('/')}}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="{{ $description ?? 'Tisya' }}">
        <meta name="keywords" content="{{ $keywords ?? 'Tisya' }}">
        <title>{{ $title ?? 'Tisya' }}</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <link rel="shortcut icon" type="image/png" href="favicon.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:slnt,wght@-10..0,100..900&family=Source+Serif+4:ital,opsz,wght@0,8..60,200..900;1,8..60,200..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/app.css?v=1.0.1') }}">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <style>
            .border_danger{
                border-color: red !important;
            }
        </style>
    </head>
    <?php
    $pagename = basename($_SERVER['PHP_SELF'], '.php');
    $body_class = ($pagename == 'index') ? 'home-page' : 'inner-page';
    $body_class .= ($pagename == 'property-details') ? ' details' : '';
?>
<body class="{{ $body_class }} {{ request()->routeIs('property-details') ? 'details' : '' }}">

     <div class="wrapper">
            <main class="main">
                @include('components.layouts.header')
                @yield('content')
                @include('components.layouts.footer')
            </main>
        </div>
    </body>
    <script src="{{ asset('assets/js/app.js?v=2') }}"></script>
    <script src="{{ asset('assets/js/hamburger.js?v=4') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


</html>
