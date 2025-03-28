<header class="header-main px-xl-3 home-page {{ request()->routeIs('index','about_us','team','faqs','join-our-network','contact_us') ? 'header-fixed-page' : 'header-page' }}">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <a class="logo" href="{{route('index')}}">
                    <img src="{{asset('assets/images/logo.png')}}" alt="V are Family">
                </a>
            </div>
            <div class="col">
                <a id="b1" href="javascript:void(0)" class="hm float-end d-xl-none">
                    <svg id="i1" class="hamburger" viewBox="0 0 100 100">
                        <path class="top-line-1" d="M30,37 L70,37"></path>
                        <path class="middle-line-1" d="M30,50 L70,50"></path>
                        <path class="bottom-line-1" d="M30,63 L70,63"></path>
                    </svg>
                </a>
                <div class="main-nav">
                    <ul class="nav flex-xl-nowrap">
                        <li>
                            <a href="{{ route('index') }}" style="cursor:pointer !important">DESTINATION</a>
                            <div class="submenu">
                                <ul>
                                   
                                    
                                    @foreach(locations() as $location)
                                   
                                    <li><a href="{{route('properties',['slug' => $location->slug_name])}}">{{ $location->location_name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="#" style="cursor:pointer !important">SPECIAL INVITATIONS</a>
                        </li>
                        <li>
                            <a href="{{ route('join-our-network')}}" style="cursor:pointer !important">JOIN OUR NETWORK</a>
                        </li>
                        <li>
                            <a href="{{ route('blogs') }}" style="cursor:pointer !important">BLOGS</a>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="col d-none d-xl-block">
                <div class="dropdown header-call float-end">
                    <button class="btn dropdown-toggle p-0" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,45"><i class="icon-phone-call"></i> <span>CALL NOW</span></button>
                    <ul class="dropdown-menu call-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="tel:+91 98100 74777">
                                Central Reservation
                                <strong>+91 98100 74777</strong>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="tel:+91 98102 34577">
                                Goa
                                <strong>+91 98102 34577</strong>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="tel:+91 98102 80977">
                                Himachal Pradesh
                                <strong>+91 98102 80977</strong>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="tel:+91 98102 80977">
                                Uttarakhand
                                <strong>+91 92057 34577</strong>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
