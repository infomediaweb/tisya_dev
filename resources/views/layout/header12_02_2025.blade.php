@php
    $location_data = getLocationData();
    $slugLocation = request()->route('slug_location');
    $states_with_locations = getStatesWithLocations();
@endphp    
                <header class="header-main compensate-for-scrollbar">
                    <div class="container">
                        <div class="row align-items-center align-items-center">
                            <div class="col">
                                <a href="/" class="logo text-primary">
                                <svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 297.79 77.14">                                    
                                    <g id="tisya">
                                        <g id="Layer_1-2" data-name="Layer 1">
                                        <g id="star">
                                            <path id="Path_16" data-name="Path 16" class="cls-1" d="M11.54,6.19v.08c-.11-.03-.25.09-.3.09h-1.86c-2.05,0-3.86,2.66-3.44,4.62l-.17,1.57v-2.5c0-1.6-1.81-3.69-3.43-3.69H.3c-.05,0-.19-.12-.3-.09v-.09h2.25c1.89-.27,3.34-1.79,3.52-3.69V0l.17,1.57c-.4,2.04,1.39,4.62,3.52,4.62h2.08Z"/>
                                            <path id="Path_18" data-name="Path 18" class="cls-1" d="M243.53,46.48l-.87.19c-.69.35-1.1,1.08-1.04,1.84.1-1-.62-1.89-1.62-1.99-.1,0-.19-.01-.29,0,1.03.03,1.89-.77,1.92-1.8,0-.09,0-.19-.01-.28-.13.99.56,1.89,1.54,2.03.12.02.24.02.37.01Z"/>
                                        </g>
                                        <g id="green">
                                            <path id="Union_1" data-name="Union 1" class="cls-2" d="M29.42,73.34c-2.31-3.72-1.17-8.61,2.55-10.92.14-.09.29-.17.44-.25-.13.46-.35.9-.47,1.36-1.19,4.24,1.29,8.65,5.54,9.83.49.14.99.23,1.5.27,1.41.12,2.82-.12,4.12-.68-1.15,2.28-3.36,3.84-5.9,4.15-.27.02-.54.03-.81.03-2.84.09-5.5-1.36-6.96-3.8ZM44.79,75.23c3.93-4.57,3.41-11.47-1.16-15.4s-11.47-3.41-15.4,1.16c-2.94,3.42-3.47,8.28-1.35,12.26l1.12,1.72c-8.21-4.65-11.13-15.05-6.55-23.3,4.2-7.39,13.08-10.72,21.1-7.92,12.71,4.41,15.32,21.22,5,29.68-.86.68-1.77,1.27-2.73,1.79,0,0-.02,0-.03,0ZM50.22,73.62c3.11-3.01,5.21-6.92,6-11.18,2.61-15.26-12.32-27.99-26.99-22.66-12.38,4.49-17.1,19.64-9.8,30.54-.16.04-.2-.09-.3-.17-4.74-3.97-8.21-11.22-8.78-17.35-1.52-14.57,9.06-27.61,23.63-29.13,4.49-.47,9.03.22,13.18,1.99,19.15,8.11,22.18,34.8,5.03,46.76-.63.45-1.29.85-1.98,1.2h0ZM2.99,52.61C-3.39,34.53,5.36,14.61,22.99,7.08c3.32-1.42,6.45-2.66,9.7-.29,3.28,2.4,2.86,5.58,2.94,9.19.1,1.07.13,2.15.09,3.22-.05.32-.23.6-.5.77-.87.24-1.77.37-2.67.38-16.35,2.13-27.88,17.11-25.76,33.47.39,3.02,1.25,5.97,2.53,8.73l1.46,2.74h0c-3.48-3.62-6.13-7.95-7.78-12.7Z"/>
                                            <path id="Union_2" data-name="Union 2" class="cls-2" d="M141.4,67.94l.04-.34,4.19-10.71-10.91-26.33c-.03-.07.09-.19.11-.19h7.42l6.69,17.17.3.06,5.52-17.22h7.29s.14.14.13.21l-13.23,37.36h-7.55ZM264.14,64.8h-1.53l.08-.47,2.87-7.18-9.28-20.58c-.09-.35,1.42-.42,1.51.02l8.51,18.63,7.51-18.78c.15-.22,1.37-.2,1.43,0l-11.1,28.36h0ZM167.7,55.01c-8.61-7.41-5.31-23.97,6.76-25.31,3.92-.58,7.84,1,10.25,4.14l.21-.13v-3.23l.13-.13h6.53l.13.13v19.85c.06.08.13.15.21.21h2.17l-1.82,6.7h-7.21l-.13-.13v-8.02h-.33s-1.92,6.53-1.92,6.53c-.17.38-.96.82-1.34,1.03-1.66.89-3.52,1.35-5.4,1.33-3.01,0-5.93-1.05-8.23-2.99ZM176.17,36.08c-5.16.52-7.7,6.36-5.88,10.91,2.91,7.24,14.03,5.65,14.63-2.42.37-4.95-3-8.55-7.76-8.55-.33,0-.67.01-1,.05h0ZM113.61,55.48c-.2-.11-.69-.35-.7-.56l1.56-5.56c2.88,1.68,6.98,3.75,10.41,2.86,1.59-.42,2.71-1.91,1.63-3.45-1.06-1.51-5.7-2.55-7.53-3.41-9.73-4.55-5.2-15.63,4.25-15.74h0l.99-.02c2.99.11,5.89,1.02,8.4,2.63l-1.53,5.68c-2.24-1.77-5.03-2.7-7.89-2.63-1.75.14-3.28,1.32-2.62,3.23.63,1.83,4.28,2.52,5.94,3.14,2.02.75,4.86,2.1,6.13,3.88,2.1,3.11,1.6,7.29-1.17,9.83-2.34,1.83-5.25,2.77-8.22,2.65-3.38-.01-6.7-.87-9.65-2.51ZM102,57.25l-.13-.13v-19.93l-.13-.13h-2.24l1.82-6.7h7.21l.13.13v19.93l.13.13h2.16l-1.61,6.4-.21.3h-7.13ZM85.04,57.25l-.13-.13v-20.23h-4.87c-.11,0-.17-.33-.11-.44.65-1.82.88-3.97,1.53-5.77.03-.12.1-.23.19-.32h3.27v-7.51l6.79-1.99h0v9.5h6.45c-.32,2.11-1.08,4.14-1.53,6.23l-.21.3h-4.71v20.23l-.13.13h-6.54ZM230.98,46.35c.12-9.79,12.62-14.3,18.62-6.65.17.21.7,1.15.81,1.22.14.08.43.03.43-.09v-4.54h1.44v19h2.08c.08.06.1.07.1.17-.01.33-.3.82-.31,1.18h-3.18l-.13-.13v-4.32c-.06-.08-.14-.15-.23-.2-.19-.03-1.04,1.35-1.26,1.6-1.88,2.27-4.67,3.57-7.62,3.56-5.91.03-10.73-4.75-10.75-10.66,0-.05,0-.11,0-.16ZM233.2,50.33c2.11,4.66,7.6,6.72,12.26,4.61s6.72-7.6,4.61-12.26c-1.59-3.5-5.17-5.65-9.01-5.42h0c-5.11.33-8.99,4.74-8.66,9.85.07,1.11.34,2.2.8,3.21h0ZM279.53,56.16c-.91-.39-1.76-.91-2.53-1.54-.25-.28.13-1.49.34-1.44.1.02,1.35,1.01,1.68,1.2,1.92,1.07,4.11,1.53,6.29,1.34,2-.15,4.51-1.14,4.71-3.43.58-6.77-12.37-3.89-12.56-10.94-.09-3.38,2.98-5.22,6.01-5.45h0c1.74-.1,3.48.2,5.08.89.32.14,1.68.8,1.73,1.08.03.18-.25,1.23-.47,1.23-.09,0-1.27-.8-1.54-.92-2.08-1.04-4.5-1.2-6.7-.45-3.27,1.32-3.67,5.06-.42,6.73,3.5,1.8,10.77,2.16,10.31,7.65-.31,3.65-3.69,4.99-7.08,4.99-1.66,0-3.31-.31-4.85-.93h0ZM202.76,55.74c-.29-.16-1.8-1.1-1.81-1.32.12-.22.19-1.27.46-1.23.11.02,1.56,1.12,1.92,1.31,2.56,1.39,7.12,1.93,9.54.04,1.53-1.31,1.71-3.62.4-5.15-.25-.29-.54-.54-.87-.74-2.99-2.06-10.34-2.15-10.76-6.71-.62-6.77,8.81-7.43,12.78-4.07l-.31,1.23h0c-.16.13-1.02-.54-1.24-.66-2.33-1.24-5.51-1.76-7.89-.37-1.72.84-2.43,2.91-1.59,4.63.3.61.78,1.13,1.37,1.47,3.55,2.16,11.32,2.18,10.8,8.03-.32,3.56-3.59,4.88-6.98,4.88-2.01.02-4-.44-5.8-1.34h0ZM222.33,56.66v-24.94h-20.06c-.26-.32-.07-.87-.13-1.27h20.06l.13-.13v-3.9l.21-.13,1.23,1.23v2.8l.13.13h65.61c-.06.4.14.95-.13,1.27h-65.48l-.13.13v24.8h-1.45ZM104.74,20.04c5.19-.59,5.79,7.01.98,7.51-.17.02-.34.03-.52.03-4.68,0-5.01-7.03-.47-7.54Z"/>
                                            <path id="Path_17" data-name="Path 17" class="cls-3" d="M295.09,28.52c3.32-.3,3.69,4.71.48,4.98s-3.72-4.68-.48-4.98ZM295.18,28.68c-2.99.21-2.72,4.76.23,4.65,3.1-.11,2.8-4.86-.22-4.65h0Z"/>
                                            <path id="Path_19" data-name="Path 19" class="cls-3" d="M295.74,29.77c.18.09.34.23.46.39.48.92-.58,1.18-.58,1.26l.76.89c-.66.15-.88-.56-1.36-.85l-.04.77-.39.08v-2.08c0-.07-.42-.08-.17-.47.39.05.96-.08,1.31,0h0ZM295.02,30.96c1.21.2,1.1-.97,0-.85v.85Z"/>
                                            <path id="Path_20" data-name="Path 20" class="cls-3" d="M56.44,72.42c.67-.05,1.25.45,1.3,1.12.05.65-.42,1.21-1.06,1.29-.67.05-1.25-.45-1.3-1.12-.05-.65.42-1.21,1.06-1.29ZM56.49,72.5c-.62.05-1.08.6-1.03,1.22.05.59.55,1.04,1.14,1.04.62-.08,1.06-.64.98-1.26-.07-.55-.53-.98-1.09-.99h0Z"/>
                                            <path id="Path_21" data-name="Path 21" class="cls-3" d="M56.76,73.03c.09.04.16.11.22.19.23.45-.28.57-.28.61l.37.43c-.32.07-.42-.27-.66-.41l-.02.37-.19.04v-1.01s-.2-.04-.08-.23c.19.03.47-.04.64,0h0ZM56.41,73.61c.59.1.53-.47,0-.41v.41Z"/>
                                        </g>
                                        </g>
                                    </g>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-auto d-none d-lg-block">
                                <div class="small-search-outer text-center">
                                    <a href="javascript:void(0)" class="small-search" data-small-search>
                                        <ul>
                                            <li data-type="location">
                                                @if (session()->has('location_name'))
                                                    <div>{{ session('location_name') }}</div>
                                                @else
                                                  Where
                                                @endif
                                            </li>
                                            <li data-type="dates">
                                            @if (session()->has('checkin_date'))
                                                    {{ date('jS M', strtotime(session('checkin_date'))) }}
                                                    to
                                                    {{ date('jS M', strtotime(session('checkout_date'))) }}
                                            @else
                                                Dates
                                            @endif
                                            
                                            </li>
                                            <li data-type="guests">
                                                @if (session()->has('total_guests'))
                                                {{ session('total_guests') }} {{ session('total_guests') == 1 ? 'Guest' : 'Guests' }}
                                                @else
                                                Guests
                                                @endif
                                            </li>
                                            <li data-type="search">
                                                <div class="btn-outer">
                                                    <div class="small-search-btn"><i class="icon-search"></i></div>
                                                </div>
                                            </li>
                                        </ul>
                                    </a>
                                </div>
                            </div>

                            <div class="col">
                                <nav class="main-nav">
                                    <ul class="nav list-unstyled m-0 align-items-center justify-content-end">
                                          <li data-mobile-hide class="dropdown">
                                            <a href="properties.php" class="dropdown-toggle" data-bs-toggle="dropdown"  data-bs-offset="0,10"  aria-expanded="true">Destinations</a>
                                            <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                                
                                            @foreach($states_with_locations as $state)
                                            <li class="li-heading"><a href="{{ route('state-property-list', ['state' => $state->name]) }}">{{ $state->name }}</a></li>
                                            @foreach($state->locations as $location)
                                                <li><a class="dropdown-item" href="{{ route('location-property', ['slug_location' => $location->location_name]) }}">{{ $location->location_name }}</a></li>
                                            @endforeach
                                            @endforeach
                                                
                                            </ul>
                                        </li>
                                         <li data-mobile-hide class="d-none d-xl-block">
                                            <a href="{{route('becomeahost')}}" class="btn btn-outline-dark">Become a Host <i class="icon-user"></i></a>
                                        </li>
                                        <li data-mobile-hide class="d-none d-xxl-block dropdown">
                                            <button class="btn btn-outline-dark dropdown-toggle" class="btn btn-outline-dark" data-bs-toggle="dropdown" aria-expanded="true">Get in Touch <i class="icon-chevron-down"></i></button>
                                            <ul class="dropdown-menu call-menu dropdown-menu-end" data-popper-placement="bottom-end">
                                                <li>
                                                        <a class="dropdown-item" href="tel: +91 87999 15100"> +91 87999 15100</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="tel: +91 87999 14701">+91 87999 14701</a>
                                                    </li>
                                                <li>
                                                    <a class="dropdown-item" href="mailto:reservations@tisyastays.com">reservations@tisyastays.com</a>
                                                </li>   
                                            </ul>
                                        </li>
                                        <li data-desktop-hide>
                                            <a href="javascript:void(0)" class="search-link"><i class="icon-search"></i></a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="menu-toggle">
                                                <span class="line"></span>
                                                <span class="line"></span>
                                                <span class="line"></span>
                                            </a>
                                            <div class="dropdown-mobile-wrapper">
                                                <ul class="dropdown-menu dropdown-menu-end dropdown-shadow">
                                                    <li class="d-xl-none">                                                      
                                                      <a class="dropdown-item" href="{{route('properties')}}">All Properties</a>
                                                    </li>

                                                    <li class="d-xl-none">                                                      
                                                          <a class="dropdown-item" href="{{route('becomeahost')}}">Become a Host</a>
                                                    </li>
                                                    
                                                    <!-- <li class="li-heading">Support</li> -->
                                                    <li>
                                                      <a class="dropdown-item" href="{{route('aboutus')}}">About Us</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{route('faq')}}">Frequently Asked Questions</a>
                                                    </li>
                                                     <li>
                                                        <a class="dropdown-item" href="{{route('cancellationandrefund')}}">Cancellation and Refund Policy</a>
                                                    </li>
                                                    <li>
                                                         <a class="dropdown-item" href="{{route('contactus')}}">Contact Us</a>
                                                    </li>
                                                    <li class="li-heading">Get in Touch</li>
                                                    <li>
                                                        <a class="dropdown-item" href="tel: +91 87999 15100"> +91 87999 15100</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="tel: +91 87999 14701">+91 87999 14701</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="mailto:reservations@tisyastays.com">reservations@tisyastays.com</a>
                                                    </li>                                                    
                                                </ul>

                                                
                                            </div>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="col-12">
                                <div class="header-search">
                                    @include('frontend.home-calendar.search-header')
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            


