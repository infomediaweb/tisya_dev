<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Company' : 'Add Company' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'list-company'}" class="btn rounded-pill btn-secondary-light">
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
                        <div class="col-12 col-lg-10 col-xl-9">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">State<span class="text-danger">*</span></label>
                                        <Field 
                                            class="form-control" 
                                            :class="{'border-danger': errors.state}"
                                            name="state"
                                            as="select"
                                            rules="required"
                                            v-model="vSateId">
                                            <option value="" selected disabled>Select State</option>
                                            <option
                                                v-for="(obj, index) in state"
                                                :value="obj.id">
                                                {{ obj.name }}
                                            </option>
                                        </Field>
                                    </div>
                                </div> 

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="company_name"
                                            :class="{'border-danger': errors.company_name}"
                                            class="form-control" 
                                            rules="required"
                                            v-model="companyEditData.company_name"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="">GST No<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="gst_no"
                                            :class="{'border-danger': errors.gst_no}"
                                            class="form-control" 
                                            maxlength="15"
                                            minlength="15"
                                            rules="required|min:15|max:15"
                                            v-model="companyEditData.gst_no"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="">CIN No<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="cin_no"
                                            :class="{'border-danger': errors.cin_no}"
                                            class="form-control" 
                                            maxlength="21"
                                            minlength="21"
                                            rules="required|min:21|max:21"
                                            v-model="companyEditData.cin_no"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Phone No</label>
                                        <Field 
                                            type="text" 
                                            name="company_phone"
                                            class="form-control" 
                                            :class="{'border-danger': errors.company_phone}"
                                            v-model="companyEditData.company_phone"
                                            rules="numeric"
                                        />
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Address<span class="text-danger">*</span></label>
                                        <Field 
                                            as="textarea"
                                            name="company_address"
                                            :class="{'border-danger': errors.company_address}"
                                            class="form-control" 
                                            rows="8"
                                            rules="required"
                                            v-model="companyEditData.company_address"
                                        />
                                    </div>
                                </div>
                                
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Email<span class="text-danger">*</span></label>
                                        <Field 
                                            type="email"
                                            name="company_email"
                                            :class="{'border-danger': errors.company_email}"
                                            class="form-control" 
                                            rules="required|email"
                                            v-model="companyEditData.company_email"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Website</label>
                                        <Field 
                                            type="text"
                                            name="company_website"
                                            class="form-control" 
                                            v-model="companyEditData.company_website"
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
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'

    const route = useRoute()
    const router = useRouter()
    const state = ref([])
    const vSateId = ref(null)
    const companyEditData = ref({})
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)
    
    
    // For get state edit 
    const getState = () => {
        axios.get('/api/states').then(res => {
            if(res.data.status){
                state.value = res.data.data
            }
        }).catch(error => {
            console.log(error);
        })
    }

    // For location edit 
    const dataEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/company/${route.params.id}`).then(res => {
                if(res.data.status){
                    companyEditData.value = res.data.data
                    vSateId.value = companyEditData.value.state_id

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

        if(route.params.id){
            submitApiUrl.value = `/api/company/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/company'
        }
        
        axios.post(submitApiUrl.value, {
            state_id: vSateId.value,
            company_name: v.company_name,
            company_address: v.company_address,
            gst_no: v.gst_no,
            cin_no: v.cin_no,
            company_phone: v.company_phone,
            company_email: v.company_email,
            company_website: v.company_website,
            
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                setTimeout(() => {
                    isSubmitLoading.value = false
                    router.push({name: 'list-company'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
 
    }

    onMounted(() => {
        getState()
        dataEdit()
    })


</script>