<template>
    <Form @submit="onSubmit" v-slot="{ errors }" :initial-values="formInitialValues">
        <div class="row">
            <!-- <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label for="">Heading</label>
                    <Field
                        type="text"
                        name="feature_heading"
                        class="form-control"
                        v-model="homeEdit.features_heading"
                    />
                </div>
            </div> -->
            <div class="col-12">
                <FieldArray name="addMultiItem" v-slot="{ fields, push, remove }">
                    <div class="multi-row">
                        <div class="repeat-row" v-for="(obj, index) in fields">
                            <div class="row">
                                <div class="col-12">
                                   <div class="form-group">
                                        <div class="row">
                                            <div class="col">
                                                <div class="small-action-btn">
                                                    <div class="btn btn-icon btn-secondary-light pe-none">{{ index + 1 }}</div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="small-action-btn">
                                                    <!-- For delete from database -->
                                                    <button
                                                        v-if="obj.value.id"
                                                        type="button"
                                                        class="btn btn-icon btn-danger"
                                                        @click="deleteItem(obj.value.id)">
                                                        <i class="icon-bi bi-trash fs-6"></i>
                                                    </button>

                                                    <!-- For delete from frontend -->
                                                    <button
                                                        v-else-if="fields.length > 1"
                                                        type="button"
                                                        class="btn btn-icon btn-danger"
                                                        @click="remove(index)">
                                                        <i class="icon-bi bi-trash fs-6"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Title<span class="text-danger">*</span></label>
                                        <Field
                                            type="text"
                                            :name="`title-${index}`"
                                            class="form-control"
                                            :class="{'border-danger': errors[`title-${index}`]}"
                                            v-model="obj.value.title"
                                            rules="required"
                                        />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Detail<span class="text-danger">*</span></label>
                                        <ckeditor
                                            :name="`detail-${index}`"

                                            :rules="obj.value.detail ? '' : 'required'"
                                            v-model:data="obj.value.detail"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-btn">
                            <div class="row">
                                <div class="col-12">
                                    <button
                                        type="button"
                                        class="btn btn-save btn-secondary"
                                        @click="push({
                                            title: '',
                                            detail: '',
                                        })"
                                        :disabled="!perviousArrayItem(fields).title || !perviousArrayItem(fields).detail">
                                        <i class="bi bi-plus-lg me-2"></i>Add More
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </FieldArray>

            </div>
        </div>
        <div class="row pt-4">
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
</template>
<script setup>
    import ckeditor from '@components/ckeditor.vue'
    import { Form, Field, FieldArray, ErrorMessage } from 'vee-validate'
    import { useRoute } from 'vue-router'
    import { ref, reactive, toRef } from 'vue'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'

    const route = useRoute()

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
                title: '',
                detail: '',
            }
        ]
    })


    // For get feature list
    if(homeEdit.value.home_features?.length && route.params.id){
        formInitialValues.addMultiItem = homeEdit.value.home_features
    }


    // For get pervious array item
    const perviousArrayItem = (value) => {
        let perviousItem = value[value.length - 1]
        return perviousItem.value
    }


    // For Delete feature
    const deleteItem = (id, remove) => {
        dialog('Are you sure you want to remove?', confirm).show()
        function confirm(){
            axios.post('/api/delete-features', {
                id: id,
                home_id: route.params.id
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    setTimeout(() => {
                        emit('emitCurrentTab', 'Features')
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
        axios.post('/api/home-features', {
            features: v.addMultiItem,
            home_id: route.params.id
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                setTimeout(() => {
                    isSubmitLoading.value = false
                    emit('emitCurrentTab', 'Amenities')
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }

</script>
