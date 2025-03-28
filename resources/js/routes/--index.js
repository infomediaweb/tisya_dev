import { createWebHistory, createRouter } from "vue-router"
import store from '../store'

import 'nprogress/nprogress.css'
import NProgress from "nprogress"

const routes = [
    {
        path: '/pms',
        children:[
            {
                path: '',
                name: 'login',
                component: () => import('../pms/login.vue'),
                meta: {
                    isHeader: false
                }
            },
            {
                path: 'dashboard',
                name: 'dashboard',
                component: () => import('../pms/dashboard.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-home/:id?',
                name: 'add-home',
                component: () => import('../pms/home-management/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'manage-home',
                name: 'manage-home',
                component: () => import('../pms/home-management/manage.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-location/:id?',
                name: 'add-location',
                component: () => import('../pms/location/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-location',
                name: 'list-location',
                component: () => import('../pms/location/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-collection/:id?',
                name: 'add-collection',
                component: () => import('../pms/collection/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-collection',
                name: 'list-collection',
                component: () => import('../pms/collection/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-company/:id?',
                name: 'add-company',
                component: () => import('../pms/company/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-company',
                name: 'list-company',
                component: () => import('../pms/company/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-home-type/:id?',
                name: 'add-home-type',
                component: () => import('../pms/hometype/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-home-type',
                name: 'list-home-type',
                component: () => import('../pms/hometype/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-amenities/:id?',
                name: 'add-amenities',
                component: () => import('../pms/amenities/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-amenities',
                name: 'list-amenities',
                component: () => import('../pms/amenities/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'gst',
                name: 'gst',
                component: () => import('../pms/gst.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'gst-setting',
                name: 'gst-setting',
                component: () => import('../pms/gst-setting.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'change-password',
                name: 'change-password',
                component: () => import('../pms/change-password.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'website-markup',
                name: 'website-markup',
                component: () => import('../pms/website-markup.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'new-booking/:id?',
                name: 'by-location',
                component: () => import('../pms/booking/by-location.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'new-booking-by-property',
                name: 'by-property',
                component: () => import('../pms/booking/by-property.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'booking/payment-request/:id?',
                name: 'booking/payment-request',
                component: () => import('../pms/booking/payment-request.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'booking/payment-request-edit/:id?',
                name: 'booking/payment-request-edit',
                component: () => import('../pms/booking/payment-request-edit.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'booking/checkin/:id?',
                name: 'booking/checkin',
                component: () => import('../pms/booking/checkin.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'manage-booking',
                name: 'manage-booking',
                component: () => import('../pms/booking/manage.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'booking-list',
                name: 'booking-list',
                component: () => import('../pms/booking/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'booking-enquiry-list',
                name: 'booking-enquiry-list',
                component: () => import('../pms/booking/enquiry-list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-faq/:id?',
                name: 'add-faq',
                component: () => import('../pms/site-pages/faq/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-faqs',
                name: 'list-faqs',
                component: () => import('../pms/site-pages/faq/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-team/:id?',
                name: 'add-team',
                component: () => import('../pms/site-pages/team/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-teams/:id?',
                name: 'list-teams',
                component: () => import('../pms/site-pages/team/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'about-us',
                name: 'about-us',
                component: () => import('../pms/site-pages/about-us.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'terms-and-condition',
                name: 'terms-and-condition',
                component: () => import('../pms/site-pages/terms-and-condition.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'hero-slide',
                name: 'hero-slide',
                component: () => import('../pms/site-pages/home/hero-slide.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'our-difference',
                name: 'our-difference',
                component: () => import('../pms/site-pages/home/difference.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-special-offer/:id?',
                name: 'add-special-offer',
                component: () => import('../pms/site-pages/home/special-invitations/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-special-offer',
                name: 'list-special-offer',
                component: () => import('../pms/site-pages/home/special-invitations/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-testimonial/:id?',
                name: 'add-testimonial',
                component: () => import('../pms/site-pages/home/testimonial/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-testimonial',
                name: 'list-testimonial',
                component: () => import('../pms/site-pages/home/testimonial/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'second-home',
                name: 'second-home',
                component: () => import('../pms/site-pages/home/second-home.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-blog/:id?',
                name: 'add-blog',
                component: () => import('../pms/site-pages/blog/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-blog',
                name: 'list-blog',
                component: () => import('../pms/site-pages/blog/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'join-our-network-intro',
                name: 'join-our-network-intro',
                component: () => import('../pms/site-pages/join-our-network/intro.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'join-our-network-add-faq/:id?',
                name: 'join-our-network-add-faq',
                component: () => import('../pms/site-pages/join-our-network/faq/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'join-our-network-list-faqs',
                name: 'join-our-network-list-faqs',
                component: () => import('../pms/site-pages/join-our-network/faq/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'achievements',
                name: 'achievements',
                component: () => import('../pms/site-pages/home/achievements.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-coupon/:id?',
                name: 'add-coupon',
                component: () => import('../pms/coupon/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-coupon',
                name: 'list-coupon',
                component: () => import('../pms/coupon/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'guest-database',
                name: 'guest-database',
                component: () => import('../pms/guest-database.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'add-user/:id?',
                name: 'add-user',
                component: () => import('../pms/user/add.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: 'list-user',
                name: 'list-user',
                component: () => import('../pms/user/list.vue'),
                meta: {
                    isHeader: true,
                    requiresAuth: true,
                }
            },
            {
                path: ':pathMatch(.*)*',
                redirect: '/pms/dashboard',
                meta: {
                    isHeader: true
                }
            }
        ]
    },


]

const router = createRouter({
    history: createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 }
    },
    routes,
})


router.beforeEach((to, from) => {
    if(to.matched.some(route => route.meta.requiresAuth)){
        if(store.getters.user){
            return
        }
        else{
            return {
                name : 'login'
            }
        }
    }
})

router.beforeResolve((to, from, next) => {
    if (to.name) {
      NProgress.start()
    }
    next()
})

router.afterEach((to, from) => {
    NProgress.done()
})


export default router
