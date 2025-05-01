<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">GST Setting</h1>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <div class="form-check form-check-lg form-check-box border-0 p-0">
                            <div class="row gx-2">
                                <div class="col-auto">
                                    <Field
                                        type="checkbox"
                                        name="cbk-gst"
                                        id="cbk-gst"
                                        class="form-check-input"
                                        v-model="vCheck"
                                        @change="onCheck"
                                        :value="1"
                                        :unchecked-value="0"
                                    />
                                </div>
                                <div class="col">
                                    <label for="cbk-gst">Display Price Including GST</label>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <div class="input-group">
                                <input
                                    class="form-check-input form-check-lg mt-0"
                                    type="checkbox"
                                    v-model="vCheck"
                                    @change="onCheck"
                                    true-value="1"
                                    false-value="0"
                                ><span>&nbsp;&nbsp;&nbsp;Display Price Including GST</span>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script setup>
    import axios from 'axios'
    import { ref, onMounted } from 'vue'
    import { Form, Field } from 'vee-validate'
    import { toast } from '@utils/toast'

    const vCheck = ref(0)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const getSiteSettings = () => {
        isLoading.value = true
        axios.get('/api/setting').then(res => {
            if(res.data.status){
                vCheck.value = res.data.data[0]?.is_allow_gst;
                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            console.log(error)
        })
    }

    const onCheck = () => {
        isSubmitLoading.value = true
        axios.post('/api/update-gst-allow-setting', {

        }).then(res => {
            if(res.data.status){
                setTimeout(() => {
                    isSubmitLoading.value = false
                    toast(res.data.message, 'success').show()
                    //getSiteSettings()
                }, 400)
            }
        }).catch(error => {
            isSubmitLoading.value = false
        })
    }

    onMounted(() => {
        getSiteSettings()
    })

</script>
