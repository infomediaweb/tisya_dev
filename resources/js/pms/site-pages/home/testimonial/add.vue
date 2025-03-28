<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Testimonial' : 'Add Testimonial' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'list-testimonial' }" class="btn rounded-pill btn-secondary-light">
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

            <div class="outer-wrapper" v-else>
                <Form @submit="onSubmit" v-slot="{errors}">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Guest Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="guest_name"
                                            class="form-control" 
                                            :class="{'border-danger': errors.guest_name}"
                                            v-model="testimonialEditData.guest_name"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Home Name<span class="text-danger">*</span></label>
                                        <Field 
                                            as="select"
                                            name="home_name"
                                            class="form-control" 
                                            :class="{'border-danger': errors.home_name}"
                                            v-model="homeId"
                                            rules="required">
                                            <option value="" disabled selected>Select Home Name</option>
                                            <option 
                                                v-for="(obj, index) in homeList"
                                                :key="index"
                                                :value="obj.id">
                                                {{ obj.home_name }}
                                            </option>
                                        </Field>
                                    </div>
                                </div> 
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Date<span class="text-danger">*</span></label>
                                        <Field name="date" v-model="date" rules="required" v-slot="{ field }">
                                            <DatePicker 
                                                v-bind="field"
                                                v-model="date" 
                                                :format="format" 
                                                :enable-time-picker="false" 
                                                :max-date="new Date()"
                                                :input-class-name="`form-control ${errors.date ? 'border-danger' : ''}`"
                                                prevent-min-max-navigation
                                                auto-apply 
                                            />
                                        </Field>
                                    </div>
                                </div> 
                                <div class="col-12 col-md-6">
                                    <div class="form-group ckeditor-form">
                                        <label for="">Guest Comments<span class="text-danger">*</span></label>
                                        <ckeditor
                                            name="headline" 
                                            :rules="testimonialEditData.headline ? '' : 'required'"
                                            v-model:data="testimonialEditData.headline" 
                                        />
                                    </div> 
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="row gx-4">
                                            <div class="col-auto" v-for="(obj, index) in radioInputType">
                                                <div class="form-check custom-check">
                                                    <input 
                                                        type="radio" 
                                                        name="image-video" 
                                                        class="form-check-input"
                                                        :id="obj.value"
                                                        :value="obj.value"
                                                        v-model="pickedRadioValue" 
                                                        @change="onPickedRadio($event, obj.value)"
                                                    />
                                                    <label :for="obj.value">{{ obj.name }}<span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                        </div>


                                        <template v-if="pickedRadioValue == 'image'">
                                            <UploadFile 
                                                v-if="renderComponent"
                                                name="image"
                                                id="image"
                                                fileType="image"
                                                acceptType="image/*"
                                                info="Max size: 1mb | Image size: 1280px . 720px"
                                                changeText="To change the image please click / drag new image here!"
                                                size="1"
                                                ratio="40%"
                                                v-bind="uploadFileProps"
                                                :rules="uploadFileProps.fileName ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'"
                                                @emitUploadFile="getUploadFile"
                                                @emitDeleteUploadFile="deleteUploadFile"
                                            />
                                        </template>

                                        <template v-else>
                                            <UploadFile 
                                                v-if="renderComponent"
                                                name="video"
                                                id="video"
                                                fileType="video"
                                                acceptType="video/mp4"
                                                info="Max size: 100mb"
                                                changeText="To change the video please click / drag new video here!"
                                                size="100"
                                                ratio="40%"
                                                :rules="uploadFileProps.fileName ? 'size:104800|ext:mp4' : 'required|size:104800|ext:mp4'"
                                                v-bind="uploadFileProps"
                                                @emitUploadFile="getUploadFile"
                                                @emitDeleteUploadFile="deleteUploadFile"
                                            />
                                        </template>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-2">
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
    import dayjs from 'dayjs'
    import { Form, Field } from 'vee-validate'
    import { ref, reactive, onMounted, defineAsyncComponent, nextTick } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { format } from '@utils/common'
    import { toast } from '@utils/toast'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const router = useRouter()
    const route = useRoute()

    const date = ref()
    const pickedRadioValue = ref('image')

    const uploadFile = ref({})
    const homeList = ref([])
    const homeId = ref()
    const testimonialEditData = ref([])
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const renderComponent = ref(true)


    // For input type list
    const radioInputType = ref([
        { 
            value: 'image' ,
            name: 'Image'
        },
        { 
            value: 'video',
            name: 'Video' 
        }
    ])


    // For upload file props 
    const uploadFileProps = reactive({
        filePath: '',
        fileName: '', 
    })


    // For upload file values
    const getUploadFile = (value) => {
        uploadFile.value = value
    }


    // For on change radio input is video or image
    const onPickedRadio = (e, value = pickedRadioValue.value) => {
        let props = uploadFileProps
        let fileData = testimonialEditData.value
        
        const propsInitValue = async (type) => {
            renderComponent.value = false
            await nextTick()
            renderComponent.value = true

            if(fileData.file_type == type){
                props.filePath = fileData.file_path
                props.fileName = fileData.file
            }
            else{
                props.filePath = ''
                props.fileName = '' 
            } 
        }

        if(value == 'video'){
            propsInitValue(value)
        }
        else if(value == 'image'){
            propsInitValue(value)
        }
    }

    
    // For get homes list
    const getHomesList = () => {
        axios.get(`/api/only-homes`).then(res => {
            if(res.data.status){
                homeList.value = res.data.data

                homeList.value.filter(item => {
                    if(item.home_name == testimonialEditData.value?.home_name){
                        homeId.value = item.id
                    }
                })
            }
        }).catch(error => {
            console.log(error)
        })
    }


    // For testimonial edit 
    const testimonialEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/testimonials/${route.params.id}`).then(res => {
                if(res.data.status){
                    testimonialEditData.value = res.data.data

                    date.value = testimonialEditData.value.date
                    pickedRadioValue.value = testimonialEditData.value.file_type

                    nextTick(() => {
                        onPickedRadio()
                        getHomesList()
                    })

                    setTimeout(() => {
                        isLoading.value = false
                    }, 400)
                    
                }
            }).catch(error => {
                isLoading.value = false
                console.log(error)
            })
        }
        else{
            getHomesList()
        }
    }


    // For delete uploaded file
    const deleteUploadFile = (id) => {
        axios.get(`/api/testimonials-delete-image/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                uploadFileProps.filePath = ''
                uploadFileProps.fileName = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/testimonials/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/testimonials'
        }

        axios.post(submitApiUrl.value, {
            guest_name: v.guest_name,
            headline: v.headline,
            home_name: homeId.value,
            file: uploadFile.value.filename,
            file_type: pickedRadioValue.value,
            date: dayjs(date.value).format('YYYY-MM-DD'),
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false 
                    router.push({name: 'list-testimonial'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        testimonialEdit()
    })

</script>