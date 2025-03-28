
<template>
    <div class="page-wrap dashboard-wrap">
        <div class="page-title">
            <div class="row gy-3 align-items-center justify-content-xl-end">
                <div class="col">
                    <div class="row gy-3">
                        <div class="col align-self-end">
                            <h1 class="h2 mb-0">Bookings Created</h1>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="search-filter">
                        <div class="row gy-3 gx-2">
                            <div class="col-auto position-relative">                               
                                <div class="row justify-content-xl-end gy-3 gx-2">
                                    <div class="col-12 col-lg col-xl-auto">
                                        <div class="form-group mb-0">
                                            <select class="form-control" name="property_name"
                                             v-model="selectedProperty"
                                             @change="onPropertyChange"
                                             style="min-width:250px;"
                                            >
                                                <option selected="" disabled="" value="">Property Name</option>
                                                <option :value="property.id" v-for="property in propertiesName">{{ property.property_name }}</option>
                                            </select>
                                        </div>
                                    </div>  
                                    
                                    <div class="col-6 col-lg col-xl-auto">
                                        <div class="form-group mb-0">
                                            <select class="form-control" name="channel"
                                             v-model="selectedLocation"
                                            >
                                                <option selected="" disabled="" value="">Location</option>
                                                <option :value="location.location_id" v-for="location in propertiesLocations">{{ location.location_name }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6 col-lg col-xl-auto">
                                        <div class="form-group mb-0">
                                            <select class="form-control" name="channel"
                                            v-model="selectedChannel"
                                            >
                                                <option selected="" disabled="" value="">Channel</option>
                                                <option :value="channel" v-for="channel in channelList">{{ channel }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-auto">
                                        <div class="btn-group gap-1">
                                            <button class="btn btn-primary btn-icon" @click="onFilterSearch()"><span class="bi bi-search"></span></button>

                                            <button class="btn btn-icon btn-clear btn-warning" @click="onReset()"><span class="bi bi-arrow-clockwise"></span></button>
                                            <!-- <button class="btn btn-export btn-secondary"><i class="bi bi-file-earmark-excel me-2 fs-6"></i>Export</button> -->
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-auto ms-xxl-auto">
                                <select 
                                    class="form-control" 
                                    v-model="daysWiseModel"
                                    @change="onDaysChange">
                                    <option value="" disabled selected>Select days</option>
                                    <option value="7_next">Next 7 days</option>
                                    <option value="7">Last 7 days</option>
                                    <option value="30">Last 30 days</option>
                                    <option value="60">Last 60 days</option>
                                    <option value="90">Last 90 days</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="outer-wrapper">
                <div class="d-flex justify-content-center align-items-center h-100 py-5" v-if="isListLoading">
                    <div class="spinner-border" role="status"></div>
                </div>
                <div class="table-wrap" v-else-if="dataList.length">
                    <div class="table-responsive">
                        <table class="table table-list-2 align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white">Location</th>
                                    <th class="bg-primary text-white">Property Name</th>
                                    <th class="bg-primary text-white">Channel</th>
                                    <th class="bg-primary text-white text-end">Amount</th>
                                </tr>
                            </thead>                            
                            <tbody v-for="data in dataList" :key="data.id">
                                <tr>    
                                    <td nowrap="">
                                        {{data?.location_name}}
                                    </td>                                 
                                    <td nowrap="">
                                        {{data?.property_name}}
                                    </td>  
                                    <td nowrap="">
                                        {{data?.channel}}
                                    </td>                                  
                                    <td class="text-end">
                                        <b class="text-black">₹{{currFormat(data?.total_amount)}}</b>
                                    </td>
                                </tr>
                            </tbody>
                           

                        </table>
                    </div>
                </div>
                <div class="py-5 px-3" v-else>
                    No Data Found...!
                </div>
            </div>
        </section>
    </div>
    
    <div class="card card-chartBox bg-transparent" v-if="!isListLoading && dataList.length">
        <div class="card-body p-2">
            <div class="row g-3 justify-content-end align-items-center">
                <div class="col">
                    <PaginationAnalytics
                    :total="paginationData?.total"
                    :perPage="paginationData?.per_page"
                    :currentPage="paginationData?.current_page"
                    :lastPage="paginationData?.last_page"
                    @update:currentPage="fetchBookingsCreatedList"
                    v-show="paginationData?.last_page > 1"
                    />  
                </div>
                <div class="col-auto"><h4 class="text-primary m-0"><span class="text-dark">Grand Total:</span> <b>₹{{ currFormat(grandTotal) }}</b></h4></div>
            </div>
        </div>
    </div>
</template>




<script setup>
    import axios from 'axios'      
    import { ref, onMounted, nextTick, computed, watch } from 'vue'
    import { useRoute, useRouter } from 'vue-router'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { format, standardFormat, currFormat, shortFormat } from '@utils/common'
    import PaginationAnalytics from '@components/paginationAnalytics.vue'
    const router = useRouter()

    
    const paginationData = ref({
        total:0,
        per_page:50,
        current_page:1,
        last_page:0,
    })

    const dataList = ref([]);
    const page = ref(1);
    const grandTotal = ref(0);
    const isListLoading = ref(true);


    // filter refs
    const daysWiseModel = ref('30')
    const propertiesName = ref([])
    const selectedProperty = ref('');
    const propertiesLocations = ref([])
    const selectedLocation = ref('')
    const channelList = ref([])
    const selectedChannel = ref('')

    const fetchData = computed(() => {
       return  {
            page: page.value || null,
            days: daysWiseModel.value || '30',
            property_id: selectedProperty.value || null,
            location_id: selectedLocation.value || null,
            channel: selectedChannel.value || null,
        }
    })



    const onDaysChange = (e) => {       
        fetchBookingsCreatedList()
    }



     // Fetch location list
     const fetchLocation = () => {      
        axios.get('api/dashboard/location').then(res => {       
            if(res.data.status){       
                //console.log("location",res.data)      
                propertiesLocations.value = res.data.location
            }
        })
        .catch(error => {

        })
    }

     // Fetch propertiesName
     const fetchPropertiesName = () => {       
        axios.get('api/dashboard/property').then(res => {       
            if(res.data.status){               
                propertiesName.value = res.data.property
            }
        })
        .catch(error => {

        })
    }

    // Fetch channel list
    const fetchChannel = () => {       
        axios.get('api/dashboard/channel').then(res => {       
            if(res.data.status){       
                //console.log("channelList",res.data)      
                channelList.value = res.data.channel
            }
        })
        .catch(error => {

        })
    }

   
    const onFilterSearch = ()=>{ 
        fetchBookingsCreatedList();
    }

    const onReset = ()=>{
        page.value = 1,     
        daysWiseModel.value = "30"       
        selectedProperty.value  = ''
        selectedLocation.value  = ''
        selectedChannel.value  = ''    
        fetchBookingsCreatedList();
    }




    // For fetch Net Revenue list
    const fetchBookingsCreatedList = (d) => {
        isListLoading.value = true;
        console.log("page", d)
        page.value = d || 1;
        axios.post('api/bookings-created',fetchData.value).then(res => {       
            if(res.status){
                console.log("average", res.data)
                dataList.value = res.data.data;   
                grandTotal.value =  res.data.grand_total   
                paginationData.value = res.data.pagination;           
                paginationData.value.current_page = page.value || 1;
                console.log("data",   dataList.value)
                isListLoading.value = false;
            }
        })
        .catch(error => {

        })
    }


    onMounted(() => {
        daysWiseModel.value = router.options.history.state.days
        selectedLocation.value = router.options.history.state.location || ''
        selectedProperty.value = router.options.history.state.property_id || ''
        fetchLocation();
        fetchChannel();
        fetchPropertiesName();     
        fetchBookingsCreatedList();      
    })


</script>
