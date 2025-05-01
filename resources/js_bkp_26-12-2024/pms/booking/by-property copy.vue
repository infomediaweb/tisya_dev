<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">New Booking</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'booking-list' }" class="btn rounded-pill btn-secondary-light">
                        <i class="icon-list me-2"></i>
                        Manage
                    </router-link>
                </div>
            </div>
        </div>
        <ul class="list-tab list-group mb-4">
            <li class="list-group-item">
                <router-link :to="{name: 'by-location'}">By Location</router-link>
            </li>
            <li class="list-group-item">
                <router-link :to="{name: 'by-property'}" class="active">By Property</router-link>
            </li>
        </ul>
        <section class="section">
            <Form @submit="onFrmSearch" v-slot="{ errors, resetForm }">
                <div class="links-box mb-4">
                    <div class="row g-2 g-md-3">
                        <div
                            v-for="(obj, idx) in location"
                            class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                            <div class="label-radio">
                                <Field
                                    type="radio"
                                    name="location[]"
                                    :id="`hr${idx}`"
                                    :value="obj.id"
                                    v-model="vLocationId"
                                    rules="required"
                                    @change="onLocationChange(obj, resetForm)"
                                />
                                <label :for="`hr${idx}`" :class="{'border-danger': errors['location[]']}">
                                    <h3>{{ obj.location_name }}</h3>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <template v-if="noRecord || homeList.length">
                    <div class="links-box my-4 pt-4 border-top border-secondary-2">
                        <div class="d-flex justify-content-center py-4" v-if="isLoading">
                            <div class="spinner-border" role="status"></div>
                        </div>
                        <template v-else>
                            <template v-if="!noRecord">
                                <div class="row g-2 g-md-3">
                                    <div
                                        v-for="(obj, idx) in homeList"
                                        :key="idx"
                                        class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                                        <div class="label-radio">
                                            <Field
                                                type="radio"
                                                name="home[]"
                                                :id="`pr${idx}`"
                                                :value="obj.id"
                                                v-model="vHomeId"
                                                @change="onHomeRadio($event, obj.id, idx, resetForm)"
                                                rules="required"
                                            />
                                            <label :for="`pr${idx}`" :class="{'border-danger': errors['home[]']}">
                                                <h3>{{  obj.home_name }}</h3>
                                                <p><i class="bi bi-person cms-n2"></i> {{ obj.maximum_number_of_guests }} Max Occupancy</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div class="message text-center">
                                    <h6 class="fw-bold">No Property Found</h6>
                                </div>
                            </template>
                        </template>
                    </div>

                    

                    <template v-if="isSearchFormShow">
                        <div class="d-flex justify-content-center py-4" v-if="isSearchFormLoading">
                            <div class="spinner-border" role="status"></div>
                        </div>

                        <div class="search-location-properties mb-4" v-else>
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
                                                :disabledDates="disabledDates"
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
                                                :disabledDates="disabledDates"
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
                                                <label for="">No. of Adults</label>
                                                <Field
                                                    as="select"
                                                    name="no_adults"
                                                    v-model="no_adults"
                                                    class="form-control form-select"
                                                    :class="{'border-danger': errors.no_adults}"
                                                    >
                                                    <option value="" selected>Please Select</option>
                                                    <option value="" >0</option>
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
                                                <label for="">No. of Children</label>
                                                <Field
                                                    as="select"
                                                    name="no_children"
                                                    v-model="no_children"
                                                    class="form-control form-select"
                                                    :class="{'border-danger': errors.no_children}"
                                                    >
                                                    <option value="" selected >Please Select</option>
                                                    <option value="" >0</option>
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
                    </template>
                </template>
            </Form>    

            
            <div class="page-content">
                <Form @submit="onSubmit" v-slot="{ errors, resetForm }">
                    <template v-if="isFilterActive">
                        <div class="d-flex justify-content-center py-4" v-if="isSubmitFormLoading">
                            <div class="spinner-border" role="status"></div>
                        </div>
                        <template v-else>

                            <div class="message text-center" v-if="isExtraFormFieldsError">
                                <h6 class="fw-bold">No Record Found!</h6>
                            </div>

                            <div class="form-fields" v-else>
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
                                                v-model="customerDetail.email"
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
                                                v-model="customerDetail.mobile_number"
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
                                                v-model="customerDetail.first_name"
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
                                                v-model="customerDetail.last_name"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end" v-if="constPropertyDetail">
                                    <div class="row justify-content-end">
                                        <div class="col-auto">
                                            <table class="table fs-13 table-sm table-borderless w-auto booking-price-info">
                                                <tbody>
                                                    <tr>
                                                        <th>Price Per Night:</th>
                                                        <td>Rs. {{  currFormat(constPropertyDetail.per_night_price) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Number of Nights:</td>
                                                        <td>{{ noOfNights }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Base Price:</th>
                                                        <td>Rs. {{ currFormat(constPropertyDetail.price) }}</td>
                                                    </tr>

                                                    <tr v-if="extraGuestCharge > 0">
                                                        <td>Extra Guest:</td>
                                                        <td>Rs. {{ currFormat(extraGuestCharge) }}</td>
                                                    </tr>

                                                
                                                    <template v-if="constPropertyDetail.additional_charge.length">
                                                        <tr>
                                                            <th>Add-ons</th>
                                                            <td></td>
                                                        </tr>
                                                        <tr v-for="(obj, index) in constPropertyDetail.additional_charge">
                                                            <td>{{ obj.name }}:</td>
                                                            <td>Rs. {{ obj.price }}</td>
                                                        </tr>
                                                    </template>

                                                
                                                    <tr>
                                                        <th>Total:</th>
                                                        <th>Rs. {{ currFormatTotal(totalAmount) }}</th>
                                                    </tr>
                                                    

                                                    <tr>
                                                        <td>Discount:</td>
                                                        <td>
                                                            <div class="input-group small-input-group">
                                                                <span class="input-group-text">Rs</span>
                                                                <Field
                                                                    type="number"
                                                                    name="discount_amount"
                                                                    class="form-control"
                                                                    :class="{'border-danger': errors.discount_amount}"
                                                                    v-model="discountAmount"
                                                                    @keyup="onChangeDiscountAmount"
                                                                    :rules="{max_value: totalAmount}"
                                                                    data-bs-toggle="tooltip"
                                                                />
                                                                <Tooltip 
                                                                    :error="errors.discount_amount"
                                                                    :message="`The value should be less than or equal to ${currFormatTotal(totalAmount)}`"
                                                                />
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>Taxable Amount:</td>
                                                        <td>Rs. {{ currFormatTotal(totalTaxableAmount) }}</td>
                                                    </tr>

                                                    <tr>
                                                        <td>GST ({{ tax }}%):</td>
                                                        <td>Rs. {{ currFormat(taxAmount) }}</td>
                                                    </tr>
                                                    

                                                    <tr class="fs-6">
                                                        <th class="text-primary">Total Amount Payable:</th>
                                                        <th class="text-primary">Rs. {{ currFormatTotal(totalPayableAmount) }}</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label for="">Note</label>
                                        <Field 
                                            as="textarea"
                                            name="note" 
                                            id="" 
                                            cols="30" 
                                            rows="4" 
                                            class="form-control"
                                            v-model="customerDetail.note"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-save btn-primary">
                                        <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                        <span v-else>SUBMIT</span>
                                    </button>
                                </div>
                            </div>

                        </template>
                    </template>
                </Form>
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
    import { ref, onMounted, watch, nextTick, computed } from 'vue'
    import { format, currFormat, currFormatTotal } from '@utils/common'
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

    const noOfNights = ref()
    const tax = ref()

    const taxAmount = ref()
    const totalAmount = ref()
    const discountAmount = ref()

    const no_children = ref(0)
    const no_adults = ref(0)
    const price = ref()

    const isFilterActive = ref(false)

    const constPropertyDetail = ref({})

    const isSubmitFormLoading = ref(false)
    const isExtraFormFieldsError = ref(false)

    const disabledDates = ref( [])
    const gstText = ref('Exclusive')

    const isInvoiceRequired = ref(0)

    const gstSlab = ref([])
    const guestIncluded = ref()
    const extraGuestCharge = ref(0)
    const extraGuestChargePerNight = ref()
    const totalGuest = ref()
    const extraAddedGuestNumber = ref()

    const totalTaxableAmount = ref()

    const isSearchFormShow = ref(false)
    const isSearchFormLoading = ref(false)

    const customerDetail = ref({})


    // For get location list
    const getLocation = async () => {
        axios.get(`/api/location`, {
            params: {
                status: 1
            }
        }).then(res => {
            if(res.data.status){
                location.value = res.data.data.data;
            }
        }).catch(error => {
            console.log(error);
        })
    }


    // For on change check in date
    watch(checkInDate, (newVal, oldVal) => {
        if(newVal){
            checkOutMinDate.value = dayjs(newVal).add(1, 'day').format()
            if(checkInDate.value >= checkOutDate.value){
                checkOutDateRef.value.clearValue();
            }
            if(checkInDate.value >= checkOutDate.value || !checkOutDate.value){
                checkOutDateRef.value.openMenu();
            }
        }
    })


    // For on change check out date
    watch(checkOutDate, (newVal, oldVal) => {
        if(newVal && !checkInDate.value){
            checkInDateRef.value.openMenu();
        }
    })


    const resetSearchForm = () => {
        checkInDate.value = ''
        checkOutDate.value = ''
        no_adults.value = ''
        no_children.value = ''
    }
 
    const onLocationChange = (obj, resetForm) => {
        vHomeId.value = ''

        resetSearchForm()
        getProperty() 
    }


    // For get booking edit data 
    const getBookingEditData = () => {
        if(route.params.id){
            axios.get(`/api/property/booking/detail/${route.params.id}`).then(res => {
                if(res.data.status){
                    homeList.value = res.data.data
                    customerDetail.value = homeList.value[0].customer_detail
                    vLocationId.value = homeList.value[0].location_id

                    getProperty()

                    vHomeId.value = homeList.value[0].property_id

                    onHomeRadio('', vHomeId.value)

                    checkInDate.value = homeList.value[0].checkin_date
                    checkOutDate.value = homeList.value[0].checkout_date
                    no_adults.value = homeList.value[0].no_of_adult
                    no_children.value = homeList.value[0].no_of_children
                    discountAmount.value = homeList.value[0].discount_amount
                
                    
                    watch(homeList, (newVal, oldVal) => {
                        if(checkInDate.value && checkOutDate.value){
                            onFrmSearch()
                        }
                    }) 
                     
                }
            }).catch(error => {

            })
        }
    }


    const getProperty = (v) => {
        isSearchFormShow.value = false
        isFilterActive.value = false
        isLoading.value = true
        noRecord.value = true

        axios.post('/api/property/list/by/id', {
            location_id: vLocationId.value,
            checkin_date: dayjs(new Date()).format('YYYY-MM-DD'),
            checkout_date: dayjs(new Date()).format('YYYY-MM-DD')
        }).then(res => {
            if(res.data.status){
                homeList.value = res.data.data.property_list
                noOfNights.value = res.data.data.no_of_nights
                !homeList.value.length ? noRecord.value = true : noRecord.value = false

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isLoading.value = false
        })
    }


    // For form on search
    const onFrmSearch = (v) => {
        isSearchLoading.value = true
        isSubmitFormLoading.value = true
        isFilterActive.value = true

        axios.post('/api/property/avaliability/detail', {
            location_id: vLocationId.value,
            checkin_date: dayjs(checkInDate.value).format('YYYY-MM-DD'),
            checkout_date: dayjs(checkOutDate.value).format('YYYY-MM-DD'),
            no_children: no_children.value,
            no_adults: no_adults.value,
            propertyId: vHomeId.value
        }).then(res => {
            if(res.data.status){
                if(res.data.data.propertyDetail != null){
                    gstSlab.value = res.data.data.gst_slab
                    noOfNights.value = res.data.data.no_of_nights
                    constPropertyDetail.value = res.data.data.propertyDetail

                    tax.value = res.data.data.propertyDetail.gst_percentage
                    extraGuestChargePerNight.value = constPropertyDetail.value.extra_guest_charges
                    guestIncluded.value = constPropertyDetail.value.guests_included
                    totalGuest.value = no_adults.value + no_children.value


                    if(totalGuest.value > guestIncluded.value){
                        extraAddedGuestNumber.value = (totalGuest.value - guestIncluded.value)
                        extraGuestCharge.value = (extraGuestChargePerNight.value * extraAddedGuestNumber.value) * noOfNights.value
                        price.value = constPropertyDetail.value.price + extraGuestCharge.value
                        totalAmount.value = price.value
                        taxAmount.value = ((totalAmount.value * tax.value) / 100)

                    }
                    else{
                        price.value = constPropertyDetail.value.price
                        totalAmount.value = price.value
                        taxAmount.value = ((totalAmount.value * tax.value) / 100)

                        extraGuestCharge.value = 0
                    }


                    isExtraFormFieldsError.value = false


                    if(res.data.data.is_gst_allowed == 1){
                        gstText.value = "Inclusive"
                    }
                }
                else{
                    isExtraFormFieldsError.value = true
                }

                isSubmitFormLoading.value = false

                let additionalPrice =  constPropertyDetail.value.additional_charge.reduce((value, item, index) => {
                    let finalPrice = item.price * noOfNights.value
                    constPropertyDetail.value.additional_charge[index].price = finalPrice
                    return value += finalPrice
                }, 0)

                totalAmount.value = totalAmount.value + additionalPrice
                totalTaxableAmount.value = totalAmount.value

                isInvoiceRequired.value = 0
                discountAmount.value = ''

                setTimeout(() => {
                    isSubmitFormLoading.value = false
                    isSearchLoading.value = false
                }, 400)

            }
        }).catch(error => {
            // toast(error.response.data.message, 'error').show()
            isSubmitFormLoading.value = false
        })
    }


    // For get slab base gst 
    const getSlabBaseGst = (list) => {
        let slabAmount = totalTaxableAmount.value / noOfNights.value

        list.forEach((item, idx) => {
            if(slabAmount > item.slabs_start && slabAmount < item.slabs_upto){
                tax.value = item.gst_percentage
                taxAmount.value = ((totalTaxableAmount.value * tax.value) / 100)
            }
        })
    }


    // For on watch get value
    watch(gstSlab, (newVal, oldVal) => {
        getSlabBaseGst(newVal)
    })


    // For on change discount amount 
    const onChangeDiscountAmount = ({target}) => {
        if(target.value > totalAmount.value){
            totalTaxableAmount.value = totalAmount.value
        }
        else if(target.value == totalAmount.value){
            taxAmount.value = 0
            totalTaxableAmount.value = 0
        }
        else{
            totalTaxableAmount.value = totalAmount.value - discountAmount.value
        }

        getSlabBaseGst(gstSlab.value)
    }


    // For get total payable amount 
    const totalPayableAmount = computed(() => {
        let totalFinalAmount = totalTaxableAmount.value + taxAmount.value
        return totalFinalAmount
    })



    // For home radio input change
    const onHomeRadio = (e, id, idx, resetForm) => {
        isSearchFormLoading.value = true
        isFilterActive.value = false
        disabledDates.value = []

        resetSearchForm()

        axios.post('/api/property/unavaliabile/dates', {
            id: id
        }).then(res => {
            if(res.data.status){
                isSearchFormShow.value = true

                if(res.data.propertyUnavailableDates != null){
                    res.data.propertyUnavailableDates.forEach(element => {
                        disabledDates.value.push(new Date(element))
                    })
                }

                setTimeout(() => {
                    isSearchFormLoading.value = false
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isLoading.value = false
        })
    }


    // For form on Submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        v.id = route.params.id
        v.locationId = vLocationId.value
        v.propertyId = vHomeId.value
        v.checkInDate = dayjs(checkInDate.value).format('YYYY-MM-DD')
        v.checkOutDate = dayjs(checkOutDate.value).format('YYYY-MM-DD')
        v.noOfNights = noOfNights.value
        v.tax = tax.value
        v.taxAmount = taxAmount.value
        v.netAmount = totalAmount.value
        v.netPayableAmount = totalPayableAmount.value
        v.discount_amount = discountAmount.value
        v.additional_charges = constPropertyDetail.value.additional_charge
        v.type = 'Property'
        v.no_children = no_children.value
        v.no_adult = no_adults.value
        v.per_night_price = constPropertyDetail.value.per_night_price
        v.initial_price = constPropertyDetail.value.initial_price
        // v.is_invoice = v['cbk-invoice']

        axios.post('/api/property/booking', v).then(res => {
            if(res.data.status){
                setTimeout(() => {
                    toast(res.data.message, 'success').show()
                    isSubmitLoading.value = false
                    router.push({name: 'booking-list'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getLocation()
        getBookingEditData()
    })


</script>
