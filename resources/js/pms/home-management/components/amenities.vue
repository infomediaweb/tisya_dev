<template>
    <div class="d-flex justify-content-center py-5" v-if="isLoading">
        <div class="spinner-border" role="status"></div>
    </div>

    <template v-else>
        <template v-if="amenitiesList.length">
            <Form @submit="onSubmit" v-slot="{ errors }">
                <draggable 
                    :list="amenitiesList"
                    :move="onDragover"
                    @end="onDragend"
                    filter=".form-check-input, label"
                    class="row">
                    <template v-for="(obj, index) in amenitiesList">
                        <!-- <pre>{{ obj }}</pre> -->
                        <div 
                            class="col-12 col-md-6 col-lg-4 col-xxl-3 drag-el"
                            :class="{'active': activeDrag == index}">
                            <div class="form-group">
                                <div class="form-check form-check-lg form-check-box" :class="{'border-primary': obj.isChecked}">
                                    <div class="row gx-3 gy-3">
                                        <div class="col-auto">
                                            <Field 
                                                type="checkbox" 
                                                class="form-check-input"
                                                :name="obj.amenities_name"
                                                :id="obj.id"
                                                :value="true"
                                                :unchecked-value="false"
                                                v-model="obj.isChecked"
                                            />
                                        </div>
                                            
                                        <div class="col">
                                            <img
                                                :src="obj.amenities_image ? obj.amenities_image : placeholder"
                                                :alt="obj.amenities_name"
                                                height="20"
                                            >&nbsp;
                                            <label :for="obj.id">{{ obj.amenities_name }}</label>
                                        </div>
                                        
                                        <div class="col-12" v-if="false">
                                            <Field 
                                                type="text" 
                                                :name="`amenities_number-${index}`"
                                                class="form-control"
                                                v-model="obj.number"
                                                :disabled="!obj.isChecked"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </draggable>
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
            </Form>
        </template>

        <div class="message text-center" v-else>
            <h6 class="fw-bold">No record found click here to add <router-link class="icon-link icon-link-hover ff-body" :to="{name: 'add-amenities'}">amenities <i class="bi bi-arrow-right"></i></router-link></h6>
        </div>

    </template>

</template>
<script setup>
    import axios from 'axios'
    import placeholder from '@assets/images/no-img.jpg'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { useRoute } from 'vue-router'
    import { ref, toRef, onMounted } from 'vue'
    import { toast } from '@utils/toast'
    import { VueDraggableNext as draggable } from 'vue-draggable-next'

    const route = useRoute()

    const amenitiesList = ref([])
    const activeDrag = ref(null)

    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    const emit = defineEmits(['emitCurrentTab'])
    const props = defineProps({
        data: Object
    })

    const homeEdit = toRef(props, 'data')


    // For active drag
    const onDragover = (e) => {
        activeDrag.value = e.draggedContext.index
    }


    // For remove active class
    const onDragend = (e) => {
        activeDrag.value = null
    }

    
    //For get amenities list
    const getAmenitiesList = () => {
        isLoading.value = true

        axios.get(`/api/get-home-amenities/${route.params.id}`).then(res => {
            if(res.data.status){
                amenitiesList.value = res.data.data

                amenitiesList.value.filter(item => homeEdit.value.home_amenities.some(checkedItem => {
                    if(checkedItem.amenities_id == item.id){
                        item.isChecked = true
                        item.number = checkedItem.amenities_number
                    }
                }))

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isLoading.value = false
        })
    }
    
   
    
    //For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        let amenitiesListFilter = amenitiesList.value.filter(item => item.isChecked).map(item => ({
            amenities_id: item.id,
            amenities_name: item.amenities_name,
            amenities_number: Number(item.number)
        }))

        axios.post('/api/save-home-amenities', {
            amenities: amenitiesListFilter,
            home_id: route.params.id 
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    emit('emitCurrentTab', 'Tags')
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }

    onMounted(() => {
        getAmenitiesList()
    })

</script>
