<template>
    <div class="wrapper clearfix">
        <header class="header-main compensate-for-scrollbar">
            <div class="container-fluid px-3">
            <div class="row align-items-center">
                <div class="col-auto header-logo">
                    <router-link :to="{name: 'booking-list'}" class="logo">
                        <img src="@assets/images/logo.png" alt="Tisya Stays">
                    </router-link>
                </div>
                <div class="col-auto d-none d-xl-block">
                    <button class="btn px-2 menu-toggle" @click="toggleMenu">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </button>
                </div>
                <div class="col">
                    <div class="header-nav">
                        <div class="row align-items-center justify-content-end">
                            <div class="col-auto">
                                <div div class="btn-group">
                                    <button type="button" class="btn user-btn d-flex p-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="u-img bg-secondary">
                                            A
                                            <!-- <img src="assets/images/user-icon.jpg" alt="Hi Admin"> -->
                                        </span>
                                        <span class="u-name">Hi {{ name }}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li class="d-xl-none"><span class="fw-medium dropdown-item-text">Hi Admin</span></li>
                                        <li class="d-xl-none"><hr class="dropdown-divider"></li>
                                        <li><router-link class="dropdown-item" :to="{name: 'change-password'}">Change Password</router-link></li>
                                        <li><button class="dropdown-item" type="button" @click="logOut">Logout</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto d-xl-none ps-0">
                    <button class="btn px-2 menu-toggle" @click="toggleMenu">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </button>
                </div>
            </div>
            </div>
        </header>

        <main class="main container-fluid">
            <div class="row">
                <aside class="sidebar">
                    <sidebarMenu />
                </aside>


                <div class="content-wrap col-12">
                    <router-view></router-view>
                </div>
            </div>
        </main>

    </div>
</template>
<script setup>
    import sidebarMenu from '@components/sidebar-menu.vue'
    import $ from 'jquery'
    import { useRouter } from "vue-router"
    import axios from 'axios'
    import { inject } from 'vue'
    import { toast } from '@utils/toast'


    const router = useRouter()
    const store = inject('store')
    const name = store.getters.user?.user_name;

    // For mobile version menu toggle
    const toggleMenu = () => {
        $("body").toggleClass("menu-active")
        $('.menu-toggle').toggleClass('is-active')
    }

    // On Router change Menu will be close
    router.beforeEach((to, from) => {
        if(to){
            $("body").removeClass("menu-active")
            $('.menu-toggle').removeClass('is-active')
        }
    })

    const logOut = () => {
        axios.post('/api/pms-logout').then(res =>{
            if(res.data.status){
                store.dispatch('user', '')
                router.push({name:'login'})
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }

</script>
