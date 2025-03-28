<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify User' : 'Add User' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'list-user'}" class="btn rounded-pill btn-secondary-light">
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
                                        <label for="">Role<span class="text-danger">*</span></label>
                                        <Field
                                            class="form-control"
                                            :class="{'border-danger': errors.user_role}"
                                            name="user_role"
                                            as="select"
                                            rules="required"
                                            v-model="userEditData.role" @change="showProperty($event)">
                                            <option value="" selected disabled>Select Role</option>
                                            <option value="Admin">Admin</option>
                                            <option value="Front Office">Front Office</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Owner">Owner</option>
                                            <option value="Reservations">Reservations</option>
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12" v-if="isOwner">
                                    <div class="form-group">
                                        <label for="">Property <small>(Multiple Selection)</small></label>
                                        <Field
                                            as="div"
                                            name="property_name"
                                            class="dropdown form-multiselect">
                                            <MultiSelect
                                                :items="propertyList"
                                                item-name="home_name"
                                                item-id="id"
                                                name="ckb-pn"
                                                placeholder="Select Property"
                                                v-model="propertyId"
                                            />
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Name<span class="text-danger">*</span></label>
                                        <Field
                                            type="text"
                                            name="user_name"
                                            :class="{'border-danger': errors.user_name}"
                                            class="form-control"
                                            rules="required"
                                            v-model="userEditData.name"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Email<span class="text-danger">*</span></label>
                                        <Field
                                            type="text"
                                            name="user_email"
                                            :class="{'border-danger': errors.user_email}"
                                            class="form-control"
                                            rules="required|email"
                                            v-model="userEditData.email"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Mobile<span class="text-danger">*</span></label>
                                        <Field
                                            type="number"
                                            name="user_mobile_no"
                                            :class="{'border-danger': errors.user_mobile_no}"
                                            class="form-control"
                                            rules="required|numeric"
                                            v-model="userEditData.mobile_no"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Password<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <Field
                                                :type="passwordType"
                                                name="user_password"
                                                :class="{'border-danger': errors.user_password}"
                                                class="form-control"
                                                rules="required"
                                                v-model="userEditData.pass"
                                            />

                                            <button 
                                                type="button" 
                                                class="input-group-text" 
                                                @click="() => passwordType = passwordType === 'password' ? 'text' : 'password'">
                                                <i 
                                                        :class="['fs-5', {
                                                            'bi bi-eye-fill': passwordType === 'password', 
                                                            'bi bi-eye-slash-fill': passwordType === 'text'
                                                        }]">
                                                </i>
                                            </button>

                                        </div>
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
    import { ref, onMounted, defineAsyncComponent } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { format, standardFormat } from '@utils/common'
    import { toast } from '@utils/toast'
    const MultiSelect = defineAsyncComponent(() => import('@components/multi-select.vue'))
    const route = useRoute()
    const router = useRouter()

    const userEditData = ref({})
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)
    const propertyList = ref([])
    const propertyId = ref([])
    const isOwner =  ref(false)
    const passwordType = ref('password')
    

    const showProperty = (e) =>{
        if(e.target.value == 'Owner'){
            isOwner.value = true
        }
        else{
            isOwner.value = false
        }
    }

    // For location edit
    const userEdit = () => {
        isLoading.value = true
        axios.get(`/api/user-detail/${route.params.id}`).then(res => {
            if(res.data.status){
                if(res.data.data){
                    if(res.data.data.role == 'Owner'){
                        isOwner.value = true
                    }
                    userEditData.value = res.data.data
                }
                propertyList.value = res.data.propertyList
                propertyList.value.forEach(item=>{
                    if(item.user_id){
                        propertyId.value.push(item.id)
                    }
                })
                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isLoading.value = false
            console.log(error);
        })
    }

    // For form on submit
    const onSubmit = (v, {resetForm}) => {
        isSubmitLoading.value = true
        submitApiUrl.value = route.params.id ? `/api/save-user-detail/${route.params.id}` : '/api/save-user-detail'
        axios.post(submitApiUrl.value, {
            role: v.user_role,
            name: v.user_name,
            email: v.user_email,
            mobile_no: v.user_mobile_no,
            password: v.user_password,
            mappedProperties: propertyId.value
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                setTimeout(() => {
                    isSubmitLoading.value = false
                    router.push({name: 'list-user'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })

    }

    onMounted(() => {
        userEdit()
    })


</script>
