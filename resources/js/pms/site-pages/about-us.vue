<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">About Us</h1>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form ref="formRef" @submit="onSubmit" v-slot="{ errors }" :initial-values="formInitialValues">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Banner Image<span class="text-danger">*</span></label>
                                        <UploadFile 
                                            name="banner"
                                            id="banner"
                                            fileType="image"
                                            acceptType="image/*"
                                            info="Max size: 1mb | Image size: 1600px . 1080px"
                                            changeText="To change the banner image please click / drag new banner image here!"
                                            size="1"
                                            ratio="35%"
                                            :filePath="aboutEdit.banner_path"
                                            :fileName="aboutEdit.banner"
                                            :rules="aboutEdit.banner ? 'image|size:1048|ext:jpg,jpeg,webp,svg,png' : 'required|image|size:1048|ext:jpg,jpeg,webp,svg,png'"
                                            @emitUploadFile="getUploadBannerFile"
                                            @emitDeleteUploadFile="deleteBannerFile(aboutEdit.id)"
                                        />
                                    </div> 
                                </div>
                                <!-- <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Banner Text</label>
                                        <Field 
                                            type="text"
                                            name="banner_text"
                                            class="form-control"
                                            v-model="aboutEdit.banner_text"
                                        />
                                    </div> 
                                </div> -->
                                <div class="col-12 col-md-12">
                                    <div class="form-group">
                                        <label for="">Content</label>
                                        <ckeditor
                                            name="about_content" 
                                            v-model:data="aboutEdit.about_content"
                                        />
                                    </div> 
                                </div>
                                <!-- <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">Description</label>
                                        <ckeditor
                                            name="service_content"
                                            v-model:data="aboutEdit.service_content"
                                        />
                                    </div> 
                                </div> -->

                                <!-- <div class="col-12 mt-4">
                                    <div class="form-group">
                                        <h6 class="mb-0 fw-bold">About Service Certainty</h6>
                                    </div>
                                </div> -->

                                <!-- <div class="col-12">
                                    <FieldArray name="addMultiItem" v-slot="{ fields, push, remove }"> 
                                        <div class="multi-row">
                                            <div class="repeat-row" v-for="(obj, index) in fields">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="small-action-btn">
                                                                        <div class="btn btn-icon btn-secondary-light pe-none">{{ index + 1 }}</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="small-action-btn">
                                                                        <button 
                                                                            v-if="obj.value.id"
                                                                            type="button" 
                                                                            class="btn btn-icon btn-danger"
                                                                            @click="deleteItem(obj.value.id)">
                                                                            <i class="icon-bi bi-trash fs-6"></i>
                                                                        </button>
                                                                        <button 
                                                                            v-else-if="fields.length > 1"
                                                                            type="button" 
                                                                            class="btn btn-icon btn-danger"
                                                                            @click="remove(index)">
                                                                            <i class="icon-bi bi-trash fs-6"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="">Title<span class="text-danger">*</span></label>
                                                            <Field
                                                                type="text"
                                                                :name="`title-${index}`"
                                                                class="form-control"
                                                                :class="{'border-danger': errors[`title-${index}`]}"
                                                                v-model="obj.value.title"
                                                                rules="required"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <div class="form-group">
                                                            <label for="">Image<span class="text-danger">*</span></label>
                                                            <UploadFile 
                                                                :name="`image-${index}`"
                                                                :id="`image-${index}`"
                                                                fileType="image"
                                                                acceptType="image/*"
                                                                info="Max size: 1mb | Image size: 1600px . 1080px"
                                                                changeText="To change the image please click / drag new image here!"
                                                                size="1"
                                                                ratio="40%"
                                                                :filePath="obj.value.image_path"
                                                                :fileName="obj.value.image"
                                                                :rules="obj.value.image ? 'image|size:1048|ext:jpg,jpeg,webp,svg,png' : 'required|image|size:1048|ext:jpg,jpeg,webp,svg,png'"
                                                                @emitUploadFile="getUploadServicesFile($event, index, fields)"
                                                                @emitDeleteFrontendUploadFile="() => fields[index].value.image = ''"
                                                                @emitDeleteUploadFile="deleteServicesFile(index, obj.value.id, fields)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <div class="form-group ckeditor-form">
                                                            <label for="">Content</label>
                                                            <ckeditor
                                                                :name="`content-${index}`"
                                                                v-model:data="obj.value.text"
                                                            />
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="action-btn" v-if="fields.length < 4">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button 
                                                            type="button" 
                                                            class="btn btn-save btn-secondary"
                                                            @click="push({
                                                                title: '',
                                                                image: '',
                                                                text: '',
                                                            })"
                                                            :disabled="!perviousArrayItem(fields).title || !perviousArrayItem(fields).image">
                                                            <i class="bi bi-plus-lg me-2"></i>Add More
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </FieldArray>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="row pt-4">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-save btn-primary">
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
    import ckeditor from '@components/ckeditor.vue'
    import axios from 'axios'
    import { ref, onMounted, reactive, defineAsyncComponent, nextTick } from 'vue'
    import { Form, Field, FieldArray, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const formRef = ref()
    const aboutEdit = ref({})
    const uploadBannerFile = ref({})
    const uploadFile = ref({})
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


    // For initial value of addMultiItem
    const formInitialValues = reactive({
        addMultiItem: [
            {
                title: '',
                image: '',
                text: ''
            }
        ]
    })

    // For get pervious array item 
    const perviousArrayItem = (value) => {
        let perviousItem = value[value.length - 1]
        return perviousItem.value
    }

    // For upload banner file values
    const getUploadBannerFile = (value) => {
        uploadBannerFile.value = value
    }

    // For upload file values
    const getUploadServicesFile = (value, idx, fields) => {
        uploadFile.value = value
        fields[idx].value.image = uploadFile.value.filename
    }


    // For get about data
    const getAboutEdit = () => {
        isLoading.value = true

        axios.get('/api/about-us').then(res => {
            if(res.data.status){
                aboutEdit.value = res.data.data
                
                if(aboutEdit.value.about_information.length){
                    formInitialValues.addMultiItem = aboutEdit.value.about_information
                }
                else{
                    formInitialValues.addMultiItem = [{
                        title: '',
                        image: '',
                        text: ''
                    }]
                }

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            if(error.response.data.message){
                toast(error.response.data.message, 'error').show()  
            }
            isLoading.value = false
        })
    }


    // For delete banner image 
    const deleteBannerFile = (id) => {
        axios.get(`/api/about-us-delete-image/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                aboutEdit.value.banner = ''
                aboutEdit.value.banner_path = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For delete services image 
    const deleteServicesFile = (idx, id, fields) => {
        axios.get(`/api/about-information-delete-image/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                fields[idx].value.image = ''
                fields[idx].value.image_path = ''
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For delete service item
    const deleteItem = (id) => {
        dialog('Are you sure you want to remove?', confirm).show()

        function confirm(){
            axios.get(`/api/single-delete-record/${id}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    setTimeout(() => {
                        getAboutEdit()
                    }, 400)
                }
            }).catch(error => {
                toast(error.response.data.message, 'error').show()
            })
        }
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        axios.post('/api/about-us', {
            banner: uploadBannerFile.value.filename,
            banner_text: v.banner_text,
            about_content: v.about_content,
            service_content: v.service_content,
            multiServices: v.addMultiItem,
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    getAboutEdit()
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getAboutEdit()
    })


</script>
