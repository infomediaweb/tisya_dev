<template>
    <div class="page-wrap properties-page">
        <div class="page-title mb-4">       
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Team Members</h1>
                </div>
                <div class="col-lg-4">
                    <form action="">
                        <div class="input-group input-icon">
                            <span class="icon-search"></span>
                            <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                v-model="vSearchQuery"
                                placeholder="Search by name"
                            >
                        </div>
                    </form>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'add-team'}" class="btn rounded-pill btn-secondary-light">
                        <i class="bi bi-plus-lg fs-6 lh-1 me-2"></i>
                        Add Team Member
                    </router-link>
                </div>
            </div>        
        </div>
        <section class="section">   

            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>
            
            <template v-else>
                <div class="outer-wrapper" v-if="teamList.length">
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
                                        <th width="100px" class="text-secondary fw-semibold">Image</th>
                                        <th class="text-secondary fw-semibold" style="width: 40%;">Name</th>
                                        <th class="text-secondary fw-semibold">Designation</th>
                                        <th class="text-secondary fw-semibold">Status</th>
                                        <th  style="width:90px;" class="text-secondary fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <draggable
                                    :list="teamList"
                                    animation="200"
                                    ghost-class="td-drag"
                                    @change="onDragChange"
                                    class="move"
                                    filter=".form-check-input, .action-btn-group" 
                                    tag="tbody">
                                    <tr v-for="(obj, index) in teamList" :key="index">
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
                                        <td>
                                            <img 
                                                :src="obj.image_path ? obj.image_path : placeholder" 
                                                :alt="obj.name"
                                                height="30"
                                            >
                                        </td>
                                        <td>{{ obj.name }}</td>
                                        <td>{{ obj.designation }}</td>
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
                                                    <router-link :to="{name: 'add-team', params: { id: obj.id }}" class="btn ps-0 p-1 fs-5 text-black">
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
    import placeholder from '@assets/images/no-img.jpg'
    import axios from 'axios'
    import { VueDraggableNext as draggable } from 'vue-draggable-next'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { useDebouncedRef } from '@utils/debouncedRef'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'


    const teamList = ref([])
    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const vSearchQuery = useDebouncedRef('', 800)

    const isLoading = ref(false)


    // For get all team list 
    const getTeamList = (pageNumber) => {
        isLoading.value = true

        axios.get('/api/teams', {
            params: {
                page: pageNumber,
                search: vSearchQuery.value
            }
        }).then(res => {
            if(res.data.status){
                teamList.value = res.data.data

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

        axios.get(`/api/teams`, {
            params: {
                search: newVal
            }
        }).then(res => {
            if(res.data.status){
                teamList.value = res.data.data

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
            axios.delete(`/api/teams/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getTeamList()
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
            axios.post(`/api/teams-delete-multiple`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getTeamList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }  
    }


    // For update status
    const updateStatus = (v, {target}, idx) => {
        axios.post(`/api/teams-update-status/${v}`, {
            status: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                teamList.value[idx].status = Number(target.checked)
            }
        }).catch(error => {
            console.log(error)
        })
    }


    // For change position of teams list
    const onDragChange = (e) => {
        let teamListFilter = teamList.value.map(item => item.id)

        axios.post('/api/teams-save-position', {
            position: teamListFilter
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    onMounted(() => {
        getTeamList()
    })

</script>