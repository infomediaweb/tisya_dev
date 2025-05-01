<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Owner' : 'Add Owner' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'list-owner'}" class="btn rounded-pill btn-secondary-light">
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
                        <div class="col-12 col-lg-10 col-xl-6">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="owner_name"
                                            :class="{'border-danger': errors.owner_name}"
                                            class="form-control"
                                            rules="required"
                                            v-model="ownerEditData.name"
                                        />
                                    </div>
                                </div> 

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Mobile<span class="text-danger">*</span></label>
                                        <Field 
                                            type="number" 
                                            name="owner_mobile_no"
                                            :class="{'border-danger': errors.owner_mobile_no}"
                                            class="form-control"
                                            rules="required|numeric|min:10|max:10"
                                            v-model="ownerEditData.mobile_no"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Email<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="owner_email"
                                            :class="{'border-danger': errors.owner_email}"
                                            class="form-control"
                                            rules="required|email"
                                            v-model="ownerEditData.email"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Username<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="owner_user_name"
                                            :class="{'border-danger': errors.owner_user_name}"
                                            class="form-control"
                                            rules="required"
                                            v-model="ownerEditData.user_name"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Password<span class="text-danger">*</span></label>
                                        <Field 
                                            type="password" 
                                            name="owner_password"
                                            :class="{'border-danger': errors.owner_password}"
                                            class="form-control"
                                            rules="required"
                                            v-model="ownerEditData.password"
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
    import { ref, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { Form, Field } from 'vee-validate'
    import { toast } from '@utils/toast'

    const route = useRoute()
    const router = useRouter()

    const ownerEditData = ref({})
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)
    
    
    // For owner edit 
    const ownerEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/owner-detail/${route.params.id}`).then(res => {
                if(res.data.status){
                    ownerEditData.value = res.data.data

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

    // For form on submit
    const onSubmit = (v, {resetForm}) => {
        isSubmitLoading.value = true

        submitApiUrl.value = route.params.id ? `/api/save-owner-detail/${route.params.id}` : '/api/save-owner-detail'
        
        axios.post(submitApiUrl.value, {
            name: v.owner_name,
            mobile_no: v.owner_mobile_no,
            email: v.owner_email,
            user_name: v.owner_user_name,
            password: v.owner_password
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    router.push({name: 'list-owner'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
 
    }

    onMounted(() => {
        ownerEdit()
    })


</script>