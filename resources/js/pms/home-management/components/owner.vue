<template>
    <Form @submit="onSubmit" v-slot="{ errors }">
        <div class="row g-5">
            <div class="col-12 col-xxl-6">

                <div class="d-flex justify-content-center py-5" v-if="isLoading">
                    <div class="spinner-border" role="status"></div>
                </div>

                <template v-else> 
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Name<span class="text-danger">*</span></label>
                                <Field 
                                    type="text" 
                                    name="name"
                                    :class="{'border-danger': errors.name}"
                                    class="form-control"
                                    rules="required"
                                    v-model="ownerDetail.name"
                                />
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="">Mobile<span class="text-danger">*</span></label>
                                <Field 
                                    type="number" 
                                    name="mobile_no"
                                    :class="{'border-danger': errors.mobile_no}"
                                    class="form-control"
                                    rules="required|numeric|min:10|max:10"
                                    v-model="ownerDetail.mobile_no"
                                />
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="">Email<span class="text-danger">*</span></label>
                                <Field 
                                    type="text" 
                                    name="email"
                                    :class="{'border-danger': errors.email}"
                                    class="form-control"
                                    rules="required|email"
                                    v-model="ownerDetail.email"
                                />
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="">Username<span class="text-danger">*</span></label>
                                <Field 
                                    type="text" 
                                    name="user_name"
                                    :class="{'border-danger': errors.user_name}"
                                    class="form-control"
                                    rules="required"
                                    v-model="ownerDetail.user_name"
                                />
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label for="">Password<span class="text-danger">*</span></label>
                                <div class="input-group">
                                <Field 
                                    :type="passwordType"
                                    name="password"
                                    :class="{'border-danger': errors.password}"
                                    class="form-control"
                                    rules="required"
                                    v-model="ownerDetail.password"
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

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-save btn-primary">
                                    <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                    <span v-else>SUBMIT</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </Form>
</template>
<script setup>
    import axios from 'axios'
    import { Form, Field } from 'vee-validate'
    import { useRoute } from 'vue-router'
    import { ref, toRef } from 'vue'
    import { toast } from '@utils/toast'

    const route = useRoute()

    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const emit = defineEmits(['emitCurrentTab'])
    const props = defineProps({
        data: Object
    })

    const homeEdit = toRef(props, 'data')
    const ownerDetail = ref({})
    const passwordType = ref('password')
    if(homeEdit.value.owner_detail){
        ownerDetail.value = homeEdit.value.owner_detail
    }
   
    // For form on submit
    const onSubmit = (v, { resetForm }) => {
        isSubmitLoading.value = true

        v.id = homeEdit.value.owner_detail ? homeEdit.value.owner_detail.id : ''
        v.home_id = route.params.id

        axios.post('/api/save-owner-detail', v).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    emit('emitCurrentTab', 'Owner Details')
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
 
    }
    

</script>