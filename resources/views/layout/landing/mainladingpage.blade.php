
<!DOCTYPE html>
<html lang="en">
<head>
   @include('layout.landing.headlanding')
</head>
@php

   $pagename = Route::currentRouteName();

@endphp



<body data-scroll-id="top" class="<?php echo ($pagename === "index" || $pagename === "becomeahost") ? 'home-page' : (($pagename === "list-detail") ? 'inner-page' :  (($pagename === "booking-confirmation") ? 'header-hide header-page' : 'inner-page header-page')); ?>">


    <div class="wrapper clearfix">
        <main class="main clearfix">
            <div class="header-wrapper">
                @include('layout.landing.headerlanding')
            </div>

             @yield('content')

            @include('layout.landing.footerlanding')
        