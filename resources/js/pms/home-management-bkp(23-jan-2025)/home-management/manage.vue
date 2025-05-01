<template>
    <div class="page-wrap properties-page">
        <div class="page-title mb-4">       
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Manage Homes</h1>
                </div>
                <div class="col-lg-4">
                    <form action="">
                        <div class="input-group input-icon">
                            <span class="icon-search"></span>
                            <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                v-model="vSearchQuery"
                                placeholder="Search by name, location, state"
                            >
                        </div>
                    </form>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'add-home'}" class="btn rounded-pill btn-secondary-light">
                        <i class="bi bi-plus-lg fs-6 lh-1 me-2"></i>
                        Add Home
                    </router-link>
                </div>
            </div>        
        </div>
        <section class="section">   

            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>
            
            <template v-else>
                <div class="outer-wrapper" v-if="homeList.length">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 mw-lg">
                                <thead>
                                    <tr>
                                        <th width="45px">
                                            <input 
                                                class="form-check-input form-check-lg mt-0"
                                                type="checkbox"
                                                v-model="vCheckAll"
                                                @change="onCheckAll"
                                            >
                                        </th>
                                        <th style="width:90px;" class="fw-semibold">Image</th>
                                        <th class="text-secondary fw-semibold" style="width: 25%;">Name</th>
                                        <th class="text-secondary fw-semibold">Location</th>
                                        <th class="text-secondary fw-semibold">State</th>
                                        <th class="fw-semibold">Only For Enquiry</th>
                                        <th class="fw-semibold">Show On Home</th>
                                        <th class="fw-semibold">Show On Apartment</th>
                                        <th class="text-secondary fw-semibold">Status</th>
                                        <th  style="width:90px;" class="text-secondary fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(obj, index) in homeList" :key="index">
                                        <td>
                                            <input 
                                                class="form-check-input form-check-lg mt-0"
                                                ref="selectCheckRef"
                                                :checked="checkedSelectAll"
                                                :value="obj.id"
                                                v-model="vSelectCheckValue"
                                                type="checkbox"
                                            >
                                        </td>
                                        <td> <img width="85px" :src="obj.image_full_path" ></td>
                                        <td>
                                            <div class="p-detail pe-5">
                                                <h3>{{ obj.home_name }}</h3>
                                                <p>
                                                    <span v-if="obj.maximum_number_of_guests">Occupancy: {{ obj.maximum_number_of_guests }}</span>  
                                                    <span v-if="obj.no_of_bedrooms"> | Bedrooms: {{ obj.no_of_bedrooms }}</span> 
                                                    <span v-if="obj.no_of_bathrooms"> | Bathrooms: {{ obj.no_of_bathrooms }}</span> 
                                                    <span v-if="obj.no_of_staff"> | Staff: {{ obj.no_of_staff }}</span>
                                                </p>
                                            </div>
                                        </td>
                                        <td>{{ obj.location }}</td>
                                        <td>{{ obj.state }}</td>
                                        <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="s1"
                                                :checked="obj.only_for_enquiry == 1"
                                                @change="updateOnlyForEnquiry(obj.id, $event)"
                                                true-value="1"
                                                false-value="0"
                                            >
                                        </div>
                                        </td>
                                        <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="s1"
                                                :checked="obj.show_on_home == 1"
                                                @change="updateShowOnHome(obj.id, $event)"
                                                true-value="1"
                                                false-value="0"
                                            >
                                        </div>
                                        </td>
                                        <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="s1"
                                                :checked="obj.show_on_apartment == 1"
                                                @change="updateShowOnApartment(obj.id, $event)"
                                                true-value="1"
                                                false-value="0"
                                            >
                                        </div>
                                        </td>
                                        <td>
                                        <div class="form-check form-switch">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox" 
                                                role="switch" 
                                                id="s1" 
                                                :checked="obj.status == 1"
                                                @change="updateStatus(obj.id, $event)"
                                                true-value="1"
                                                false-value="0"
                                            >
                                        </div>
                                        </td>
                                        <td>
                                            <ul class="action-btn-group mb-0 mw-0">
                                                <li>
                                                    <router-link :to="{name: 'add-home', params: { id: obj.id }}" class="btn ps-0 p-1 fs-5 text-black">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <button @click="deleteItem(obj.id)" class="btn p-1 fs-5 text-black">
                                                        <i class="icon-bi bi-trash"></i>
                                                    </button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>                     
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-footer pt-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <button 
                                    class="btn btn-save btn-primary" 
                                    @click="deleteMultiItem" 
                                    :disabled="(!vCheckAll && vSelectCheckValue.length == 0) || vSelectCheckValue.length == 0 || dataRow.total == 0">
                                    Delete Selected
                                </button>
                            </div>
                            <div class="col" v-if="dataRow.last_page > 1">
                                <Pagination 
                                    :links="dataRow.links"
                                    @pagination="getHomeList"
                                />
                            </div>
                        </div>
                    </div>                    
                </div>

                <div class="message text-center" v-else>
                    <h6 class="fw-bold">No Record Found</h6>
                </div>

            </template>

        </section>
    </div>
