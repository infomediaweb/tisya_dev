<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? `Manage Home - ${homeName}` : 'Add Home' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'manage-home'}" class="btn rounded-pill btn-secondary-light">
                        <i class="icon-list me-2"></i>
                        Manage
                    </router-link>
                </div>
            </div>
        </div>

        <ul class="list-tab list-group mb-4">
            <li 
                class="list-group-item" 
                v-for="(_, tab, index) in tabs">
                <button 
                    :class="{'active': currentTab === tab}" 
                    @click="onTabChange(tab)"
                    :disabled="route.params.id ? false : index != 0">
                    {{ tab }}
                </button>
            </li>
        </ul>
        <!-- :disabled="route.params.id ? false : index != 0" -->
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <component  
                    v-if="renderComponent"
                    :is="tabs[currentTab]"
                    :data="homeEdit"
                    @emitCurrentTab="onTabChange"
                />
            </div> 
        </section>
    </div>
</template>
<script setup>
    import Overview from './components/overview.vue'
    import Feature from './components/feature.vue'
    import Amenities from './components/amenities.vue'
    import Tags from './components/tags.vue'
    import AdditionalCharges from './components/additional-charges.vue'
    import Images from './components/images.vue'
    import Video from './components/video.vue'
    import Reviews from './components/reviews.vue'
    import Owner from './components/owner.vue'

    import axios from 'axios'
    import { useRoute, useRouter } from 'vue-router'
    import { ref, onMounted, onUnmounted, inject, watch, nextTick } from 'vue'


    const store = inject('store')

    const router = useRouter()
    const route = useRoute()

    const renderComponent = ref(true)

    const homeEdit = ref({})
    const homeName = ref('')
    const isLoading = ref(false)
    
    const currentTab = ref()

    const tabs = { 
        Overview: Overview, 
        Features: Feature,
        Amenities: Amenities,
        'Tags': Tags,
        'Additional Charges': AdditionalCharges,
        Gallery: Images,
        Videos: Video, 
        Reviews: Reviews,
        'Owner Details': Owner
    }

    const onTabChange = (value) => {
        currentTab.value = value ? value : 'Overview'
        store.dispatch('homeCurrentTab', value)

        isLoading.value = true

        let currentTabType

        if(currentTab.value == 'Gallery'){
            currentTabType = 'image'
        }
        else if(currentTab.value == 'Videos'){
            currentTabType = 'video'
        }
        else if(currentTab.value == 'Tags'){
            currentTabType = 'Tags'
        }
        else{
            currentTabType = currentTab.value.toLowerCase()
        }

        setTimeout(() => {
            if(route.params.id){
                axios.get(`/api/home/${route.params.id}`, {
                    params: {
                        type: currentTabType
                    }
                }).then(res => {
                    if(res.data.status){
                        homeEdit.value = res.data.data
                        homeName.value = homeEdit.value.home_name
                        homeEdit.value.cancellation_policy = homeEdit.value.cancellation_policy || '';
                        homeEdit.value.location_info = homeEdit.value.location_info || '';
                        isLoading.value = false 
                    }
                }).catch(error => {
                    isLoading.value = false 
                    if(!error.response.data.status){
                        router.push({name: 'add-home'})
                    }
                })
            }
            else{
                homeEdit.value = []

                renderComponent.value = false
                nextTick()
                renderComponent.value = true
                
                isLoading.value = false   
            } 
        }) 
    }

    watch(route, (newVal, oldVal) => {
        if(!newVal.params.id){
            onTabChange()
        }
    })
    
    onMounted(() => {
        onTabChange(store.getters?.homeCurrentTab)
    })

    onUnmounted(() => {
        store.dispatch('homeCurrentTab', '')
    })
    
</script>