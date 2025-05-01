<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Team Member' : 'Add Team Member' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'list-teams' }" class="btn rounded-pill btn-secondary-light">
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
                        <div class="col-12 col-lg-10 col-xl-7">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="team_name"
                                            class="form-control" 
                                            :class="{'border-danger': errors.team_name}"
                                            v-model="teamEditData.name"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">Designation<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="team_designation"
                                            class="form-control" 
                                            :class="{'border-danger': errors.team_designation}"
                                            v-model="teamEditData.designation"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Image<span class="text-danger">*</span></label>
                                        <UploadFile 
                                            name="image"
                                            id="image"
                                            fileType="image"
                                            acceptType="image/*"
                                            info="Max size: 1mb | Image size: 300px . 300px"
                                            changeText="To change the image please click / drag new image here!"
                                            size="1"
                                            ratio="40%"
                                            :filePath="teamEditData.image_path"
                                            :fileName="teamEditData.image"
                                            :rules="teamEditData.image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                            @emitUploadFile="getUploadFile"
                                            @emitDeleteUploadFile="deleteUploadFile"
                                        />
                                    </div> 
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Description<span class="text-danger">*</span></label>
                                        <Field 
                                            as="textarea"
                                            name="team_description"
                                            class="form-control" 
                                            rows="8"
                                            :class="{'border-danger': errors.team_description}"
                                            v-model="teamEditData.description"
                                            rules="required"
                                        />
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
    import axios from 'axios'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { ref, onMounted, defineAsyncComponent } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { toast } from '@utils/toast'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const router = useRouter()
    const route = useRoute()

    const uploadFile = ref({})
    const teamEditData = ref([])
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


    // For team edit 
    const teamEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/teams/${route.params.id}`).then(res => {
                if(res.data.status){
                    teamEditData.value = res.data.data

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
        axios.get(`/api/teams-delete-image/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                teamEditData.value.image = ''
                teamEditData.value.image_path = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/teams/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/teams'
        }

        axios.post(submitApiUrl.value, {
            name: v.team_name,
            designation: v.team_designation,
            image: uploadFile.value.filename,
            description: v.team_description
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false 
                    router.push({name: 'list-teams'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        teamEdit()
    })

</script>