</template>
<script setup>
    import Pagination from '@components/pagination.vue'
    import axios from 'axios'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { useDebouncedRef } from '@utils/debouncedRef'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'


    const homeList = ref([])
    const dataRow = ref({})
    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const vSearchQuery = useDebouncedRef('', 800)

    const isLoading = ref(false)


    // For get all home list 
    const getHomeList = (pageNumber) => {
        isLoading.value = true

        axios.get('/api/home', {
            params: {
                page: pageNumber,
                search: vSearchQuery.value
            }
        }).then(res => {
            if(res.data.status){
                homeList.value = res.data.data.data
                dataRow.value = res.data.data

                setTimeout(() => {
                    vCheckAll.value = false
                    checkedSelectAll.value = false
                    vSelectCheckValue.value = []
                    isLoading.value = false
                }, 400)
                
            }
        }).catch(error => {
            isLoading.value = false
        })
    }


    // For Search Query
    watch(vSearchQuery, (newVal, oldVal) => {
        isLoading.value = true

        axios.get(`/api/home`, {
            params: {
                search: newVal
            }
        }).then(res => {
            if(res.data.status){
                homeList.value = res.data.data.data
                dataRow.value = res.data.data

                setTimeout(() => {
                    vCheckAll.value = false
                    checkedSelectAll.value = false
                    vSelectCheckValue.value = []
                    isLoading.value = false
                }, 200)
            }
        }).catch(error => {
            console.log(error)
        })
    })
    const updateShowOnHome = (v, {target}) => {
        axios.post(`/api/show-on-home-update/${v}`, {
            show_on_home: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }
    const updateOnlyForEnquiry = (v, {target}) => {
        axios.post(`/api/show-enquiry-update/${v}`, {
            only_for_enquiry: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }
    const updateShowOnApartment = (v, {target}) => {
        axios.post(`/api/show-on-apartment-update/${v}`, {
            show_on_apartment: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }


    // For check all list 
    const onCheckAll = () => {
        if(vCheckAll.value){
            checkedSelectAll.value = true
            vSelectCheckValue.value = []
            
            selectCheckRef.value.forEach(v => {
                vSelectCheckValue.value.push(v.value)
            })
        }
        else{
            checkedSelectAll.value = false
            vSelectCheckValue.value = []
        }   
    }

    
    // For check all item selected or not 
    watch(vSelectCheckValue, (newVal, oldVal) => {
        if(newVal.length != selectCheckRef?.value?.length){
            vCheckAll.value = false
        }
        else{
            if(newVal.length){
                vCheckAll.value = true
            }  
        }
    })


    // For delete single item
    const deleteItem = (v) => {
        dialog('Are you sure you want to delete?', confirm).show()

        function confirm(){
            axios.delete(`/api/home/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getHomeList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }
    }


    // For delete multi list items
    const deleteMultiItem = () => {
        dialog('Are you sure you want to delete?', confirm).show()

        function confirm(){
            axios.post(`/api/home-delete-multiple`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getHomeList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }  
    }


    // For update status
    const updateStatus = (v, {target}) => {
        axios.post(`/api/home-update-status/${v}`, {
            status: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }



    onMounted(() => {
        getHomeList()
    })

</script>