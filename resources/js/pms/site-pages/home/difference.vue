<template>
     <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Our Difference</h1>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form ref="formRef" @submit="onSubmit"  v-slot="{ meta, errors }" :initial-values="formInitialValues">
                    <div class="row">
                        <div class="col-12">
                            <FieldArray name="addMultiItem" v-slot="{ fields, push, remove }">
                                <div class="multi-row">
                                    <div class="repeat-row" v-for="(obj, index) in fields" :key="obj.key">
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
                                                                <!-- For delete from database -->
                                                                <button 
                                                                    v-if="obj.value.id"
                                                                    type="button" 
                                                                    class="btn btn-icon btn-danger"
                                                                    @click="deleteItem(obj.value.id)">
                                                                    <i class="icon-bi bi-trash fs-6"></i>
                                                                </button>

                                                                <!-- For delete from frontend -->
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
                                                <div class="form-group ckeditor-form">
                                                    <label for="">Text<span class="text-danger">*</span></label>
                                                    <ckeditor
                                                        :name="`detail-${index}`" 
                                                        
                                                        :rules="obj.value.detail ? '' : 'required'"
                                                        v-model:data="obj.value.detail"
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
                                                        :filePath="obj.value.imagePath"
                                                        :fileName="obj.value.image"
                                                        :rules="obj.value.image ? 'image|size:1048|ext:jpg,jpeg,webp,svg,png' : 'required|image|size:1048|ext:jpg,jpeg,webp,svg,png'"
                                                        @emitUploadFile="getUploadFile($event, index, fields)"
                                                        @emitDeleteFrontendUploadFile="() => fields[index].value.image = ''"
                                                        @emitDeleteUploadFile="getDeleteUploadFile(index, obj.value.id, fields)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="action-btn" v-if="fields.length < 5">
                                        <div class="row">
                                            <div class="col-12">
                                                <button 
                                                    type="button" 
                                                    class="btn btn-save btn-secondary"
                                                    @click="push({
                                                        title: '',
                                                        detail: '',
                                                        image: '',
                                                    })"
                                                    :disabled="!perviousArrayItem(fields).title || !perviousArrayItem(fields).detail || !perviousArrayItem(fields).image">
                                                    <i class="bi bi-plus-lg me-2"></i>Add More
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </FieldArray>
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
    const uploadFile = ref({})
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    
    // For initial value of addMultiItem
    const formInitialValues = reactive({
        addMultiItem: [
            {
                title: '',
                detail: '',
                image: ''
            }
        ]
    })


    // For get pervious array item 
    const perviousArrayItem = (value) => {
        let perviousItem = value[value.length - 1]
        return perviousItem.value
    }


    // For upload file values
    const getUploadFile = (value, idx, fields) => {
        uploadFile.value = value
        fields[idx].value.image = uploadFile.value.filename
    }


    // For get our difference data
    const getOurDifferenceEdit = () => {
        isLoading.value = true
        
        axios.get('/api/our-difference').then(async res => {
            if(res.data.status){
                if(res.data.data.length){
                    formInitialValues.addMultiItem = res.data.data.map(item => ({
                        id: item.id,
                        title: item.title,
                        detail: item.detail,
                        image: item.image,
                        imagePath: item.image_path,
                    }))
                }
                else{
                    formInitialValues.addMultiItem = [{
                        title: '',
                        detail: '',
                        image: ''
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


    // For delete our difference image
    const getDeleteUploadFile = (idx, id, fields) => {
        axios.get(`/api/our-difference-delete-image/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                fields[idx].value.image = ''
                fields[idx].value.imagePath = ''
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For delete our difference item
    const deleteItem = (id) => {
        dialog('Are you sure you want to remove?', confirm).show()

        function confirm(){
            axios.get(`/api/our-difference-delete-record/${id}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    setTimeout(() => {
                        getOurDifferenceEdit()
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

        axios.post('/api/our-difference', {
            differenceData: v.addMultiItem
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    getOurDifferenceEdit()
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getOurDifferenceEdit()
    })


</script>