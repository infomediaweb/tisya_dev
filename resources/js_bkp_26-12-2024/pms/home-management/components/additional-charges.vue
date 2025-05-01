<template>
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
                                                <th class="text-secondary fw-semibold">Name</th>
                                                <th class="text-secondary fw-semibold">Price</th>
                                                <!-- <th class="text-secondary fw-semibold">GST</th> -->
                                                <th style="width:90px;" class="text-secondary text-center fw-semibold">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(obj, index) in fields" :key="index">
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <Field   
                                                            :name="`name${index}`"
                                                            type="text"
                                                            class="form-control"
                                                            :class="{'border-danger': errors[`name${index}`]}"
                                                            v-model="obj.value.name"
                                                            rules="required"
                                                        />
                                                    </div>
                                                    
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <Field   
                                                            :name="`price${index}`"
                                                            type="number"
                                                            class="form-control"
                                                            :class="{'border-danger': errors[`price${index}`]}"
                                                            v-model="obj.value.price"
                                                            rules="required"
                                                        />
                                                    </div>    
                                                </td>
                                                <!-- <td>
                                                    <div class="form-group mb-0">
                                                        <div class="input-group">
                                                            <Field   
                                                                :name="`gst${index}`"
                                                                type="number"
                                                                class="form-control"
                                                                :class="{'border-danger': errors[`gst${index}`]}"
                                                                v-model="obj.value.gst"
                                                                rules="required"
                                                            />
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </td> -->
                                                <td>
                                                    <ul class="action-btn-group justify-content-center mb-0 mw-0">
                                                        <li>
                                                            <button 
                                                                v-if="obj.value.id"
                                                                type="button"
                                                                @click="deleteItem(obj.value.id)" 
                                                                class="btn p-1 fs-5 text-black">
                                                                <i class="icon-bi bi-trash"></i>
                                                            </button>

                                                            <button 
                                                                v-else
                                                                type="button"
                                                                @click="remove(index)" 
                                                                class="btn p-1 fs-5 text-black">
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
                                                    name: '', 
                                                    price: '', 
                                                    id: ''
                                                })"
                                                :disabled="!perviousArrayItem(fields).name || !perviousArrayItem(fields).price">
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
</template>
<script setup>
    import { Form, Field, FieldArray, ErrorMessage } from 'vee-validate'
    import { useRoute } from 'vue-router'
    import { ref, reactive, toRef } from 'vue'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'

    const route = useRoute()

    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const emit = defineEmits(['emitCurrentTab'])
    const props = defineProps({
        data: Object
    })

    const homeEdit = toRef(props, 'data')


    // For initial value of addMultiItem
    const formInitialValues = reactive({
        addMultiItem: [
            {
                name: '',
                price: '',
                id: ''
            }
        ]
    })


    // For get additional charges
    if(homeEdit.value.additional_charge?.length && route.params.id){
        formInitialValues.addMultiItem = homeEdit.value.additional_charge
    }
    
    
    // For get pervious array item 
    const perviousArrayItem = (value) => {
        let perviousItem = value[value.length - 1]
        return perviousItem.value
    }


    // For Delete item
    const deleteItem = (id, remove) => {
        dialog('Are you sure you want to remove?', confirm).show()

        function confirm(){
            axios.delete(`/api/delete-additional-charge/${id}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    setTimeout(() => {
                        emit('emitCurrentTab', 'Additional Charges')
                    }, 400)
                }
            }).catch(error => {
                toast(error.response.data.message, 'error').show()
            })
        }
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        axios.post('/api/save-additional-charges', {
            additional_charges: v.addMultiItem,
            home_id: route.params.id 
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    emit('emitCurrentTab', 'Gallery')
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }

</script>
