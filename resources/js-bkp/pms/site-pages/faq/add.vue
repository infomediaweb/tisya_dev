<template>
     <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Faq' : 'Add Faq' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'list-faqs' }" class="btn rounded-pill btn-secondary-light">
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
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Question<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="question"
                                            class="form-control" 
                                            :class="{'border-danger': errors.question}"
                                            v-model="faqEditData.question"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Answer<span class="text-danger">*</span></label>
                                        <Field 
                                            as="textarea"
                                            name="answer"
                                            class="form-control" 
                                            rows="8"
                                            :class="{'border-danger': errors.answer}"
                                            v-model="faqEditData.answer"
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
    import { ref, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { toast } from '@utils/toast'

    const router = useRouter()
    const route = useRoute()

    const faqEditData = ref([])
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


    // For faq edit 
    const faqEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/faqs/${route.params.id}`).then(res => {
                if(res.data.status){
                    faqEditData.value = res.data.data

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
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/faqs/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/faqs'
        }

        axios.post(submitApiUrl.value, v).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false 
                    router.push({name: 'list-faqs'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        faqEdit()
    })

</script>