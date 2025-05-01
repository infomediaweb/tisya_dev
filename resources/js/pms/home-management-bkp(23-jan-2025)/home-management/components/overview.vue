<template>
    <Form @submit="onSubmit" v-slot="{ meta, values, errors }">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label for="">Name<span class="text-danger">*</span></label>
                    <Field 
                        type="text" 
                        name="home_name"
                        class="form-control"
                        :class="{'border-danger': errors.home_name}" 
                        v-model="homeEdit.home_name"
                        rules="required"
                    />
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label for="">Ru ID<span class="text-danger">*</span></label>
                    <Field 
                        type="text" 
                        name="ru_property_id"
                        class="form-control"
                        :class="{'border-danger': errors.ru_property_id}" 
                        v-model="homeEdit.ru_property_id"
                        rules="required"
                    />
                </div>
            </div>
  
            <!--Break Column-->
            <!-- <div class="w-100"></div> -->
            <!--Break Column-->

            <div class="col-12 col-lg-4">
                <div class="form-group">
                    <label for="">Category<span class="text-danger">*</span></label>
                    <Field 
                        as="select"
                        name="home_type_id"
                        class="form-control"
                        :class="{'border-danger': errors.home_type_id}"
                        v-model="vHomeTypeId"
                        rules="required">
                        <option value="" selected disabled>Select Category</option>
                        <option 
                            v-for="(obj, index) in homeType" 
                            :key="index"
                            :value="obj.id">
                            {{ obj.name }}
                        </option>
                    </Field>
                </div>
            </div> 
            
            <div class="col-12 col-lg-4">
                <div class="form-group">
                    <label for="">State<span class="text-danger">*</span></label>
                    <Field 
                        as="select"
                        name="state_id"
                        class="form-control"
                        :class="{'border-danger': errors.state_id}"
                        v-model="vStateId"
                        @change="getLocation"
                        rules="required">
                        <option value="" selected disabled>Select State</option>
                        <option 
                            v-for="(obj, index) in state" 
                            :key="index"
                            :value="obj.id">
                            {{ obj.name }}
                        </option>
                    </Field>
                </div>
            </div> 

            <div class="col-12 col-lg-4">
                <div class="form-group">
                    <label for="">Location<span class="text-danger">*</span></label>
                    <Field 
                        as="select"
                        name="location_id"
                        class="form-control"
                        :class="{'border-danger': errors.location_id}"
                        v-model="vLocationId"
                        rules="required">
                        <option value="" selected disabled>Select Location</option>
                        <option 
                            v-for="(obj, index) in location" 
                            :key="index"
                            :value="obj.id">
                            {{ obj.location_name }}
                        </option>
                    </Field>
                </div>
            </div>  
            
            <!-- <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Collection</label>
                    <Field 
                        as="select"
                        name="collection_id"
                        class="form-control"
                        :class="{'border-danger': errors.collection_id}"
                        v-model="vCollectionId"
                        >
                        <option value="" selected >Select Collection</option>
                        <option 
                            v-for="(obj, index) in collection" 
                            :key="index"
                            :value="obj.id">
                            {{ obj.collection_name }}
                        </option>
                    </Field>
                </div>
            </div>   -->

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Collection <small>(Multiple Selection)</small></label>
                    <Field
                        as="div"
                        name="collection_name"
                        class="dropdown form-multiselect">
                        <MultiSelect
                            :items="collectionList"
                            item-name="collection_name"
                            item-id="id"
                            name="ckb-pn"
                            placeholder="Select Collection"
                            v-model="vCollectionId"
                        />
                    </Field>
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Capacity<span class="text-danger">*</span></label>
                    <Field 
                        type="text" 
                        name="guests_included"
                        class="form-control"
                        :class="{'border-danger': errors.guests_included}"
                        v-model="homeEdit.guests_included"
                        rules="required|numeric"
                    />
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Max Occupancy<span class="text-danger">*</span></label>
                    <Field 
                        type="text" 
                        name="maximum_number_of_guests"
                        class="form-control"
                        :class="{'border-danger': errors.maximum_number_of_guests}"
                        :rules="{required: true,  min_value: homeEdit.guests_included}"
                        v-model="homeEdit.maximum_number_of_guests"
                        data-bs-toggle="tooltip"
                    />
                    <Tooltip 
                        :error="errors.maximum_number_of_guests"
                        :message="'Value should be greater than capacity'"  
                    />
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Extra guest charge</label>
                    <Field 
                        type="text" 
                        name="extra_guest_charges"
                        class="form-control"
                        :class="{'border-danger': errors.no_of_staff}"
                        v-model="homeEdit.extra_guest_charges"
                        rules="numeric"
                    />
                </div>
            </div>

            <!-- <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">No of Nights<span class="text-danger">*</span></label>
                    <Field 
                        type="text" 
                        name="home_no_night"
                        class="form-control"
                        :class="{'border-danger': errors.home_no_night}"
                        rules="required"
                    />
                </div>
            </div> -->

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Number of Staff</label>
                    <Field 
                        type="text" 
                        name="no_of_staff"
                        class="form-control"
                        :class="{'border-danger': errors.no_of_staff}"
                        v-model="homeEdit.no_of_staff"
                        rules="numeric"
                    />
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Bedrooms<span class="text-danger">*</span></label>
                    <Field 
                        type="text" 
                        name="no_of_bedrooms"
                        class="form-control"
                        :class="{'border-danger': errors.no_of_bedrooms}"
                        v-model="homeEdit.no_of_bedrooms"
                        rules="required|numeric"
                    />
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Bathrooms<span class="text-danger">*</span></label>
                    <Field 
                        type="text" 
                        name="no_of_bathrooms"
                        class="form-control"
                        :class="{'border-danger': errors.no_of_bathrooms}"
                        v-model="homeEdit.no_of_bathrooms"
                        rules="required|decimal"
                    />
                </div>
            </div>


            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Arrival Time<span class="text-danger">*</span></label>
                    <Field 
                        as="select"
                        name="arrival_time"
                        class="form-control"
                        :class="{'border-danger': errors.arrival_time}"
                        v-model="homeEdit.checkin_time"
                        rules="required">
                        <option value="" selected disabled>Select Arrival Time</option>
                        <option 
                            v-for="(obj, index) in getTimeSlots" 
                            :key="index"
                            :value="obj">
                            {{ obj }}
                        </option>
                    </Field>
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Departure Time<span class="text-danger">*</span></label>
                    <Field 
                        as="select"
                        name="departure_time"
                        class="form-control"
                        :class="{'border-danger': errors.departure_time}"
                        v-model="homeEdit.checkout_time"
                        rules="required">
                        <option value="" selected disabled>Select Departure Time</option>
                        <option 
                            v-for="(obj, index) in getTimeSlots" 
                            :key="index"
                            :value="obj">
                            {{ obj }}
                        </option>
                    </Field>
                </div>
            </div>


            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Map Latitude</label>
                    <Field 
                        type="text" 
                        name="map_latitude"
                        class="form-control"
                        :class="{'border-danger': errors.map_latitude}"
                        v-model="homeEdit.map_latitude"
                    />
                </div>
            </div>

            <div class="col-12 col-lg-3">
                <div class="form-group">
                    <label for="">Map Longitude</label>
                    <Field 
                        type="text" 
                        name="map_longitude"
                        class="form-control"
                        :class="{'border-danger': errors.map_longitude}"
                        v-model="homeEdit.map_longitude"
                    />
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label for="">Google Location URL<span class="text-danger">*</span></label>
                    <Field 
                        type="url" 
                        name="googlelocation_url"
                        class="form-control"
                        :class="{'border-danger': errors.googlelocation_url}"
                        v-model="homeEdit.googlelocation_url"
                        rules="required|url"
                        data-bs-toggle="tooltip"
                    />
                    <Tooltip :error="errors.googlelocation_url" />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="">Location Info</label>
                    <ckeditor
                        name="location_info" 
                        v-model:data="homeEdit.location_info" 
                    />
                </div>
            </div> 
            
            <div class="col-12">
                <div class="form-group">
                    <label for="">Short Description</label>
                    <Field 
                        as="textarea"
                        name="short_description"
                        class="form-control"
                        rows="7" 
                        v-model="homeEdit.short_description"
                    />
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="">Description</label>
                    <ckeditor
                        name="description" 
                        
                        v-model:data="homeEdit.description" 
                    />
                </div>
            </div> 

            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label for="">Directions Short Description<span class="text-danger">*</span></label>
                    <ckeditor
                        name="short_direction" 
                        
                        :rules="homeEdit.short_direction ? '' : 'required'"
                        v-model:data="homeEdit.short_direction" 
                    />
                </div>
            </div> 

            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label for="">Directions (How to get there)</label>
                    <ckeditor
                        name="direction_how_to_get_there" 
                        
                        v-model:data="homeEdit.direction_how_to_get_there" 
                    />
                </div>
            </div> 

            <div class="col-12">
                <div class="form-group">
                    <label for="">Home Rules</label>
                    <ckeditor
                        name="house_rules" 
                        v-model:data="homeEdit.house_rules" 
                    />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="">Cancellation Policy</label>
                    <ckeditor
                        name="cancellation_policy" 
                        v-model:data="homeEdit.cancellation_policy" 
                    />
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label for="">Brochure</label>
                    <PdfUploadFile 
                        name="brochure"
                        id="brochure"
                        fileType="pdf"
                        acceptType="application/pdf"
                        info="Max size: 100mb"
                        changeText="To change the PDF, click or drag a new file here!"
                        size="100"
                        :filePath="homeEdit.pdf_full_path"
                        :fileName="homeEdit.brochure"
                        @emitUploadFile="getUploadFile"
                        @emitDeleteUploadFile="deleteUploadFile"
                    />

                </div>
            </div>
        </div>
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
<script setup>
    import ckeditor from '@components/ckeditor.vue'
    import Tooltip from '@components/tooltip.vue'
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { useRouter, useRoute } from 'vue-router'
    import { toast } from '@utils/toast'
    import { ref, toRef, onMounted, computed, nextTick,defineAsyncComponent } from 'vue'

    const route = useRoute()
    const router = useRouter()
    const MultiSelect = defineAsyncComponent(() => import('@components/multi-select.vue'))

    const uploadFilepdf  = ref({})
    const homeType = ref([])
    const state = ref([])
    const location = ref([])
    const collectionList = ref([])
    const vCollectionId = ref([]);
    const vHomeTypeId = ref()
    const vStateId = ref()
    const vLocationId = ref()
    const PdfUploadFile = defineAsyncComponent(() => import('@components/PdfUploadFile.vue'))
    const submitApiUrl = ref(null)
    const isSubmitLoading = ref(false)

    const emit = defineEmits(['emitCurrentTab'])
    const props = defineProps({
        data: Object
    })

    const homeEdit = toRef(props, 'data')

     const getUploadFile = (value) => {
        uploadFilepdf.value = value
    } 
    const deleteUploadFile = (id) => {
        console.log(id,"id")
        axios.get(`/api/pdf-brochure-delete/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                locationEditData.value.image_name = ''
                locationEditData.value.image = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For get home type 
    const getHomeType = () => {
        axios.get('/api/home-types').then(res => {
            if(res.data.status){
                homeType.value = res.data.data
                
                nextTick(() => {
                    vHomeTypeId.value = homeEdit.value.home_type_id
                })
            }
        }).catch(error => {
            console.log(error);
        })
    }


    // For get state 
    const getState = () => {
        axios.get('/api/states').then(res => {
            if(res.data.status){
                state.value = res.data.data

                nextTick(() => {
                    if(route.params.id){
                        vStateId.value = homeEdit.value.state_id
                        getLocation()
                    }
                })  
            }
        }).catch(error => {
            console.log(error);
        })
    }


    // For get state wise location 
    const getLocation = async () => {
        await nextTick(() => {
            axios.get(`/api/location-by-state/${vStateId.value}`).then(res => {
                if(res.data.status){
                    location.value = res.data.data

                    nextTick(() => {
                        vLocationId.value = homeEdit.value.location_id
                    })
                }
            }).catch(error => {
                console.log(error);
            })
        }) 
    }

    // const getCollection = async () => {
    //     await nextTick(() => {
    //         axios.get(`/api/get-collection`).then(res => {
    //             if(res.data.status){
    //                 collectionList.value = res.data.data
    //                 nextTick(() => {

    //           homeEdit.value?.homecollections.forEach(item=>{
    //                 if(item.user_id){
    //                     vCollectionId.value.push(item.collection_id)
    //                 }
    //             })

    //                    // vCollectionId.value = homeEdit.value?.homecollections || null;
                        
    //                 })
    //             }
    //         }).catch(error => {
    //             console.log(error);
    //         })
    //     }) 
    // }

    const getCollection = async () => {
    try {
        // Fetch data from the API
        const res = await axios.get(`/api/get-collection`);
        if (res.data.status) {
            collectionList.value = res.data.data;
            //await nextTick();
            homeEdit.value?.homecollections.forEach((item) => {
                console.log(item.collection_id,"collection_id");
                if (item.collection_id) {
                    vCollectionId.value.push(item.collection_id);
                }
            });
        }
    } catch (error) {
        console.error("Error fetching collections:", error);
    }
};


    // For get time slots for 24 hour 
    const getTimeSlots = computed(() => {
        let startTime = dayjs().startOf('day').add(0, 'hour')
        let endTime = dayjs().startOf('day').add(24, 'hour')
        let timeSlots = []

        while(startTime.isBefore(endTime)){
            timeSlots.push(startTime.format('HH:mm'));
            startTime = startTime.add(60, 'minute');
        }

        return timeSlots
    })
    

    // For form on submit
    const onSubmit = (value, {resetForm}) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/home/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/home'
        }
        const payload = {
            ...value,
            brochure: uploadFilepdf.value.filename || '', // Use the file path of the uploaded PDF
            mappedProperties: vCollectionId.value  || '',
        };
 
        axios.post(submitApiUrl.value, payload).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false

                    emit('emitCurrentTab', 'Features')

                    if(!route.params.id){
                        router.push({
                            name: 'add-home', 
                            params: { 
                                id: res.data.last_insert_id 
                            }
                        })
                    }
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getHomeType()
        getState()
        getCollection()
    })
    

</script>