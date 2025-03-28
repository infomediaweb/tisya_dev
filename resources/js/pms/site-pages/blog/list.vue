<template>
    <div class="page-wrap properties-page">
        <div class="page-title mb-4">       
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Blogs</h1>
                </div>
                <div class="col-lg-4">
                    <form action="">
                        <div class="input-group input-icon">
                            <span class="icon-search"></span>
                            <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                v-model="vSearchQuery"
                                placeholder="Search by title"
                            >
                        </div>
                    </form>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'add-blog'}" class="btn rounded-pill btn-secondary-light">
                        <i class="bi bi-plus-lg fs-6 lh-1 me-2"></i>
                        Add Blog
                    </router-link>
                </div>
            </div>        
        </div>
        <section class="section">   

            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>
            
            <template v-else>
                <div class="outer-wrapper" v-if="blogList.length">
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
                                        <th width="80px" class="text-secondary fw-semibold">Image</th>
                                        <th class="text-secondary fw-semibold" style="width: 45%;">Title</th>
                                        <th class="text-secondary fw-semibold">Date</th>
                                        <th class="fw-semibold">Show On Home</th>
                                        <th class="text-secondary fw-semibold">Status</th>
                                        <th  style="width:90px;" class="text-secondary fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(obj, index) in blogList" :key="index">
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
                                        <td>{{ obj.title }}</td>
                                        <td>{{ format(obj.date) }}</td>
                                        <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="s1"
                                                :checked="obj.show_on_blog_page == 1"
                                                @change="updateShowOnBlog(obj.id, $event)"
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
                                                    @change="updateStatus(obj.id, $event, index)"
                                                    true-value="1"
                                                    false-value="0"
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="action-btn-group mb-0 mw-0">
                                                <li>
                                                    <router-link :to="{name: 'add-blog', params: { id: obj.id }}" class="btn ps-0 p-1 fs-5 text-black">
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
                                    @pagination="getBlogList"
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
    import placeholder from '@assets/images/no-img.jpg'
    import axios from 'axios'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { useDebouncedRef } from '@utils/debouncedRef'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'
    import { format } from '@utils/common'


    const blogList = ref([])
    const dataRow = ref({})
    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const vSearchQuery = useDebouncedRef('', 800)

    const isLoading = ref(false)


    // For get all blog list 
    const getBlogList = (pageNumber) => {
        isLoading.value = true

        axios.get('/api/blogs', {
            params: {
                page: pageNumber,
                search: vSearchQuery.value
            }
        }).then(res => {
            if(res.data.status){
                blogList.value = res.data.data.data
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

        axios.get(`/api/blogs`, {
            params: {
                search: newVal
            }
        }).then(res => {
            if(res.data.status){
                blogList.value = res.data.data.data
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
            axios.delete(`/api/blogs/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getBlogList()
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
            axios.post(`/api/blogs-delete-multiple-record`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        getBlogList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }  
    }


    // For update status
    const updateStatus = (v, {target}, idx) => {
        axios.post(`/api/blogs-update-status/${v}`, {
            status: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                blogList.value[idx].status = Number(target.checked)
            }
        }).catch(error => {
            console.log(error)
        })
    }

    const updateShowOnBlog = (v, {target}) => {
        axios.post(`/api/show-on-home-blog/${v}`, {
            show_on_blog: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }

    onMounted(() => {
        getBlogList()
    })

</script>