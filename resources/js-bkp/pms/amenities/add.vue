<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Amenity' : 'Add Amenity' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'list-amenities'}" class="btn rounded-pill btn-secondary-light">
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
                <Form @submit="onSubmit" v-slot="{ errors }">
                    <div class="row">
                        <div class="col-12 col-lg-10 col-xl-7">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="amenities_name"
                                            :class="{'border-danger': errors.amenities_name}"
                                            class="form-control" 
                                            v-model="amenitiesEditData.amenities_name"
                                            rules="required"
                                        />
                                    </div> 
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Icon<span class="text-danger">*</span></label>
                                        <UploadFile 
                                            name="image"
                                            id="image"
                                            fileType="image"
                                            acceptType="image/*"
                                            info="Max size: 1mb | Image size: 100px . 100px"
                                            changeText="To change the icon please click / drag new icon here!"
                                            size="1"
                                            ratio="15%"
                                            :filePath="amenitiesEditData.amenities_image"
                                            :fileName="amenitiesEditData.amenities_image_name"
                                            :rules="amenitiesEditData.amenities_image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                            @emitUploadFile="getUploadFile"
                                            @emitDeleteUploadFile="deleteUploadFile"
                                        />
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
    import { ref, onMounted, defineAsyncComponent } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const router = useRouter()
    const route = useRoute()

    const uploadFile = ref({})
    const submitApiUrl = ref(null)
    const amenitiesEditData = ref({})
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


    // For location edit 
    const amenitiesEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/amenities/${route.params.id}`).then(res => {
                if(res.data.status){
                    amenitiesEditData.value = res.data.data

                    setTimeout(() => {
                        isLoading.value = false
                    }, 400)
                }
            }).catch(error => {
                toast(error.response.data.message, 'error').show()
                isLoading.value = false
            })
        }
    }


    // For upload file values
    const getUploadFile = (value) => {
        uploadFile.value = value
    }


    // For delete uploaded file
    const deleteUploadFile = (id) => {
        axios.get(`/api/amenities-image-delete/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                amenitiesEditData.value.amenities_image = ''
                amenitiesEditData.value.amenities_image_name = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }

    
    // For on submit form 
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/amenities/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/amenities'
        }

        axios.post(submitApiUrl.value, {
            amenities_name: v.amenities_name,
            amenities_image: uploadFile.value.filename,
            amenities_image_filepath: uploadFile.value.filepath
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    router.push({name: 'list-amenities'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        amenitiesEdit()
    })

</script>