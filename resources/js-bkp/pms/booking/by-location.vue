<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">New Booking</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'manage-booking' }" class="btn rounded-pill btn-secondary-light">
                        <i class="icon-list me-2"></i>
                        Manage
                    </router-link>
                </div>
            </div>
        </div>

        <ul class="list-tab list-group mb-4">
            <li class="list-group-item">
                <router-link :to="{name: 'by-location'}" class="active">By Location</router-link>
            </li>
            <li class="list-group-item">
                <router-link :to="{name: 'by-property'}">By Property</router-link>
            </li>
        </ul>


        <section class="section">
            <div class="page-content">

                <div class="booking-filter">
                    <Form @submit="onFrmSearch" v-slot="{ errors }">
                        <div class="row gy-4">
                            <div class="col-12">
                                <div class="links-box">
                                    <div class="row g-2 g-md-3">
                                        <div 
                                            v-for="(obj, idx) in location"
                                            class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                                            <div class="label-radio">
                                                <Field 
                                                    type="radio" 
                                                    name="location[]" 
                                                    :id="`lr${idx}`"
                                                    :value="obj.id"
                                                    v-model="vLocationId"
                                                    rules="required"
                                                />
                                                <label :for="`lr${idx}`" :class="{'border-danger': errors['location[]']}">
                                                    <h3>{{ obj.location_name }}</h3>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="search-location-properties">
                                    <div class="row gy-3 gx-2 gx-md-3">
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group mb-0">
                                                <label for="">Check-In Date<span class="text-danger">*</span></label>
                                                <Field 
                                                    name="check-in-date" 
                                                    v-model="checkInDate" 
                                                    rules="required" 
                                                    v-slot="{ field }">
                                                    <DatePicker 
                                                        ref="checkInDateRef"
                                                        v-bind="field"
                                                        v-model="checkInDate" 
                                                        :format="format" 
                                                        :enable-time-picker="false" 
                                                        :min-date="new Date()"
                                                        :input-class-name="`form-control ${errors['check-in-date'] ? 'border-danger' : ''}`" 
                                                        prevent-min-max-navigation
                                                        auto-apply 
                                                    />
                                                </Field>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="form-group mb-0">
                                                <label for="">Check-Out Date<span class="text-danger">*</span></label>
                                                <Field name="check-out-date" v-model="checkOutDate" rules="required" v-slot="{ field }">
                                                    <DatePicker 
                                                        ref="checkOutDateRef"
                                                        v-bind="field"
                                                        v-model="checkOutDate" 
                                                        :format="format" 
                                                        :enable-time-picker="false" 
                                                        :min-date="checkOutMinDate"
                                                        :start-date="checkOutMinDate"
                                                        :input-class-name="`form-control ${errors['check-out-date'] ? 'border-danger' : ''}`" 
                                                        prevent-min-max-navigation
                                                        auto-apply 
                                                    /> 
                                                </Field>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <div class="row gx-2 gy-3 g-md-3">
                                                <div class="col-6 col-lg">
                                                    <div class="form-group mb-0">
                                                        <label for="">No. of Adults<span class="text-danger">*</span></label>
                                                        <Field
                                                            as="select"
                                                            name="no_adults"
                                                            class="form-control form-select"
                                                            :class="{'border-danger': errors.no_adults}"
                                                            >
                                                            <option value="" selected disabled>Please Select</option>
                                                            <option 
                                                                v-for="(obj, idx) in 10"
                                                                :value="obj">
                                                                {{ obj }}
                                                            </option>
                                                        </Field>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-lg">
                                                    <div class="form-group mb-0">
                                                        <label for="">No. of Children<span class="text-danger">*</span></label>
                                                        <Field
                                                            as="select"
                                                            name="no_children"
                                                            class="form-control form-select"
                                                            :class="{'border-danger': errors.no_children}"
                                                            >
                                                            <option value="" selected disabled>Please Select</option>
                                                            <option 
                                                                v-for="(obj, idx) in 7"
                                                                :value="obj">
                                                                {{ obj }}
                                                            </option>
                                                        </Field>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-auto align-self-end">
                                                    <button type="submit" class="btn w-100 btn-primary fw-bold miw-120">
                                                        <div v-if="isSearchLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                                        <span v-else>SEARCH</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Form>
                </div>


               
                <template v-if="noRecord || homeList.length">
                    <div class="booking-search-results mt-4 pt-4 border-top border-secondary-2">

                        <div class="d-flex justify-content-center py-4" v-if="isLoading">
                            <div class="spinner-border" role="status"></div>
                        </div>

                        <template v-else>
                            <template v-if="!noRecord">
                                <Form @submit="onSubmit" v-slot="{ errors }">
                                    <div class="row gy-4">
                                        <div class="col-12">
                                            <div class="links-box">
                                                <div class="row g-2 g-md-3">
                                                    <div 
                                                        v-for="(obj, idx) in homeList"
                                                        :key="idx"
                                                        class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                                                        <div class="label-radio">
                                                            <Field 
                                                                type="radio" 
                                                                name="home[]" 
                                                                :id="`hr${idx}`"
                                                                :value="obj.id"
                                                                v-model="vHomeId"
                                                                @change="onHomeRadio($event, obj.id, idx)"
                                                                rules="required"
                                                            />
                                                            <label :for="`hr${idx}`" :class="{'border-danger': errors['home[]']}">
                                                                <h3>{{ obj.home_name }}</h3>
                                                                <p><strong>Rs. {{ currFormat(obj.price) }}</strong> Rs. {{ currFormat(obj.per_night_price) }}/night</p>
                                                            </label>
                                                        </div>
                                                    </div>   
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-fields">
                                                <div class="row gx-2 gx-md-3">
                                                    <div class="col-6 col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Email Address<span class="text-danger">*</span></label>
                                                            <Field 
                                                                type="text"
                                                                name="email_address" 
                                                                class="form-control"
                                                                :class="{'border-danger': errors.email_address}"
                                                                rules="required|email"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Mobile Number<span class="text-danger">*</span></label>
                                                            <Field 
                                                                type="text"
                                                                name="mobile_number" 
                                                                class="form-control"
                                                                :class="{'border-danger': errors.mobile_number}"
                                                                rules="required|numeric"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">First Name<span class="text-danger">*</span></label>
                                                            <Field 
                                                                type="text"
                                                                name="first_name" 
                                                                class="form-control"
                                                                :class="{'border-danger': errors.first_name}"
                                                                rules="required|alpha"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-3">
                                                        <div class="form-group">
                                                            <label for="">Last Name<span class="text-danger">*</span></label>
                                                            <Field 
                                                                type="text"
                                                                name="last_name" 
                                                                class="form-control"
                                                                :class="{'border-danger': errors.last_name}"
                                                                rules="required|alpha"
                                                            />
                                                        </div>
                                                    </div>
                                                </div> 


                                                <div class="col-12 text-end" v-if="Object.keys(homePrices).length">
                                                    <div class="row justify-content-end">
                                                        <div class="col-auto">
                                                            <table class="table fs-13 table-sm table-borderless w-auto booking-price-info">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Price per night:</td>
                                                                        <td>Rs. {{ currFormat(homePrices.per_night_price) }} /night</td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td>Number of nights:</td>
                                                                        <td>XXXX</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Base price:</th>
                                                                        <td>Rs. XXXX</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Tax (XX%):</td>
                                                                        <td>Rs. XXXX</td>
                                                                    </tr>
                                                                    <tr class="fs-6">
                                                                        <th class="text-primary">Net price:</th>
                                                                        <th class="text-primary">Rs. XXXX</th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group mb-0">
                                                        <label for="">Note</label>
                                                        <textarea name="" id="" cols="30" rows="4" class="form-control"></textarea>
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
                            </template>

                            <template v-else>
                                <div class="message text-center">
                                    <h6 class="fw-bold">No Record Found</h6>
                                </div>
                            </template>

                        </template>
                    </div>
                </template>
            </div>
        </section>
    </div>
