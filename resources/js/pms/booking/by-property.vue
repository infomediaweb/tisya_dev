<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Booking' : 'New Booking' }}</h1>
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
            <li class="list-group-item" v-if="!route.params.id">
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
                                                :disabledDates="checkinDisabledDates"
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
                                                :disabledDates="checkoutDisabledDates"
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
                                                        v-for="(obj, idx) in 12"
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
                                                        v-for="(obj, idx) in 12"
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
                                                rules="required|numeric|min:7|max:13"
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
                                                    <tr v-if="extraGuestCharge > 0">
                                                        <td>Extra Guest:</td>
                                                        <td>Rs. {{ currFormat(extraGuestCharge) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Number of Nights:</td>
                                                        <td>{{ noOfNights }}</td>
                                                    </tr>

                                                    <!-- <template v-if="constPropertyDetail.additional_charge.length">
                                                        <tr>
                                                            <th>Add-ons</th>
                                                            <td></td>
                                                        </tr>
                                                        <tr v-for="(obj, index) in constPropertyDetail.additional_charge">
                                                            <td>{{ obj.name }}:</td>
                                                            <td>Rs. {{ obj.price }}</td>
                                                        </tr>
                                                    </template> -->

                                                    <tr>
                                                        <th>Total:</th>
                                                        <th>Rs. {{ currFormatTotal(totalAmount) }}</th>
                                                    </tr>

                                                    <template v-if="dataRow.tax_inclusive == 1">
                                                        <tr>
                                                            <td>Offer Price:</td>
                                                            <td>
                                                                <div class="input-group small-input-group">
                                                                    <span class="input-group-text">Rs</span>
                                                                    <input
                                                                        type="number"
                                                                        name="discount_amount"
                                                                        class="form-control"
                                                                        v-model="discountAmount"
                                                                        @input="onChangeDiscountAmount"
                                                                    />
                                                                    <!-- <Field
                                                                        type="number"
                                                                        name="discount_amount"
                                                                        class="form-control"
                                                                        :class="{'border-danger': errors.discount_amount}"
                                                                        v-model="discountAmount"
                                                                        @keyup="onChangeDiscountAmount"
                                                                        :rules="{max_value: totalPayableAmountHidden + 1}"
                                                                        data-bs-toggle="tooltip"
                                                                    />
                                                                    <Tooltip
                                                                        :error="errors.discount_amount"
                                                                        :message="`The value should be less than or equal to ${currFormatTotal(totalPayableAmountHidden)}`"
                                                                    /> -->
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr v-if="discountAmount">
                                                            <td>
                                                                Price Per Night<span v-if="extraGuestCharge == 0">:</span><span v-if="extraGuestCharge > 0"> (Including Extra Guest):</span>
                                                            </td>
                                                            <td>Rs. {{ currFormat(perNightPriceWithExtraGuest) }}</td>
                                                        </tr>
                                                    </template>

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
    import { ref, onMounted, watch, nextTick, computed, inject } from 'vue'
    import { format, currFormat, currFormatTotal } from '@utils/common'
    import { toast } from '@utils/toast'


    const store = inject('store');
    const role = store.getters.user?.role;
    const userId = store.getters.user?.id;

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
    //const taxAmount = ref()
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
    //const totalTaxableAmount = ref()
    const isSearchFormShow = ref(false)
    const isSearchFormLoading = ref(false)

    const customerDetail = ref({})
    const taxInclusive = ref(0)
    const checkinDisabledDates = ref([])
    const checkoutDisabledDates = ref([])
    const matchValue = ref(false)
    const state = ref(null)


    const perNightPriceWithExtraGuest = ref()
    const dataRow = ref({})


    // For get location list
    const getLocation = async () => {
        axios.get(`/api/location`, {
            params: {
                status: 1,
                existing_property_id: route.params.id
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
        matchValue.value = false;
        if(newVal){
            checkOutMinDate.value = dayjs(newVal).add(1, 'day').format();
            if(checkInDate.value >= checkOutDate.value){
                checkOutDateRef.value.clearValue();
            }
            if(checkInDate.value >= checkOutDate.value || !checkOutDate.value){
                checkOutDateRef.value.openMenu();
            }
            checkoutDisabledDates.value = [];
            checkoutDisabledDates.value = disabledDates.value;
            nextTick().then(() => {
                for(let i = 1; i<=30; i++){
                    let nextCheckoutDate;
                    nextCheckoutDate = dayjs(new Date(newVal)).add(i, 'day').format('YYYY-MM-DD');
                    checkoutDisabledDates.value.forEach((item, ci) => {
                        let checkDate = dayjs(new Date(item)).format('YYYY-MM-DD');
                        if(nextCheckoutDate === checkDate){
                            if(!matchValue.value){
                                matchValue.value = true
                                let dDate = new Date(checkDate)
                                checkoutDisabledDates.value = checkoutDisabledDates.value.filter(
                                    disabledDate => disabledDate.getTime() !== dDate.getTime()
                                );
                            }
                        }
                    })
                }
            })
        }
    })


    // For on change check out date
    watch(checkOutDate, (newVal, oldVal) => {
        if(newVal && !checkInDate.value){
            checkInDateRef.value.openMenu()
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
                    taxInclusive.value = res.data.tax_inclusive
                    customerDetail.value = homeList.value[0].customer_detail
                    vLocationId.value = homeList.value[0].location_id

                    if(res.data.data.is_invoice == 'yes'){
                        isInvoiceRequired.value = 1
                    }

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
            checkout_date: dayjs(new Date()).format('YYYY-MM-DD'),
            existing_property_id: route.params.id
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
            propertyId: vHomeId.value,
            tax_inclusive: taxInclusive.value,
            existing_property_id: route.params.id
        }).then(res => {
            if(res.data.status){
                if(res.data.data.propertyDetail != null){
                    state.value = res.data.data.states
                    dataRow.value = res.data.data
                    gstSlab.value = res.data.data.gst_slab
                    noOfNights.value = res.data.data.no_of_nights
                    constPropertyDetail.value = res.data.data.propertyDetail
                    tax.value = res.data.data.propertyDetail.gst_percentage
                    extraGuestChargePerNight.value = constPropertyDetail.value.extra_guest_charges
                    guestIncluded.value = constPropertyDetail.value.guests_included
                    totalGuest.value = no_adults.value + no_children.value

                    if(totalGuest.value > guestIncluded.value){
                        extraAddedGuestNumber.value = (totalGuest.value - guestIncluded.value)
                        extraGuestCharge.value = constPropertyDetail.value.final_extra_guest_charge
                        price.value = (constPropertyDetail.value.per_night_price + extraGuestCharge.value) * noOfNights.value
                        totalAmount.value = price.value
                    }
                    else{
                        price.value = constPropertyDetail.value.per_night_price * noOfNights.value
                        totalAmount.value = price.value
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

                if(!route.params.id){
                    discountAmount.value = ''
                }

                isInvoiceRequired.value = 0

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


    // For get tax amount
    const taxAmount = computed(() => {
        return ((totalTaxableAmount.value * tax.value) / 100)
    })


    // For get total taxable amount
    const totalTaxableAmount = computed(() => {
        if(discountAmount.value){
            perNightPriceWithExtraGuest.value = (discountAmount.value * 100) / (100 + tax.value) / (noOfNights.value)
            return perNightPriceWithExtraGuest.value * noOfNights.value
        }
        else{
            return (totalAmount.value * 100) / (100 + tax.value)
        }

    })


    // For get slab base gst
    const getSlabBaseGst = () => {
        let slabAmount = totalPayableAmount.value / noOfNights.value

        tax.value = slabAmount > 8399 ? 18 : 12
        taxAmount.value = ((totalTaxableAmount.value * tax.value) / 100)
    }


    // For on watch get value
    watch(gstSlab, (newVal, oldVal) => {
        getSlabBaseGst()
    })


    // For on change discount amount
    const onChangeDiscountAmount = ({target}) => {
        getSlabBaseGst()
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
        checkinDisabledDates.value =[]
        checkoutDisabledDates.value = []

        resetSearchForm()

        axios.post('/api/property/unavaliabile/dates', {
            id: id,
            bookingId: route.params.id
        }).then(res => {
            if(res.data.status){
                isSearchFormShow.value = true

                if(res.data.propertyUnavailableDates != null){
                    res.data.propertyUnavailableDates.forEach(element => {
                        disabledDates.value.push(new Date(element))
                    })
                }

                checkoutDisabledDates.value =  disabledDates.value;
                checkinDisabledDates.value  =  disabledDates.value;

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
        v.tax_inclusive = taxInclusive.value
        v.is_invoice = isInvoiceRequired.value,
        v.totalTaxableAmount = totalTaxableAmount.value
        v.extraGuestCharge = extraGuestCharge.value
        v.booking_created_by = store.getters.user?.id
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
