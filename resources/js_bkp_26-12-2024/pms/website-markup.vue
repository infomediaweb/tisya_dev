<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Website Markup</h1>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form @submit="onSubmit" v-slot="{ errors }">
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <label>Markup %</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <Field
                                        type="text"
                                        name="website_markup"
                                        class="form-control"
                                        :class="{'border-danger': errors.website_markup}"
                                        v-model="vWebsiteMarkup"
                                    />
                                    <span class="input-group-text">%</span>
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
    import { ref, onMounted } from 'vue'
    import { Form, Field } from 'vee-validate'
    import { toast } from '@utils/toast'

    const vWebsiteMarkup = ref()
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const getWebsiteMarkup = () => {
        isLoading.value = true

        axios.get('/api/setting').then(res => {
            if(res.data.status){
                vWebsiteMarkup.value = res.data.data[0]?.website_markup
                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            console.log(error)
        })
    }

    const onSubmit = (v) => {
        isSubmitLoading.value = true

        axios.post('/api/set-website-markup', {
            website_markup: vWebsiteMarkup.value
        }).then(res => {
            if(res.data.status){
                setTimeout(() => {
                    isSubmitLoading.value = false
                    toast(res.data.message, 'success').show()
                    getWebsiteMarkup()
                }, 400)
            }
        }).catch(error => {
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getWebsiteMarkup()
    })

</script>