</template>
<script setup>
    import Tooltip from '@components/tooltip.vue'
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { useRouter, useRoute } from 'vue-router'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { format, currFormat } from '@utils/common'
    import { toast } from '@utils/toast'


    const route = useRoute()
    const router = useRouter()

    const location = ref([])
    const homeList = ref([])
    const homePrices = ref({})

    const vLocationId = ref('')
    const vHomeId = ref('')

    const currentDay = dayjs()

    const checkInDate = ref()
    const checkOutDate = ref()
    const checkOutMinDate = ref(currentDay.format())

    const checkInDateRef = ref()
    const checkOutDateRef = ref()

    const noRecord = ref(false)
    const isLoading = ref(false)
    const isSearchLoading = ref(false)
    const isSubmitLoading = ref(false)



    // For get location list
    const getLocation = async () => {
        axios.get(`/api/location`, {
            params: {
                status: 1
            }
        }).then(res => {
            if(res.data.status){
                location.value = res.data.data.data
            }
        }).catch(error => {
            console.log(error)
        })
    }

    
    // For on change check in date 
    watch(checkInDate, (newVal, oldVal) => {
        if(newVal){
            checkOutMinDate.value = dayjs(newVal).add(1, 'day').format()
            if(checkInDate.value >= checkOutDate.value){
                checkOutDateRef.value.clearValue()
            }
            if(checkInDate.value >= checkOutDate.value || !checkOutDate.value){
                checkOutDateRef.value.openMenu() 
            }
        }
    })


    // For on change check out date 
    watch(checkOutDate, (newVal, oldVal) => {
        if(newVal && !checkInDate.value){
            checkInDateRef.value.openMenu()
        } 
    })


    // For form on search
    const onFrmSearch = (v) => {
        isSearchLoading.value = true
        isLoading.value = true 
        noRecord.value = true
        
        axios.post('/api/property/list', {
            location_id: vLocationId.value,
            checkin_date: dayjs(checkInDate.value).format('YYYY-MM-DD'),
            checkout_date: dayjs(checkOutDate.value).format('YYYY-MM-DD')
        }).then(res => {
            if(res.data.status){
                homeList.value = res.data.data.property_list
                !homeList.value.length ? noRecord.value = true : noRecord.value = false

                setTimeout(() => {
                    isSearchLoading.value = false
                    isLoading.value = false 
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSearchLoading.value = false
            isLoading.value = false 
        })
    }


    // For home radio input change 
    const onHomeRadio = (e, id, idx) => {
        let homeListFilter = homeList.value.filter((item, idx) => {
          if(id == item.id){
            return item
          }
        })

        homePrices.value = homeListFilter[0]
    }
 

    // For form on Submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        // axios.post('/api/property/list', {
        //     location_id: vLocationId.value,
        //     checkin_date: dayjs(checkInDate.value).format('YYYY-MM-DD'),
        //     checkout_date: dayjs(checkOutDate.value).format('YYYY-MM-DD')
        // }).then(res => {
        //     if(res.data.status){

        //         setTimeout(() => {
        //             isSubmitLoading.value = false
        //         }, 400)
        //     }
        // }).catch(error => {
        //     toast(error.response.data.message, 'error').show()
        //     isSubmitLoading.value = false
        // })
    }



    onMounted(() => {
        getLocation()
    })


</script>