<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Second Home</h1>
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
                                        <label for="">Title<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text"
                                            name="tag_line"
                                            class="form-control"
                                            :class="{'border-danger': errors.tag_line}"
                                            v-model="secondHomeEdit.tag_line"
                                            rules="required"
                                        />
                                    </div> 
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Introduction</label>
                                        <Field 
                                            as="textarea"
                                            name="introduction"
                                            class="form-control" 
                                            v-model="secondHomeEdit.introduction"
                                            rows="3"
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
                                                                        :filePath="secondHomeEdit[`image${obj}`] ? secondHomeEdit[`imagePath${obj}`] + secondHomeEdit[`image${obj}`] : ''"
                                                                        :fileName="secondHomeEdit[`image${obj}`]"
                                                                        :rules="secondHomeEdit[`image${obj}`] ? 'image|size:1048|ext:jpg,jpeg,webp,svg,png' : 'required|image|size:1048|ext:jpg,jpeg,webp,svg,png'"
                                                                        @emitUploadFile="getUploadFile($event, `image${obj}`)"
                                                                        @emitDeleteUploadFile="deleteImage(`image${obj}`, secondHomeEdit[`image${obj}`])"
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
                                                                        v-model="secondHomeEdit[`title${obj}`]"
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
                                                                        v-model="secondHomeEdit[`description${obj}`]"
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
    import axios from 'axios'
    import { ref, onMounted, defineAsyncComponent } from 'vue'
    import { Form, Field } from 'vee-validate'
    import { toast } from '@utils/toast'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const formRef = ref()
    const uploadImages = ref({})
    const secondHomeEdit = ref({})
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)



    // For upload file values
    const getUploadFile = async (value, fieldName) => {
        uploadImages.value[fieldName] = await value.filename
    }


    // For get second home data
    const getSecondHomeEdit = async () => {
        isLoading.value = true

        await axios.get('/api/second-home').then(res => {
            if(res.data.status){
                secondHomeEdit.value = res.data.data

                for(let i = 1; i < 4; i++){
                    if(!secondHomeEdit.value[`image${i}`]){
                        secondHomeEdit.value[`imagePath${i}`] = ''
                    }
                    else{
                        secondHomeEdit.value[`imagePath${i}`] = secondHomeEdit.value.image_path
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
        axios.post(`/api/second-home-delete-image`, {
            fieldName: fieldName,
            imageName: imageName
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                secondHomeEdit.value[fieldName] = ''
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

        axios.post('/api/second-home', v).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    getSecondHomeEdit()
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getSecondHomeEdit()
    })

</script>
