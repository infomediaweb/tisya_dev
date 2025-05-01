<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">GST</h1>
                </div>
            </div>
        </div>

        <section class="section"> 
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <template v-else>
                <div class="outer-wrapper">
                    <Form @submit="onSubmit" v-slot="{errors}" :initial-values="formInitialValues">
                        <div class="table-wrap">
                            <FieldArray name="addMultiItem" v-slot="{ fields, push, remove }"> 
                                <div class="table-responsive">
                                    <table class="table align-middle table-list mb-0 mw-lg">
                                        <thead>
                                            <tr>
                                                <th class="text-secondary fw-semibold">Start Tariff / Night</th>
                                                <th class="text-secondary fw-semibold">Upto Tariff / Night</th>
                                                <th class="text-secondary fw-semibold">Percentage %</th>
                                                <th style="width:90px;" class="text-secondary text-center fw-semibold">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(obj, index) in fields" :key="index" :class="{'disabled': index != (fields.length - 1)}">
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <Field   
                                                            :name="`slabs_start${index}`"
                                                            type="text"
                                                            class="form-control"
                                                            :class="{'border-danger': errors[`slabs_start${index}`]}"
                                                            v-model="obj.value.slabs_start"
                                                            readonly
                                                            rules="required"
                                                        />
                                                    </div>
                                                    
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <Field   
                                                            :name="`slabs_upto${index}`"
                                                            type="text"
                                                            class="form-control"
                                                            :class="{'border-danger': errors[`slabs_upto${index}`]}"
                                                            v-model="obj.value.slabs_upto"
                                                            :rules="{required: true, numeric: true, min_value: obj.value.slabs_start + 1}"
                                                            data-bs-toggle="tooltip"
                                                        />
                                                        <Tooltip 
                                                            :error="errors[`slabs_upto${index}`]" 
                                                            :message="'Value should be greater than Start Tariff / Night'" 
                                                        />
                                                    </div>    
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <div class="input-group">
                                                            <Field   
                                                                :name="`gst_percentage${index}`"
                                                                type="text"
                                                                class="form-control"
                                                                :class="{'border-danger': errors[`gst_percentage${index}`]}"
                                                                v-model="obj.value.gst_percentage"
                                                                rules="required|numeric"
                                                            />
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <ul v-if="index && index == (fields.length - 1)" class="action-btn-group justify-content-center mb-0 mw-0">
                                                        <li>
                                                            <button @click="remove(index)" class="btn p-1 fs-5 text-black">
                                                                <i class="icon-bi bi-trash"></i>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> 

                                <div class="table-footer pt-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <button 
                                                type="button"
                                                class="btn btn-save btn-secondary"
                                                @click="push({
                                                    slabs_start: parseInt(perviousArrayItem(fields).slabs_upto) + 1, 
                                                    slabs_upto: '', 
                                                    gst_percentage: ''
                                                })"
                                                :disabled="!perviousArrayItem(fields).slabs_upto || !perviousArrayItem(fields).gst_percentage || parseInt(perviousArrayItem(fields).slabs_upto) <= parseInt(perviousArrayItem(fields).slabs_start)">
                                                <i class="bi bi-plus-lg me-2"></i>Add More
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </FieldArray>
                        </div>
                        
                        <div class="table-footer pt-4">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <button class="btn btn-save btn-primary">
                                        <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                        <span v-else>SUBMIT</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </Form>
                </div>
            </template>
        </section>
    </div>
</template>
<script setup>
    import Tooltip from '@components/tooltip.vue'
    import axios from 'axios'
    import { Form, Field, FieldArray, ErrorMessage } from 'vee-validate';
    import { ref, onMounted, reactive } from 'vue'
    import { toast } from '@utils/toast'

    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    // For initial value of addMultiItem
    const formInitialValues = reactive({
        addMultiItem: [
            {
                slabs_start: 1,
                slabs_upto: '',
                gst_percentage: ''
            }
        ]
    })

    const perviousArrayItem = (value) => {
        let perviousItem = value[value.length - 1]
        return perviousItem.value
    }

    
    // For get GST Slabs list
    const getGstSlabs = () => {
        isLoading.value = true

        axios.get('/api/gst-slabs').then(res => {
            if(res.data.status){
                if(res.data.data.length){
                    formInitialValues.addMultiItem = res.data.data

                    setTimeout(() => {
                        isLoading.value = false
                    }, 400)
                }
                else{
                    isLoading.value = false
                }  
            }
        }).catch(error => {
            isLoading.value = false
            console.log(error)
        })
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        axios.post('/api/gst-slabs', {
            addMultiItem: v.addMultiItem
        }).then(res => {
            if(res.data.status){
                setTimeout(() => {
                    isSubmitLoading.value = false
                    toast(res.data.message, 'success').show()
                    getGstSlabs()
                }, 400)
            }
        }).catch(error => {
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getGstSlabs()
    })

</script>