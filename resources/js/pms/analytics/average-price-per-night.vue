
<template>
    <div class="page-wrap dashboard-wrap">
        <div class="page-title">
            <div class="row gy-3 align-items-center justify-content-xl-end">
                <div class="col">
                    <div class="row gy-3">
                        <div class="col align-self-end">
                            <h1 class="h2 mb-0">Average Price Per Night</h1>
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
                                    <th class="bg-primary text-white">Property Name</th>
                                    <th class="bg-primary text-white text-end">Average Price</th>
                                </tr>
                            </thead>                            
                            <tbody v-for="data in dataList" :key="data.id">
                                <tr>                                   
                                    <td nowrap="">
                                        {{data?.property_name}}
                                    </td>                                   
                                    <td class="text-end">
                                        <b class="text-black">₹{{currFormat(data?.average_price_per_night)}}</b>
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
                    @update:currentPage="fetchAveragePriceList"
                    v-show="paginationData?.last_page > 1"
                    />  
                </div>
                <div class="col-auto"><h4 class="text-primary m-0"><span class="text-dark">Average Price:</span> <b>₹{{ currFormat(averagePrice) }}</b></h4></div>
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
    const averagePrice = ref(0);
    const isListLoading = ref(true);


    // filter refs
    const daysWiseModel = ref('30')
    const propertiesName = ref([])
    const selectedProperty = ref('');


    const fetchData = computed(() => {
       return  {
            page: page.value || null,
            days: daysWiseModel.value || '30',
            property_id: selectedProperty.value || null
        }
    })



    const onDaysChange = (e) => {       
        fetchAveragePriceList()
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

   
    const onFilterSearch = ()=>{ 
        fetchAveragePriceList();
    }

    const onReset = ()=>{
        page.value = 1,     
        daysWiseModel.value = "30"       
        selectedProperty.value  = ''      
        fetchAveragePriceList();
    }




    // For fetch Net Revenue list
    const fetchAveragePriceList = (d) => {
        isListLoading.value = true;
        console.log("page", d)
        page.value = d || 1;
        axios.post('api/average-price-per-night',fetchData.value).then(res => {       
            if(res.status){
                 console.log("average", res.data)
                dataList.value = res.data.data;   
                averagePrice.value =  res.data.total_average_price   
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
        daysWiseModel.value = router.options.history.state.days || "30"     
        selectedProperty.value = router.options.history.state.property_id || ''
        fetchPropertiesName();     
        fetchAveragePriceList();      
    })


</script>
