<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Cancellation & Refund Policy</h1>
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
                                <label for="">Policy Content<span class="text-danger">*</span></label>
                                <ckeditor
                                    name="cancellation_and_refund_policy"
                                    rule="required"
                                    v-model:data="cancellation_and_refund_policy"
                                />
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
    import ckeditor from '@components/ckeditor.vue'

    const props = defineProps({
        data: Object
    })
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const cancellation_and_refund_policy = ref('')


    const getEmails = () => {
        isLoading.value = true

        axios.get('/api/setting').then(res => {
            if(res.data.status){
                cancellation_and_refund_policy.value = res.data.data[0]?.cancellation_and_refund_policy

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
        console.log('cancellation_and_refund_policy.value', cancellation_and_refund_policy.value)
        axios.post('/api/update-cancellation-and-refund-policy', {
            cancellation_and_refund_policy: cancellation_and_refund_policy.value
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
