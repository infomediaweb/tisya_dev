<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Checkin & Checkout Time</h1>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form @keypress.enter.prevent @submit="onSubmit" v-slot="{ errors, resetForm }">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <div class="form-group">
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Checkin Time<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <Field
                                                type="text"
                                                name="checkin_time"
                                                v-model="checkin_time"
                                                class="form-control"
                                                :class="{'border-danger': errors.checkin_time}"
                                                placeholder="00:00"
                                                rules="required"
                                                max="24"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Checkout Time<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <Field
                                                type="text"
                                                name="checkout_time"
                                                v-model="checkout_time"
                                                class="form-control"
                                                :class="{'border-danger': errors.checkout_time}"
                                                placeholder="00:00"
                                                rules="required"
                                                max="24"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-save btn-primary">
                                <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                <span v-else>SUBMIT</span>
                            </button>
                        </div>
                    </div>
                </Form>
            </div>
        </section>
    </div>
</template>
<script setup>
    import axios from 'axios'
    import { ref, onMounted, nextTick, reactive, toRef  } from 'vue'
    import { Form, Field, FieldArray, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'
    import dayjs from 'dayjs'

    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const checkin_time = ref('')
    const checkout_time = ref('')
    const vTime = ref('')

    const getEmails = () => {
        isLoading.value = true

        axios.get('/api/setting').then(res => {
            if(res.data.status){
                checkin_time.value = res.data.data[0]?.checkin_time
                checkout_time.value = res.data.data[0]?.checkout_time


                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isLoading.value = false
        })
    }

    const onSubmit = () => {
        isSubmitLoading.value = true
        axios.post('/api/update-checkin-checkout-time', {
            checkin_time: checkin_time.value,
            checkout_time: checkout_time.value
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getEmails()
    })

</script>
