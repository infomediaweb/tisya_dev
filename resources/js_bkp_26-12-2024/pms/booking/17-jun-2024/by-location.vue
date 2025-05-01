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
                <router-link :to="{name: 'by-location'}" class="active">By Location</router-link>
            </li>
            <li class="list-group-item">
                <router-link :to="{name: 'by-property'}">By Property</router-link>
            </li>
        </ul>
        <section class="section">
            <div class="page-content">
                <div class="booking-filter">
                    <Form @submit="onFrmSearch" v-slot="{ errors, resetForm }">
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
                                                    @change="onLocationChange(obj, resetForm)"
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
                                                        <label for="">No. of Adults</label>
                                                        <Field
                                                            as="select"
                                                            name="no_adults"
                                                            v-model="no_adults"
                                                            class="form-control form-select"
                                                            :class="{'border-danger': errors.no_adults}">
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
                                                            :class="{'border-danger': errors.no_children}">
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
                                                <div class="col-6 col-lg align-self-center">
                                                    <div class="form-group mb-0">
                                                        <label for="" class="opacity-0 invisible">Tax Inclusive</label>
                                                        <div class="form-check form-check-lg form-check-box border-0 p-0">
                                                            <div class="row gx-2">
                                                                <div class="col-auto">
                                                                    <Field
                                                                        type="checkbox"
                                                                        name="cbk-inclusive-tax"
                                                                        id="cbk-inclusive-tax"
                                                                        class="form-check-input"
                                                                        v-model="taxInclusive"
                                                                        :value="1"
                                                                        :unchecked-value="0"
                                                                    />
                                                                </div>
                                                                <div class="col">
                                                                    <label for="cbk-inclusive-tax">Tax Inclusive</label>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                                        <div class="label-radio home-radio">
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
                                                                <p><i class="bi bi-person cms-n2"></i> {{ obj.maximum_number_of_guests }} Max Occupancy</p>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                       <template v-if="isPropertyBookingForm">
                                            <div class="d-flex justify-content-center py-4" v-if="isHomePriceLoading">
                                                <div class="spinner-border" role="status"></div>
                                            </div>
                                            <template v-else>
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
                                                            <!-- <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="">Address<span class="text-danger">*</span></label>
                                                                    <Field
                                                                        as="textarea"
                                                                        name="address"
                                                                        cols="30"
                                                                        rows="4"
                                                                        class="form-control"
                                                                        :class="{'border-danger': errors.address}"
                                                                        v-model="address"
                                                                        rules="required"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <div class="form-check form-check-lg form-check-box border-0 p-0">
                                                                        <div class="row gx-2">
                                                                            <div class="col-auto">
                                                                                <Field
                                                                                    type="checkbox"
                                                                                    name="cbk-invoice"
                                                                                    id="cbk-invoice"
                                                                                    class="form-check-input"
                                                                                    v-model="isInvoiceRequired"
                                                                                    :value="1"
                                                                                    :unchecked-value="0"
                                                                                />
                                                                            </div>
                                                                            <div class="col">
                                                                                <label for="cbk-invoice">Need Invoice</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> -->
                                                            <template v-if="isInvoiceRequired == 1">
                                                                <div class="col-6 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label for="">State<span class="text-danger">*</span></label>
                                                                        <Field
                                                                            name="state"
                                                                            as="select"
                                                                            class="form-control"
                                                                            :class="{'border-danger': errors.state}"
                                                                            rules="required">
                                                                            <option value="" selected disabled>Select State</option>
                                                                            <option
                                                                                v-for="(obj, index) in state"
                                                                                :value="obj.id">
                                                                                {{ obj.name }}
                                                                            </option>
                                                                        </Field>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label for="">Location<span class="text-danger">*</span></label>
                                                                        <Field
                                                                            type="text"
                                                                            name="company_location"
                                                                            class="form-control"
                                                                            :class="{'border-danger': errors.company_location}"
                                                                            rules="required"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label for="">Company Name<span class="text-danger">*</span></label>
                                                                        <Field
                                                                            type="text"
                                                                            name="company_name"
                                                                            class="form-control"
                                                                            :class="{'border-danger': errors.company_name}"
                                                                            rules="required"
                                                                        />
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label for="">GSTIN<span class="text-danger">*</span></label>
                                                                        <Field
                                                                            type="text"
                                                                            name="gstin"
                                                                            class="form-control"
                                                                            :class="{'border-danger': errors.gstin}"
                                                                            rules="required"
                                                                        />
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div class="col-12 text-end" v-if="Object.keys(homePrices).length">
                                                            <div class="row justify-content-end">
                                                                <div class="col-auto">
                                                                    <table class="table fs-13 table-sm table-borderless w-auto booking-price-info">
                                                                        <tbody>
                                                                            <tr>
                                                                                <th>Price Per Night:</th>
                                                                                <td>Rs. {{ currFormat(per_night_price) }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Number of Nights:</td>
                                                                                <td>{{ noOfNights }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Base Price:</td>
                                                                                <td>Rs. {{ currFormat(base_price) }}</td>
                                                                            </tr>
                                                                            <tr v-if="extraGuestCharge > 0">
                                                                                <td>Extra Guest:</td>
                                                                                <td>Rs. {{ currFormat(extraGuestCharge) }}</td>
                                                                            </tr>
                                                                            <template v-if="homePrices.additional_charge.length">
                                                                                <tr>
                                                                                    <th>Add-ons</th>
                                                                                    <td></td>
                                                                                </tr>
                                                                                <tr v-for="(obj, index) in homePrices.additional_charge">
                                                                                    <td>{{ obj.name }}:</td>
                                                                                    <td>Rs. {{ obj.final_price }}</td>
                                                                                </tr>
                                                                            </template>
                                                                            <tr>
                                                                                <th>Total:</th>
                                                                                <th>Rs. {{ currFormatTotal(totalAmount) }}</th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Offer Price:</td>
                                                                                <td>
                                                                                    <div class="input-group small-input-group">
                                                                                        <span class="input-group-text">Rs</span>
                                                                                        <Field
                                                                                            type="number"
                                                                                            name="offer_amount"
                                                                                            class="form-control"
                                                                                            :class="{'border-danger': errors.offer_amount}"
                                                                                            v-model="offerAmount"
                                                                                            @keyup="onChangeOfferAmount"
                                                                                            :rules="{min_value: 0}"
                                                                                            data-bs-toggle="tooltip"
                                                                                        />
                                                                                        <Tooltip
                                                                                            :error="errors.offer_amount"
                                                                                            :message="`The value should be greater than or equal to 0`"
                                                                                        />
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <!-- <tr>
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
                                                                            </tr> -->
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
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-save btn-primary">
                                                        <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                                        <span v-else>SUBMIT</span>
                                                    </button>
                                                </div>
                                            </template>
                                       </template>
                                    </div>
                                </Form>
                            </template>
                            <template v-else>
                                <div class="message text-center">
                                    <h6 class="fw-bold">No Record Found!</h6>
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
    import { ref, onMounted, watch, computed, nextTick, defineEmits } from 'vue'
    import { format, currFormat, currFormatTotal } from '@utils/common'
    import { toast } from '@utils/toast'

    const emit = defineEmits(['onFrmSearch'])

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
    const price = ref()
    const no_children = ref(0)
    const no_adults = ref(0)
    const homeClicki =  ref(false)
    const gstText =  ref('Exclusive')
    const isInvoiceRequired = ref(0)
    const state = ref(null)
    const address = ref(null)

    const gstSlab = ref([])
    const guestIncluded = ref()
    const extraGuestCharge = ref(0)
    const extraGuestChargePerNight = ref()
    const totalGuest = ref()
    const extraAddedGuestNumber = ref()

    const totalTaxableAmount = ref()
    const isHomePriceLoading = ref(false)
    const isPropertyBookingForm = ref(false)

    const customerDetail = ref({})
    const taxInclusive = ref(1)
    const offerAmount = ref('')
    const per_night_price  = ref('')
    const base_price = ref('')

    // For on change offer amount
    const onChangeOfferAmount = ({target}) => {
        offerAmount.value = totalAmount.value = base_price.value =  totalTaxableAmount.value  =  target.value
        per_night_price.value = target.value/noOfNights.value
        calculateGstOnOffer(gstSlab.value)
    }

    const calculateGstOnOffer = (list) => {
        let price = offerAmount.value
        gstSlab.value.forEach((item, idx) => {
            if(offerAmount.value >= item.slabs_start && offerAmount.value <= item.slabs_upto){
                tax.value = item.gst_percentage
                taxAmount.value = ((offerAmount.value * tax.value) / 100)
                totalAmount.value = totalTaxableAmount.value = price - taxAmount.value;
            }
        })
    }

    // For get total payable amount
     const totalPayableAmount = computed(() => {
        let totalFinalAmount;
        if(offerAmount.value !=''){
            totalFinalAmount = offerAmount.value
        }
        else{
            totalFinalAmount = totalTaxableAmount.value + taxAmount.value
        }
        return totalFinalAmount;
    })

    // For get location list
    const getLocation = async () => {
        axios.get(`/api/location/get/all`, {
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

    // For reset form data on location change
    const onLocationChange = (obj, resetForm) => {
        if(checkInDate.value && checkOutDate.value){
            resetForm({
                values: {
                    'location[]': vLocationId.value,
                }
            })
            homeList.value = []
        }
    }

    // For get booking edit data
    const getBookingEditData = () => {
        if(route.params.id){
            axios.get(`/api/property/booking/detail/${route.params.id}`).then(res => {
                if(res.data.status){
                    homeList.value = res.data.data
                    customerDetail.value = homeList.value[0].customer_detail
                    vLocationId.value = homeList.value[0].location_id
                    checkInDate.value = homeList.value[0].checkin_date
                    checkOutDate.value = homeList.value[0].checkout_date
                    no_adults.value = homeList.value[0].no_of_adult
                    no_children.value = homeList.value[0].no_of_children
                    discountAmount.value = homeList.value[0].discount_amount
                    onFrmSearch()
                    vHomeId.value = homeList.value[0].property_id
                    watch(homeList, (newVal, oldVal) => {
                        if(homeList.value.length && vHomeId.value){
                            onHomeRadio('', vHomeId.value)
                        }
                    })
                }
            }).catch(error => {

            })
        }
    }

    // For form on search
    const onFrmSearch = (v ) => {
        isSearchLoading.value = true
        isLoading.value = true
        noRecord.value = true
        vHomeId.value = ''
        isPropertyBookingForm.value = false
        axios.post('/api/property/list', {
            location_id: vLocationId.value,
            checkin_date: dayjs(checkInDate.value).format('YYYY-MM-DD'),
            checkout_date: dayjs(checkOutDate.value).format('YYYY-MM-DD'),
            no_children: no_children.value,
            no_adults: no_adults.value,
            txt_inclusive: taxInclusive.value
        }).then(res => {
            if(res.data.status){
                homeList.value = res.data.data.property_list
                gstSlab.value = res.data.data.gst_slab
                noOfNights.value = res.data.data.no_of_nights
                discountAmount.value = res.data.data.discount_amount
                state.value = res.data.data.states
                if(res.data.data.is_gst_allowed == 1){
                    gstText.value = "Inclusive"
                }
                !homeList.value.length ? noRecord.value = true : noRecord.value = false
                setTimeout(() => {
                    isSearchLoading.value = false
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isSearchLoading.value = false
            isLoading.value = false
        })
    }

    // For home radio input change
    const onHomeRadio = (e, id, idx) => {
        if(id){
            isPropertyBookingForm.value = true
        }
        isHomePriceLoading.value = true
        let homeListFilter = homeList.value.filter((item, idx) => {
            if(id == item.id){
                tax.value = item.gst_percentage
                extraGuestChargePerNight.value = item.extra_guest_charges
                guestIncluded.value = item.guests_included
                totalGuest.value = no_adults.value + no_children.value

                if(totalGuest.value > guestIncluded.value){
                    extraAddedGuestNumber.value = (totalGuest.value - guestIncluded.value)
                    extraGuestCharge.value = (extraGuestChargePerNight.value * extraAddedGuestNumber.value) * noOfNights.value
                    price.value = item.price + extraGuestCharge.value
                    totalAmount.value = price.value
                    taxAmount.value = ((totalAmount.value * tax.value) / 100)
                }
                else{
                    price.value = item.price
                    totalAmount.value = price.value
                    taxAmount.value = ((totalAmount.value * tax.value) / 100)
                    extraGuestCharge.value = 0
                }
                return item
            }
        })
        homePrices.value = homeListFilter[0]
        per_night_price.value = homePrices.value.per_night_price
        console.log(homePrices.value)
        base_price.value = homePrices.value.price

        let additionalPrice = homePrices.value.additional_charge.reduce((value, item, index) => {
            let finalPrice = item.price * noOfNights.value
            homePrices.value.additional_charge[index].final_price = finalPrice
            return value += finalPrice
        }, 0)

        totalAmount.value = totalAmount.value + additionalPrice
        totalTaxableAmount.value = totalAmount.value

        isInvoiceRequired.value = 0
        discountAmount.value = ''

        getSlabBaseGst(gstSlab.value)
        setTimeout(() => {
            isHomePriceLoading.value = false
        }, 300)
    }


    // For get slab base gst
    const getSlabBaseGst = (list) => {
        let slabAmount = totalTaxableAmount.value / noOfNights.value
        list.forEach((item, idx) => {
            if(slabAmount > item.slabs_start && slabAmount < item.slabs_upto){
                tax.value = item.gst_percentage
                console.log((totalTaxableAmount.value))
                taxAmount.value = ((totalTaxableAmount.value * tax.value) / 100)
            }
        })
    }

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

    // For form on Submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true
        v.id = route.params.id
        v.locationId = vLocationId.value
        v.propertyId = vHomeId.value
        v.checkInDate = dayjs(checkInDate.value).format('YYYY-MM-DD')
        v.checkOutDate = dayjs(checkOutDate.value).format('YYYY-MM-DD')
        v.noOfNights = noOfNights.value;
        v.tax = tax.value
        v.taxAmount = taxAmount.value
        v.netAmount = totalAmount.value
        v.netPayableAmount = totalPayableAmount.value
        v.discount_amount = discountAmount.value
        v.additional_charges = homePrices.value.additional_charge
        v.type = 'Location'
        v.no_children = no_children.value
        v.no_adult = no_adults.value
        v.per_night_price = ref(0)
        v.initial_price = homePrices.value.initial_price
        // v.is_invoice = v['cbk-invoice']

        axios.post('/api/property/booking', v).then(res => {
            if(res.data.status){
                setTimeout(() => {
                    toast(res.data.message, 'success').show()
                    isSubmitLoading.value = false;
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
