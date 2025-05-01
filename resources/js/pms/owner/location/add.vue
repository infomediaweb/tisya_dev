<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Location' : 'Add Location' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'list-location'}" class="btn rounded-pill btn-secondary-light">
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
                        <div class="col-12 col-lg-10 col-xl-7">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">State<span class="text-danger">*</span></label>
                                        <Field 
                                            class="form-control" 
                                            :class="{'border-danger': errors.state}"
                                            name="state"
                                            as="select"
                                            rules="required"
                                            v-model="vStateId">
                                            <option value="" selected disabled>Select State</option>
                                            <option
                                                v-for="(obj, index) in state"
                                                :value="obj.id">
                                                {{ obj.name }}
                                            </option>
                                        </Field>
                                    </div>
                                </div> 

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="location_name"
                                            :class="{'border-danger': errors.location_name}"
                                            class="form-control" 
                                            placeholder="Name" 
                                            v-model="locationEditData.location_name"
                                            rules="required"
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
                                            ratio="15%"
                                            :filePath="locationEditData.image"
                                            :fileName="locationEditData.image_name"
                                            :rules="locationEditData.image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                            @emitUploadFile="getUploadFile"
                                            @emitDeleteUploadFile="deleteUploadFile"
                                        />

                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-group">
                                        <h6 class="mb-0 fw-bold">SEO Fields</h6>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label>Title</label>
                                    <div class="form-group">
                                        <Field 
                                            type="text" 
                                            name="meta_title" 
                                            class="form-control"
                                            v-model="locationEditData.meta_title"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label>Keywords</label>
                                    <div class="form-group">
                                        <Field 
                                            as="textarea" 
                                            name="meta_keyword" 
                                            class="form-control"
                                            rows="3"
                                            v-model="locationEditData.meta_keyword"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label>Description</label>
                                    <div class="form-group">
                                        <Field 
                                            as="textarea" 
                                            name="meta_description" 
                                            class="form-control"
                                            rows="3"
                                            v-model="locationEditData.meta_description"
                                        />
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

            axios.get(`/api/location/${route.params.id}`).then(res => {
                if(res.data.status){
                    locationEditData.value = res.data.data
                    vStateId.value = locationEditData.value.state_id
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
        axios.get(`/api/location-image-delete/${id}`).then(res => {
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
            submitApiUrl.value = `/api/location/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/location'
        }
        
        axios.post(submitApiUrl.value, {
            state_id: vStateId.value,
            location_name: v.location_name,
            image: uploadFile.value.filename,
            meta_title: v.meta_title,
            meta_keyword: v.meta_keyword,
            meta_description: v.meta_description,
            status: vStatusChecked.value
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    router.push({name: 'list-location'})
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