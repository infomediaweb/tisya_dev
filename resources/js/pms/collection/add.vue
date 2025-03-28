<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Collection' : 'Add Collection' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'list-collection'}" class="btn rounded-pill btn-secondary-light">
                        <i class="icon-list me-2"></i>
                        Manage
                    </router-link>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>
            
            <div class="page-content" v-else>
                <Form action="" @submit="onSubmit" v-slot="{errors}">
                    <div class="row">
                        <div class="col-12 ">
                            <div class="row">
                                <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Collection Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="collection_name"
                                            :class="{'border-danger': errors.collection_name}"
                                            class="form-control" 
                                            placeholder="Name" 
                                            v-model="locationEditData.collection_name"
                                            rules="required"
                                        />
                                    </div>
                                </div>
                               </div>
                               <div class="row">
                               <div class="col-12 col-lg-6">
                                    <div class="form-group ckeditor-form">
                                        <label for="">Description<span class="text-danger">*</span></label>
                                        <ckeditor
                                            name="collection_description" 
                                            v-model:data="locationEditData.collection_description"
                                        />
                                    </div> 
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Image<span class="text-danger">*</span></label>
                                        <UploadFile 
                                            name="image"
                                            id="image"
                                            fileType="image"
                                            acceptType="image/*"
                                            info="Max size: 1mb | Image size: 100px . 100px"
                                            changeText="To change the icon please click / drag new icon here!"
                                            size="1"
                                            ratio="20%"
                                            :filePath="locationEditData.image"
                                            :fileName="locationEditData.image_name"
                                            :rules="locationEditData.image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                            @emitUploadFile="getUploadFile"
                                            @emitDeleteUploadFile="deleteUploadFile"
                                        />

                                    </div>
                                </div>
                            </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="form-check form-switch">
                                            <input 
                                                name="status" 
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="vStatusChecked"
                                                true-value="1"
                                                false-value="0"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <button class="btn btn-save btn-primary">
                                    <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                    <span v-else>SUBMIT</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </Form>
            </div> 
        </section>
    </div>
</template>
<script setup>

    import axios from 'axios'
    import { ref, onMounted,defineAsyncComponent } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'
    import ckeditor from '@components/ckeditor.vue'
    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))
    const uploadFile = ref({})
    const route = useRoute()
    const router = useRouter()
    const state = ref([])
    const vStateId = ref(null)
    const vStatusChecked = ref(1)
    const locationEditData = ref({})
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)
    
    
    // For get state edit 
    const getState = () => {
        axios.get('/api/states').then(res => {
            if(res.data.status){
                state.value = res.data.data
            }
        }).catch(error => {
            console.log(error);
        })
    }

    // For location edit 
    const locationEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/collection/${route.params.id}`).then(res => {
                if(res.data.status){
                    locationEditData.value = res.data.data
                    vStateId.value = locationEditData.value.state_id
                    locationEditData.value.collection_description = locationEditData.value.collection_description || '';
                    vStatusChecked.value = locationEditData.value.status
                    setTimeout(() => {
                        isLoading.value = false
                    }, 400)
                }
            }).catch(error => {
                isLoading.value = false
                console.log(error);
            })
        }
    }

    const getUploadFile = (value) => {
        uploadFile.value = value
    }

// For delete uploaded file
const deleteUploadFile = (id) => {
        axios.get(`/api/collection-image-delete/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                locationEditData.value.image_name = ''
                locationEditData.value.image = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }

    // For form on submit
    const onSubmit = (v, {resetForm}) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/collection/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/collection'
        }
        
        axios.post(submitApiUrl.value, {
            collection_name: v.collection_name,
            image: uploadFile.value.filename,
            collection_description: v.collection_description,
            status: vStatusChecked.value
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    router.push({name: 'list-collection'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
 
    }

    onMounted(() => {
        getState()
        locationEdit()
    })


</script>