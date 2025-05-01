<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Join Our Network - Intro</h1>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form ref="formRef" @submit="onSubmit" v-slot="{ errors }">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Introduction<span class="text-danger">*</span></label>
                                        <ckeditor
                                            name="introduction"
                                            :rules="joinOurNetworkEdit.introduction ? '' : 'required'"
                                            v-model:data="joinOurNetworkEdit.introduction"
                                        />
                                    </div> 
                                </div>

                                <div class="col-12">
                                    <div class="row nth-row g-4">
                                        <div class="col-12 col-lg-6" v-for="(obj, idx) in 3">
                                            <div class="multi-row">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="">Image<span class="text-danger">*</span></label>
                                                                    <UploadFile 
                                                                        :name="`image${obj}`"
                                                                        :id="`image${obj}`"
                                                                        fileType="image"
                                                                        acceptType="image/*"
                                                                        info="Max size: 1mb | Image size: 1280px . 720px"
                                                                        changeText="To change the image please click / drag new image here!"
                                                                        size="1"
                                                                        ratio="40%"
                                                                        :filePath="joinOurNetworkEdit[`image${obj}`] ? joinOurNetworkEdit[`imagePath${obj}`] + joinOurNetworkEdit[`image${obj}`] : ''"
                                                                        :fileName="joinOurNetworkEdit[`image${obj}`]"
                                                                        :rules="joinOurNetworkEdit[`image${obj}`] ? 'image|size:1048|ext:jpg,jpeg,webp,svg,png' : 'required|image|size:1048|ext:jpg,jpeg,webp,svg,png'"
                                                                        @emitUploadFile="getUploadFile($event, `image${obj}`)"
                                                                        @emitDeleteUploadFile="deleteImage(`image${obj}`, joinOurNetworkEdit[`image${obj}`])"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="">Title<span class="text-danger">*</span></label>
                                                                    <Field
                                                                        type="text"
                                                                        :name="`title${obj}`"
                                                                        class="form-control"
                                                                        :class="{'border-danger': errors[`title${obj}`]}"
                                                                        v-model="joinOurNetworkEdit[`title${obj}`]"
                                                                        rules="required"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group mb-0">
                                                                    <label for="">Description</label>
                                                                    <Field 
                                                                        as="textarea"
                                                                        :name="`description${obj}`"
                                                                        class="form-control"
                                                                        :class="{'border-danger': errors[`description${obj}`]}"
                                                                        v-model="joinOurNetworkEdit[`description${obj}`]"
                                                                        rows="5"
                                                                    />
                                                                </div> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
    import { ref, onMounted, defineAsyncComponent } from 'vue'
    import { Form, Field } from 'vee-validate'
    import { toast } from '@utils/toast'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const formRef = ref()
    const uploadImages = ref({})
    const joinOurNetworkEdit = ref({})
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)



    // For upload file values
    const getUploadFile = async (value, fieldName) => {
        uploadImages.value[fieldName] = await value.filename
    }


    // For get join our network data
    const getJoinOurNetworkEdit = async () => {
        isLoading.value = true

        await axios.get('/api/join-our-network-intro').then(res => {
            if(res.data.status){
                joinOurNetworkEdit.value = res.data.data

                for(let i = 1; i < 4; i++){
                    if(!joinOurNetworkEdit.value[`image${i}`]){
                        joinOurNetworkEdit.value[`imagePath${i}`] = ''
                    }
                    else{
                        joinOurNetworkEdit.value[`imagePath${i}`] = joinOurNetworkEdit.value.image_path
                    }
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


    // For delete uploaded image 
    const deleteImage = (fieldName, imageName) => {
        axios.post(`/api/join-our-network-intro-delete-image`, {
            fieldName: fieldName,
            imageName: imageName
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                joinOurNetworkEdit.value[fieldName] = ''
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }

  
    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true
        
        for(let i = 1; i < 4; i++){
            v[`image${i}`] = uploadImages.value[`image${i}`]
        }

        axios.post('/api/join-our-network-intro', v).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    getJoinOurNetworkEdit()
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getJoinOurNetworkEdit()
    })

</script>
