<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Terms and Condition</h1>
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
                                        <label for="">Title</label>
                                        <Field 
                                            type="text"
                                            name="title_text"
                                            class="form-control"
                                            :class="{'border-danger': errors.title_text}"
                                            v-model="aboutEdit.title_text"
                                            rules="required"
                                        />
                                    </div> 
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="form-group">
                                        <label for="">Terms and Conditions</label>
                                        <ckeditor
                                            name="terms_and_condition" 
                                            :class="{'border-danger': errors.terms_and_condition}"
                                            v-model:data="aboutEdit.terms_and_condition"
                                            rules="required"
                                        />
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
        axios.get('/api/terms-and-condition').then(res => {
            if(res.data.status){
                aboutEdit.value = res.data.data
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
        const terms_id = aboutEdit.value.id 
        if(terms_id){
            var submitApiUrl = `/api/terms-and-condition/${terms_id}`
            }
        else{
        var submitApiUrl = '/api/terms-and-condition'
        } 
        axios.post(submitApiUrl, {
            title_text: v.title_text,
            terms_and_condition: v.terms_and_condition,
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
