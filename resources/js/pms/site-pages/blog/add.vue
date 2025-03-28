<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Blog' : 'Add Blog' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'list-blog' }" class="btn rounded-pill btn-secondary-light">
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
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Image<span class="text-danger">*</span></label>
                                        <UploadFile 
                                            name="image"
                                            id="image"
                                            fileType="image"
                                            acceptType="image/*"
                                            info="Max size: 1mb | Image size: 1280px . 720px"
                                            changeText="To change the image please click / drag new image here!"
                                            size="1"
                                            ratio="40%"
                                            :filePath="blogEditData.image_path"
                                            :fileName="blogEditData.image"
                                            :rules="blogEditData.image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                            @emitUploadFile="getUploadFile"
                                            @emitDeleteUploadFile="deleteUploadFile"
                                        />
                                    </div> 
                                </div>
                                <div class="col-6 ">
                                    <div class="form-group">
                                        <label for="">Title<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="blog_title"
                                            class="form-control" 
                                            :class="{'border-danger': errors.blog_title}"
                                            v-model="blogEditData.title"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-12 col-md-6">
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
                                <div class="col-12">
                                    <div class="form-group ckeditor-big-form">
                                        <label for="">Description<span class="text-danger">*</span></label>
                                        <ckeditor
                                            name="blog_description" 
                                            :rules="blogEditData.blog_description ? '' : 'required'"
                                            v-model:data="blogEditData.description" 
                                        />
                                    </div> 
                                </div> 
                               <!--  <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">Facebook Url</label>
                                        <Field 
                                            type="text" 
                                            name="blog_facebook_url"
                                            class="form-control" 
                                            :class="{'border-danger': errors.blog_facebook_url}"
                                            v-model="blogEditData.facebook_url"
                                            rules="url"
                                            data-bs-toggle="tooltip"
                                        />
                                        <Tooltip :error="errors.blog_facebook_url" />
                                    </div>
                                </div>  -->
                               <!--  <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">Linkedin Url</label>
                                        <Field 
                                            type="text" 
                                            name="blog_linkedin_url"
                                            class="form-control" 
                                            :class="{'border-danger': errors.blog_linkedin_url}"
                                            v-model="blogEditData.linkedin_url"
                                            rules="url"
                                            data-bs-toggle="tooltip"
                                        />
                                        <Tooltip :error="errors.blog_linkedin_url" />
                                    </div>
                                </div>  -->
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
    import Tooltip from '@components/tooltip.vue'
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { ref, onMounted, defineAsyncComponent } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { toast } from '@utils/toast'
    import { format } from '@utils/common'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const router = useRouter()
    const route = useRoute()

    const date = ref()
    const uploadFile = ref({})
    const blogEditData = ref([])
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


    // For blog edit 
    const blogEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/blogs/${route.params.id}`).then(res => {
                if(res.data.status){
                    blogEditData.value = res.data.data
                    date.value = blogEditData.value.date

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


    // For upload file values
    const getUploadFile = (value) => {
        uploadFile.value = value
    }


    // For delete uploaded file
    const deleteUploadFile = (id) => {
        axios.get(`/api/blogs-delete-image/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                blogEditData.value.image = ''
                blogEditData.value.image_path = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/blogs/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/blogs'
        }

        axios.post(submitApiUrl.value, {
            image: uploadFile.value.filename,
            title: v.blog_title,
            description: v.blog_description,
            date: dayjs(date.value).format('YYYY-MM-DD'),
            facebook_url: v.blog_facebook_url,
            linkedin_url: v.blog_linkedin_url,
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false 
                    router.push({name: 'list-blog'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        blogEdit()
    })

</script>