<template>
    <div class="page-wrap properties-page">
        <div class="page-title mb-4">       
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Testimonials List</h1>
                </div>
                <div class="col-lg-4">
                    <form action="">
                        <div class="input-group input-icon">
                            <span class="icon-search"></span>
                            <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                v-model="vSearchQuery"
                                placeholder="Search by guest name"
                            >
                        </div>
                    </form>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'add-testimonial'}" class="btn rounded-pill btn-secondary-light">
                        <i class="bi bi-plus-lg fs-6 lh-1 me-2"></i>
                        Add Testimonial
                    </router-link>
                </div>
            </div>        
        </div>
        <section class="section">   

            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>
            
            <template v-else>
                <div class="outer-wrapper" v-if="testimonialList.length">
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
                                        <th class="text-secondary fw-semibold" style="width: 30%;">Guest Name</th>
                                        <th class="text-secondary fw-semibold">Home Name</th>
                                        <th class="text-secondary fw-semibold">Date</th>
                                        <th class="text-secondary fw-semibold">Status</th>
                                        <th  style="width:90px;" class="text-secondary fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <draggable
                                    :list="testimonialList"
                                    animation="200"
                                    ghost-class="td-drag"
                                    @change="onDragChange"
                                    class="move"
                                    filter=".form-check-input, .action-btn-group" 
                                    tag="tbody">
                                    <tr v-for="(obj, index) in testimonialList" :key="index">
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
                                        <td>{{ obj.guest_name }}</td>
                                        <td>{{ obj.home_name }}</td>
                                        <td>{{ format(obj.date) }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    role="switch" 
                                                    id="s1" 
                                                    :checked="obj.status == 1"
                                                    @change="updateStatus(obj.id, $event, index)"
                                                    true-value="1"
                                                    false-value="0"
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="action-btn-group mb-0 mw-0">
                                                <li>
                                                    <router-link :to="{name: 'add-testimonial', params: { id: obj.id }}" class="btn ps-0 p-1 fs-5 text-black">
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
                                </draggable>
                            </table>
                        </div>
                    </div>

                    <div class="table-footer pt-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <button 
                                    class="btn btn-save btn-primary" 
                                    @click="deleteMultiItem" 
                                    :disabled="(!vCheckAll && vSelectCheckValue.length == 0) || vSelectCheckValue.length == 0">
                                    Delete Selected
                                </button>
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
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { VueDraggableNext as draggable } from 'vue-draggable-next'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { useDebouncedRef } from '@utils/debouncedRef'
    import { toast } from '@utils/toast'
    import { format } from '@utils/common'
    import { dialog } from '@utils/modal'


    const testimonialList = ref([])
    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const vSearchQuery = useDebouncedRef('', 800)

    const isLoading = ref(false)


    // For get all testimonials list 
    const getTestimonialList = (pageNumber) => {
        isLoading.value = true

        axios.get('/api/testimonials', {
            params: {
                page: pageNumber,
                search: vSearchQuery.value
            }
        }).then(res => {
            if(res.data.status){
                testimonialList.value = res.data.data

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

        axios.get(`/api/testimonials`, {
            params: {
                search: newVal
            }
        }).then(res => {
            if(res.data.status){
                testimonialList.value = res.data.data

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
            axios.delete(`/api/testimonials/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getTestimonialList()
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
            axios.post(`/api/testimonials-delete-multiple-record`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getTestimonialList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }  
    }


    // For update status
    const updateStatus = (v, {target}, idx) => {
        axios.post(`/api/testimonials-update-status/${v}`, {
            status: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                testimonialList.value[idx].status = Number(target.checked)
            }
        }).catch(error => {
            console.log(error)
        })
    }


    // For change position of testimonials list
    const onDragChange = (e) => {
        let testimonialListFilter = testimonialList.value.map(item => item.id)

        axios.post('/api/testimonials-save-position', {
            position: testimonialListFilter
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    onMounted(() => {
        getTestimonialList()
    })

</script>