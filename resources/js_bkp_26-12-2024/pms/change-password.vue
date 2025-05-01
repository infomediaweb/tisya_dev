<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Change Password</h1>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="page-content">
                <Form @submit="onSubmit" v-slot="{ errors }">
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="">New Password<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <Field 
                                        type="password" 
                                        name="password"
                                        class="form-control"
                                        :class="{'border-danger': errors.password}" 
                                        placeholder="New Password"
                                        rules="required|min:5|max:10"
                                        data-bs-toggle="tooltip"
                                    />
                                    
                                    <button type="button" class="input-group-text" @click="showPasswordToggle">
                                        <i class="bi bi-eye-fill fs-5"></i>
                                    </button>
                                </div>

                                <Tooltip :error="errors.password" />

                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label for="">Confirm Password<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <Field 
                                        type="password" 
                                        name="confirmation"
                                        class="form-control" 
                                        :class="{'border-danger': errors.confirmation}" 
                                        placeholder="Confirm Password"
                                        ref="passwordRef"
                                        rules="required|min:5|max:10|confirmed:@password"
                                        data-bs-toggle="tooltip"
                                    /> 
                                
                                    <button type="button" class="input-group-text" @click="showPasswordToggle">
                                        <i class="bi bi-eye-fill fs-5"></i>
                                    </button>
                                </div>

                                <Tooltip :error="errors.confirmation" />
                                
                            </div>
                        </div>

                    </div>
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <button class="btn btn-save btn-primary">
                                    <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></div>
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
    import Tooltip from '@components/tooltip.vue'
    import axios from 'axios'
    import $ from 'jquery'
    import { ref } from 'vue'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'

    const isLoading = ref(false)

    const showPasswordToggle = ({target}) => {
        let inputEl = $(target).parent().find('input')
        let iconEl = $(target).find('i')
        if(inputEl.attr('type') === 'password'){
            inputEl.attr('type', 'text')
            iconEl.attr('class', 'bi bi-eye-slash-fill fs-5')
        }
        else{
            inputEl.attr('type', 'password')
            iconEl.attr('class', 'bi bi-eye-fill fs-5')
        }
    }

    const onSubmit = (v, { resetForm }) => {
        isLoading.value = true

        axios.post('/api/pms-changepassword', {
            newpass: v.password
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                isLoading.value = false
                resetForm()
            }
        }).catch(error => {
            isLoading.value = false
        })
    }

</script>