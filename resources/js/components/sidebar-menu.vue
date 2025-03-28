<template>
    <div class="main-nav">
        <ul class="m-0 list-unstyled">
            <li class="active">
                <router-link :to="{name: 'booking-list'}"><i class="icon-dashboard"></i> <span>Dashboard</span></router-link>
            </li>

            <li class="has-dropdown" v-if="role == 'Admin'">
                <a href="javascript:void(0)"><i class="icon-user"></i> <span>User</span></a>
                <div class="submenu">
                    <ul>
                        <li><router-link :to="{name: 'add-user'}">Add</router-link></li>
                        <li><router-link :to="{name: 'list-user'}">Manage</router-link></li>
                    </ul>
                </div>
            </li>

            <!-- <li class="has-dropdown">
                <a href="javascript:void(0)"><i class="icon-owner fs-5"></i> <span>Owner</span></a>
                <div class="submenu">
                    <ul>
                        <li><router-link :to="{name: 'add-owner'}">Add</router-link></li>
                        <li><router-link :to="{name: 'list-owner'}">Manage</router-link></li>
                    </ul>
                </div>
            </li> -->

            <li class="has-dropdown" v-if="role == 'Admin'">
                <a href="javascript:void(0)"><i class="icon-property"></i> <span>Home Management</span></a>
                <div class="submenu">
                    <ul>
                        <li><router-link :to="{name: 'add-home'}">Add Home</router-link></li>
                        <li><router-link :to="{name: 'manage-home'}">Manage Homes</router-link></li>
                    </ul>
                </div>
            </li>
            <li class="has-dropdown">
                <a href="javascript:void(0)"><i class="icon-booking"></i> <span>Bookings</span></a>
                <div class="submenu">
                    <ul>
                        <li v-if="role == 'Admin' || role == 'Finance' || role == 'Reservations'"><router-link :to="{name: 'by-location'}">New Booking</router-link></li>
                        <li><router-link :to="{name: 'booking-list'}">All Bookings</router-link></li>
                        <li v-if="role == 'Admin'"><router-link :to="{name: 'booking-enquiry-list'}">Booking Enquiry</router-link></li>
                    </ul>
                </div>
            </li>

            <li v-if="role == 'Admin'">
                <router-link :to="{name: 'guest-database'}"><i class="icon-database"></i> <span>Guest Database</span></router-link>
            </li>
            <li v-if="role == 'Admin'">
                <router-link :to="{name: 'list-coupon'}"><i class="bi bi-tag fs-5"></i> <span>Coupons</span></router-link>
            </li>

            <!-- <li>
                <a href="javascript:void(0)"><i class="icon-tools"></i> <span>Tools</span></a>
                <div class="submenu">
                    <ul>
                        <li><a href="coupons.php">Coupons</a></li>
                        <li><a href="website-markup.php">Website Markup</a></li>
                        <li><a href="quoting-tool.php">Quoting Tool</a></li>
                    </ul>
                </div>
            </li> -->
            <!-- <li>
                <a href="javascript:void(0)"><i class="icon-report"></i> <span>Reports & DB</span></a>
                <div class="submenu">
                    <ul>
                        <li><a href="invoices.php">Invoices</a></li>
                        <li><a href="guest-database.php">Guest Database</a></li>
                    </ul>
                </div>
            </li> -->
            <li v-if="role == 'Admin'">
                <router-link :to="{name: 'analytics'}"><i class="bi bi-bar-chart-line fs-5"></i> <span>Analytics</span></router-link>
            </li>
            <li class="has-dropdown" v-if="role == 'Admin'">
                <a href="javascript:void(0)"><i class="icon-report"></i> <span>Reports</span></a>
                <div class="submenu">
                    <ul>
                        <li><router-link :to="{name: 'sale-report'}">Sale</router-link></li>
                    </ul>
                    <ul>
                        <li><router-link :to="{name: 'police-verification'}">Police Verification</router-link></li>
                    </ul>
                </div>
            </li>
            <li class="has-dropdown" v-if="role == 'Admin'">
                <a href="javascript:void(0)"><i class="icon-settings"></i> <span>Masters</span></a>
                <div class="submenu">
                    <ul>
                        
                        <li><router-link :to="{name: 'list-company'}">Company</router-link></li>
                        <li><router-link :to="{name: 'list-location'}">Locations</router-link></li>
                        <li><router-link :to="{name: 'list-home-type'}">Home Type</router-link></li>
                        <li><router-link :to="{name: 'list-amenities'}">Amenities</router-link></li>
                        <li><router-link :to="{name: 'list-tags'}">Tags</router-link></li>
                        <li><router-link :to="{name: 'gst'}">GST</router-link></li>
                        <!-- <li><router-link :to="{name: 'email'}">Email</router-link></li> -->
                        <!-- <li><router-link :to="{name: 'checkin-checkout-time'}">Checkin Checkout Time</router-link></li> -->
                        <!-- <li><router-link :to="{name: 'gst-setting'}">GST Setting</router-link></li> -->
                        <!-- <li><router-link :to="{name: 'website-markup'}">Website Markup</router-link></li> -->
                    </ul>
                </div>
            </li>
            <li class="has-dropdown" v-if="role == 'Admin'">
                <a href="javascript:void(0)"><i class="icon-cms"></i> <span>CMS</span></a>
                <div class="submenu">
                    <ul>
                        <!-- <li class="has-multi-dropdown">
                            <a href="javascript:void(0)">Home</a>
                            <div class="submenu multi-level-menu">
                                <ul>
                                    <li>
                                        <router-link :to="{name: 'hero-slide'}">Hero Slide</router-link>
                                    </li>
                                    <li>
                                        <router-link :to="{name: 'our-difference'}">Our Difference</router-link>
                                    </li>
                                    
                                    <li>
                                        <router-link :to="{name: 'list-testimonial'}">Testimonial</router-link>
                                    </li>
                                    <li>
                                        <router-link :to="{name: 'second-home'}">Second Home</router-link>
                                    </li>
                                    <li>
                                        <router-link :to="{name: 'achievements'}">Achievements</router-link>
                                    </li>
                                </ul>
                            </div>
                        </li> -->
                        <!-- <li class="has-multi-dropdown">
                            <a href="javascript:void(0)">Join Our Network</a>
                            <div class="submenu multi-level-menu">
                                <ul>
                                    <li>
                                        <router-link :to="{name: 'join-our-network-intro'}">Intro</router-link>
                                    </li>
                                    <li>
                                        <router-link :to="{name: 'join-our-network-list-faqs'}">FAQs</router-link>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li><router-link :to="{name: 'list-teams'}">Team</router-link></li>
                        <li><router-link :to="{name: 'list-faqs'}">FAQs</router-link></li> -->
                        <li><router-link :to="{name: 'list-collection'}">Collections</router-link></li>
                        <li><router-link :to="{name: 'privacy-policy'}">Privacy Policy</router-link></li>
                        <li><router-link :to="{name: 'cancellation-and-refund-policy'}">Cancellation & Refund Policy</router-link></li>
                        <li><router-link :to="{name: 'footer-banner-content'}">Footer Banner Content</router-link></li>
                        <li><router-link :to="{name: 'hero-slide'}">Banner Slide</router-link></li>
                        <li><router-link :to="{name: 'about-us'}">About Us</router-link></li>
                        <li><router-link :to="{name: 'list-special-offer'}">Special Offers</router-link></li>
                        <li><router-link :to="{name: 'addventure-content'}">Adventure</router-link></li>
                        <li><router-link :to="{name: 'list-blog'}">Blogs</router-link></li>
                        <li><router-link :to="{name: 'list-faqs'}">Frequently Asked Questions</router-link></li>
                        <li><router-link :to="{name: 'terms-and-condition'}">Terms and Conditions</router-link></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
    import $ from 'jquery';
    import { onMounted, inject } from 'vue';
    import { useRoute, useRouter } from 'vue-router';

    const store = inject('store');
    const router = useRouter();
    const route = useRoute();

    const role = store.getters.user?.role;

    router.beforeEach((to, from) => {
        $('.main-nav>ul li a').each(function() {
            if(to.href == $(this).attr('href')){
                $(this).parents('.submenu').parent().siblings().find('.submenu li.active').removeClass('active')
            }
        })
    })

    onMounted(() => {
        // Add arrow in submenu parent li
        $('.submenu').parent().append('<span class="expend"></span>')

        // Toggle Submenu
        $(document).off('click', '.main-nav ul>li>a').on('click', '.main-nav ul>li>a', function() {
            $(this).parent().toggleClass('active').siblings().removeClass('active').find(".submenu").stop().slideUp(300)
            $(this).parent().find(".submenu").stop().slideToggle(300)

            let multiMenu = $(this).parent().find('.multi-level-menu')
            let multiMenuLi = $(this).parent().find('.multi-level-menu li')
            let multiMenuParent = $(this).parent().find('.multi-level-menu li.active').parents('li:not(.has-dropdown)')

            if($(this).parent().find('.submenu:not(.multi-level-menu)').parent().hasClass('active')){
                multiMenu.hide()

                if(multiMenuLi.hasClass('active') && multiMenuLi.find('a').hasClass('router-link-active')){
                    multiMenuParent.addClass('active').siblings().removeClass('active')
                    multiMenuParent.find('.multi-level-menu').slideDown()
                }
                else if(!multiMenuLi.hasClass('active') && !multiMenuLi.find('a').hasClass('router-link-active')){
                    multiMenu.parent().removeClass('active')
                }
            }
            else{
                if(!multiMenu.parents('.submenu').parent().hasClass('active') && !multiMenu.parent().hasClass('active')){
                    multiMenu.hide()
                }
                if(!$(this).parents('.has-multi-dropdown').hasClass('active')){
                    $(this).parent().siblings().find('.multi-level-menu li.active').removeClass('active')
                }
            }
        })

        // Active menu li
        $('.main-nav>ul li a').each(function() {
            if($(this).attr('href') == route.path){
                let mainLi = $(this).parents('.main-nav>ul>li')
                mainLi.addClass('active').siblings().removeClass('active')

                $(this).parents('.has-multi-dropdown').addClass('active')
                $(this).parent().addClass('active')

                if(mainLi.hasClass('active')){
                    mainLi.find('.submenu').show()
                    mainLi.find('.multi-level-menu').parent().not('.active').find('.submenu').hide()
                }
            }
        })
    })

</script>
