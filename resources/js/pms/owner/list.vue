<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Owner List</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'add-owner'}" class="btn rounded-pill btn-secondary-light">
                        <i class="bi bi-plus-lg fs-6 lh-1 me-2"></i>
                        Add Owner
                    </router-link>
                </div>
            </div>
        </div>

        <section class="section">  
            
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>
            
            <template v-else>
                <div class="outer-wrapper" v-if="ownerList.length">
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
                                        <th width="25%" class="text-secondary fw-semibold">Name</th>
                                        <th class="text-secondary fw-semibold">Email</th>
                                        <th class="text-secondary fw-semibold">Mobile</th>
                                        <th class="text-secondary fw-semibold">Status</th>
                                        <th  style="width:90px;" class="text-secondary fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(obj, index) in ownerList">
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
                                        <td>{{ obj.name }}</td>
                                        <td>{{ obj.email }}</td>
                                        <td>{{ obj.mobile_no }}</td>
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
                                                    <router-link :to="{name: 'add-owner', params: { id: obj.id }}" class="btn ps-0 p-1 fs-5 text-black">
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
    import { ref, onMounted, watch, inject, nextTick } from 'vue'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'

    const ownerList = ref([])

    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const isLoading = ref(false)


    // For get all owner list 
    const getOwnerList = () => {
        isLoading.value = true

        axios.get('/api/owner-list').then(res => {
            if(res.data.status){
                ownerList.value = res.data.data

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
            axios.delete(`/api/delete-owner/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getOwnerList()
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
            axios.post(`/api/delete-multiple-owner`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getOwnerList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }  
    }


    // For update status
    const updateStatus = (v, {target}) => {
        axios.post(`/api/owner-update-status/${v}`, {
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
        getOwnerList()
    })
    
</script>