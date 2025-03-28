<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Coupon' : 'Add Coupon' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'list-coupon'}" class="btn rounded-pill btn-secondary-light">
                        <i class="icon-list me-2"></i>
                        Manage
                    </router-link>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form action="" @submit="onSubmit" v-slot="{errors}">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 col-lg-4 col-xxl-4">
                                    <div class="form-group">
                                        <label for="">Name<span class="text-danger">*</span></label>
                                        <Field
                                            type="text"
                                            name="coupon_title"
                                            class="form-control"
                                            :class="{'border-danger': errors.coupon_title}"
                                            v-model="couponEditData.title"
                                            rules="required"
                                        />
                                    </div>
                                </div>
                                <div class="col-6 col-lg-4 col-xxl-3">
                                    <div class="form-group">
                                        <label for="">Start Date<span class="text-danger">*</span></label>
                                        <Field
                                            name="start-date"
                                            v-model="startDate"
                                            rules="required"
                                            v-slot="{ field }">
                                            <DatePicker
                                                ref="startDateRef"
                                                v-bind="field"
                                                v-model="startDate"
                                                :format="format"
                                                :enable-time-picker="false"
                                                :min-date="new Date()"
                                                :input-class-name="`form-control ${errors['start-date'] ? 'border-danger' : ''}`"
                                                prevent-min-max-navigation
                                                auto-apply
                                            />
                                        </Field>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-4 col-xxl-3">
                                    <div class="form-group">
                                        <label for="">End Date<span class="text-danger">*</span></label>
                                        <Field name="end-date" v-model="endDate" rules="required" v-slot="{ field }">
                                            <DatePicker
                                                ref="endDateRef"
                                                v-bind="field"
                                                v-model="endDate"
                                                :format="format"
                                                :enable-time-picker="false"
                                                :min-date="endMinDate"
                                                :start-date="endMinDate"
                                                :input-class-name="`form-control ${errors['end-date'] ? 'border-danger' : ''}`"
                                                prevent-min-max-navigation
                                                auto-apply
                                            />
                                        </Field>
                                    </div>
                                </div>
                                <!-- <div class="col-12 col-lg-auto">
                                    <div class="form-group">
                                        <label for="">To offer only to first time users</label>
                                        <div class="form-check form-check-lg form-check-box border-0" style="padding: 0!important;">
                                            <div class="row gx-3">
                                                <div class="col-auto">
                                                    <Field
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        name="first_time_users"
                                                        id="first_time_users"
                                                        :value="1"
                                                        :unchecked-value="0"
                                                        v-model="firstTimeUsers"
                                                    />
                                                </div>
                                                <div class="col">
                                                    <label for="first_time_users">Check if Yes</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>  -->
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Use Type<span class="text-danger">*</span></label>
                                        <div class="row g-3 align-items-center">
                                            <div class="col-auto">
                                                <div class="form-check form-radio-check">
                                                    <Field
                                                        type="radio"
                                                        class="form-check-input"
                                                        name="use_type"
                                                        id="r-single"
                                                        value="single"
                                                        v-model="useType"
                                                        @change="onChangeUseTypeRadio"
                                                    />
                                                    <label for="r-single" class="form-check-label">Single</label>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check form-radio-check">
                                                    <Field
                                                        type="radio"
                                                        class="form-check-input"
                                                        name="use_type"
                                                        id="r-multiple"
                                                        value="multiple"
                                                        v-model="useType"
                                                        @change="onChangeUseTypeRadio"
                                                    />
                                                    <label for="r-multiple" class="form-check-label">Multiple</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xxl">
                                                <Field
                                                    type="number"
                                                    class="form-control"
                                                    name="people_use_limit"
                                                    placeholder="People Use Limit"
                                                    :disabled="useType != 'multiple'"
                                                    v-model="couponEditData.use_limit"
                                                />
                                            </div>
                                        </div>

                                        <div class="fs-10 fst-italic text-danger pt-1">For multiple coupons with unlimited use, select "Multiple" and leave the field blank</div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Discount<span class="text-danger">*</span></label>
                                        <div class="row g-3 align-items-center">
                                            <div class="col-auto">
                                                <div class="form-check form-radio-check">
                                                    <Field
                                                        type="radio"
                                                        class="form-check-input"
                                                        name="discount_type"
                                                        id="r-percentage"
                                                        value="percentage"
                                                        v-model="discountType"
                                                    />
                                                    <label for="r-percentage" class="form-check-label">Percentage</label>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check form-radio-check">
                                                    <Field
                                                        type="radio"
                                                        class="form-check-input"
                                                        name="discount_type"
                                                        id="r-flat"
                                                        value="flat"
                                                        v-model="discountType"
                                                    />
                                                    <label for="r-flat" class="form-check-label">Flat</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xxl">
                                                <Field
                                                    v-if="discountType == 'percentage'"
                                                    type="number"
                                                    name="discount_amount"
                                                    class="form-control"
                                                    :class="{'border-danger': errors.discount_amount}"
                                                    placeholder="Discount percentage"
                                                    v-model="couponEditData.discount_percentage"
                                                    rules="required"
                                                />

                                                <Field
                                                    v-else
                                                    type="number"
                                                    name="discount_amount"
                                                    class="form-control"
                                                    :class="{'border-danger': errors.discount_amount}"
                                                    placeholder="Discount Amount"
                                                    v-model="couponEditData.discount_flat"
                                                    rules="required"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <label for="">Generate Code By<span class="text-danger">*</span></label>
                                        <div class="row g-3 align-items-center">
                                            <div class="col-auto">
                                                <div class="form-check form-radio-check">
                                                    <Field
                                                        type="radio"
                                                        class="form-check-input"
                                                        name="generated_by"
                                                        id="r-self"
                                                        value="self"
                                                        v-model="generatedBy"
                                                    />
                                                    <label for="r-self" class="form-check-label">Self</label>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="form-check form-radio-check">
                                                    <Field
                                                        type="radio"
                                                        class="form-check-input"
                                                        name="generated_by"
                                                        id="r-auto"
                                                        value="auto"
                                                        v-model="generatedBy"
                                                    />
                                                    <label for="r-auto" class="form-check-label">Auto</label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xxl">
                                                <Field
                                                    v-if="generatedBy == 'self'"
                                                    type="text"
                                                    name="coupon_code"
                                                    class="form-control"
                                                    :class="{'border-danger': errors.coupon_code}"
                                                    placeholder="Code"
                                                    v-model="couponEditData.coupon_code_self"
                                                    rules="required"
                                                />

                                                <div class="row gx-2" v-else>
                                                    <div class="col-4">
                                                        <Field
                                                            type="text"
                                                            name="prefix"
                                                            class="form-control"
                                                            :class="{'border-danger': errors.prefix}"
                                                            placeholder="Prefix"
                                                            v-model="couponEditData.prefix"
                                                            rules="required"
                                                        />
                                                    </div>
                                                    <div class="col-8">
                                                        <Field
                                                            type="number"
                                                            name="no_of_codes"
                                                            class="form-control"
                                                            :class="{'border-danger': errors.no_of_codes}"
                                                            placeholder="No of Codes"
                                                            v-model="couponEditData.coupon_code_auto"
                                                            rules="required"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="">Min. number of nights</label>
                                        <Field
                                            type="number"
                                            name="no_of_nights"
                                            class="form-control"
                                            v-model="couponEditData.coupon_valid_on_min_no_of_nights"
                                        />
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="">Min days before check-in</label>
                                        <Field
                                            type="number"
                                            name="min_days_before_check_in"
                                            class="form-control"
                                            v-model="couponEditData.min_days_before_check_in"
                                        />
                                    </div>
                                </div>  -->
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label for="">Stay dates between <small>(From/To, including the start and end night)</small><span class="text-danger">*</span></label>
                                        <div class="row gy-3">
                                            <div class="col">
                                                <Field
                                                    name="coupon-stay-start-date"
                                                    v-model="couponStayStartDate"
                                                    rules="required"
                                                    v-slot="{ field }">
                                                    <DatePicker
                                                        ref="couponStayStartDateRef"
                                                        v-bind="field"
                                                        v-model="couponStayStartDate"
                                                        :format="format"
                                                        :enable-time-picker="false"
                                                        :min-date="new Date()"
                                                        :input-class-name="`form-control ${errors['coupon-stay-start-date'] ? 'border-danger' : ''}`"
                                                        placeholder="Start Date"
                                                        prevent-min-max-navigation
                                                        auto-apply
                                                    />
                                                </Field>
                                            </div>
                                            <div class="col">
                                                <Field
                                                    name="coupon-stay-end-date"
                                                    v-model="couponStayEndDate"
                                                    rules="required"
                                                    v-slot="{ field }">
                                                    <DatePicker
                                                        ref="couponStayEndDateRef"
                                                        v-bind="field"
                                                        v-model="couponStayEndDate"
                                                        :format="format"
                                                        :enable-time-picker="false"
                                                        :min-date="couponStayEndMinDate"
                                                        :start-date="couponStayEndMinDate"
                                                        :input-class-name="`form-control ${errors['coupon-stay-end-date'] ? 'border-danger' : ''}`"
                                                        placeholder="End Date"
                                                        prevent-min-max-navigation
                                                        auto-apply
                                                    />
                                                </Field>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="">Property Type <small>(Multiple Selection)</small></label>
                                        <Field
                                            as="div"
                                            name="property_type"
                                            class="dropdown form-multiselect">
                                            <MultiSelect
                                                :items="propertyTypeList"
                                                item-name="name"
                                                item-id="id"
                                                name="ckb-pt"
                                                placeholder="Select Property Type"
                                                v-model="propertyType"
                                                @emit-change="getPropertyList"
                                            />
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-3">
                                    <div class="form-group">
                                        <label for="">Property <small>(Multiple Selection)</small></label>
                                        <Field
                                            as="div"
                                            name="property_name"
                                            class="dropdown form-multiselect">
                                            <MultiSelect
                                                :items="propertyList"
                                                item-name="home_name"
                                                item-id="id"
                                                name="ckb-pn"
                                                placeholder="Select Property"
                                                v-model="propertyId"
                                            />
                                        </Field>
                                    </div>
                                </div>

                                <!-- <div class="col-12">
                                    <div class="form-group ckeditor-form">
                                        <label for="">Terms and Conditions</label>
                                        <ckeditor
                                            name="terms_and_conditions"

                                        />
                                    </div>
                                </div> -->


                            </div>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <button class="btn btn-save btn-primary">
                                    <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                    <span v-else>SUBMIT</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </Form>
            </div>
        </section>
    </div>
</template>
<script setup>
    import ckeditor from '@components/ckeditor.vue'
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { ref, onMounted, watch, defineAsyncComponent, nextTick } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { Form, Field } from 'vee-validate'
    import { format, standardFormat } from '@utils/common'
    import { toast } from '@utils/toast'

    const MultiSelect = defineAsyncComponent(() => import('@components/multi-select.vue'))

    const route = useRoute()
    const router = useRouter()

    const currentDay = dayjs()

    const startDate = ref()
    const endDate = ref()
    const endMinDate = ref(currentDay.format())
    const startDateRef = ref()
    const endDateRef = ref()

    const couponStayStartDate = ref()
    const couponStayEndDate = ref()
    const couponStayEndMinDate = ref(currentDay.format())
    const couponStayStartDateRef = ref()
    const couponStayEndDateRef = ref()

    const firstTimeUsers = ref(0)
    const useType = ref('single')
    const discountType = ref('percentage')
    const generatedBy = ref('self')

    const propertyType = ref([])
    const propertyId = ref([])

    const propertyTypeList = ref([])
    const propertyList = ref([])

    const couponEditData = ref({})

    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


    const handleStartDateChange = (newDate, endDate, endDateRef, endMinDate) => {
        if(newDate){
            endMinDate.value = dayjs(newDate).add(1, 'day').format()
            if(standardFormat(newDate) >= standardFormat(endDate.value)){
                endDateRef.value.clearValue()
            }
            if(standardFormat(newDate) >= standardFormat(endDate.value) || !endDate.value){
                endDateRef.value.openMenu()
            }
        }
    }

    const handleEndDateChange = (newDate, startDate, startDateRef) => {
        if(standardFormat(newDate) && !startDate.value){
            startDateRef.value.openMenu()
        }
    }

    // For on change start date
    watch(startDate, (newVal, oldVal) => {
        handleStartDateChange(newVal, endDate, endDateRef, endMinDate)
    })

    // For on change end date
    watch(endDate, (newVal, oldVal) => {
        handleEndDateChange(newVal, startDate, startDateRef)
    })

    // For on change coupon start date
    watch(couponStayStartDate, (newVal, oldVal) => {
        handleStartDateChange(newVal, couponStayEndDate, couponStayEndDateRef, couponStayEndMinDate)
    })

    // For on change coupon end date
    watch(couponStayEndDate, (newVal, oldVal) => {
        handleEndDateChange(newVal, couponStayStartDate, couponStayStartDateRef)
    })


    // For use type on change 
    const onChangeUseTypeRadio = () => {
        if(useType.value == 'single'){
            couponEditData.value['use_limit_dummy'] = couponEditData.value['use_limit']
            couponEditData.value['use_limit'] = ''
        }
        else{
            couponEditData.value['use_limit'] = couponEditData.value['use_limit_dummy']
        }
    }


    // For get property type
    const getPropertyTypes = () => {
        axios.get('/api/home-types').then(res => {
            if(res.data.status){
                propertyTypeList.value = res.data.data
            }
        }).catch(error => {
            console.log(error)
        })
    }


    // For get property list
    const getPropertyList = (e) => {
        propertyId.value = e ? [] : propertyId.value

        axios.get('/api/get/property/by/propertyTypes', {
            params: {
                ids: JSON.stringify(propertyType.value)
            }
        }).then(res => {
            if(res.data.status){
                propertyList.value = res.data.data

            }
        }).catch(error => {
            console.log(error)
        })
    }


    // For coupon edit
    const getCouponEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/coupon/code/show/${route.params.id}`).then(res => {
                if(res.data.status){
                    couponEditData.value = res.data.data

                    startDate.value = couponEditData.value.start_date
                    endDate.value = couponEditData.value.end_date
                    firstTimeUsers.value = couponEditData.value.is_offer_valid_only_for_first_time
                    useType.value = couponEditData.value.user_type
                    discountType.value = couponEditData.value.discount_type
                    generatedBy.value = couponEditData.value.generated_coupon_code_by
                    couponStayStartDate.value = couponEditData.value.stay_date_from
                    couponStayEndDate.value = couponEditData.value.stay_date_to
                    propertyType.value = couponEditData.value.property_type_id
                    propertyId.value = couponEditData.value.property_id


                    nextTick(() => {
                        getPropertyTypes()
                        getPropertyList()
                    })


                    setTimeout(() => {
                        isLoading.value = false
                    }, 400)
                }
            }).catch(error => {
                toast(error.response.data.message, 'error').show()
                isLoading.value = false
            })
        }
        else{
            getPropertyTypes()
            getPropertyList()
        }
    }


    // For on submit form
    const onSubmit = (v) => {
        isSubmitLoading.value = true
        submitApiUrl.value = 'api/coupon/code/save'

        axios.post('/api/coupon/code/save', {
            title: v.coupon_title,
            start_date: dayjs(startDate.value).format('YYYY-MM-DD'),
            end_date: dayjs(endDate.value).format('YYYY-MM-DD'),
            is_offer_valid_only_for_first_time: firstTimeUsers.value,
            user_type: useType.value.toLowerCase(),
            use_limit: v.people_use_limit,
            discount_type: discountType.value.toLowerCase(),
            discount: v.discount_amount,
            generated_coupon_code_by: generatedBy.value.toLowerCase(),
            coupon_code: v.coupon_code,
            no_of_coupon_codes: v.no_of_codes,
            coupon_valid_on_min_no_of_nights: v.no_of_nights,
            coupon_min_days_before_check_in: v.min_days_before_check_in,
            stay_date_from: dayjs(couponStayStartDate.value).format('YYYY-MM-DD'),
            stay_date_to: dayjs(couponStayEndDate.value).format('YYYY-MM-DD'),
            property_type_id: propertyType.value,
            property_id: propertyId.value,
            term_and_conditions: v.terms_and_conditions,
            id: route.params.id,
            prefix: v.prefix
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    router.push({name: 'list-coupon'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }



    onMounted(() => {
        getCouponEdit()
    })


</script>
