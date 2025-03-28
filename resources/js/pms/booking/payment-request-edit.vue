<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Booking Payment Request (ID: {{ bookingDetail.booking_id }})</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{name: 'booking-list'}" class="btn rounded-pill btn-secondary-light">
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
            <template v-else>
                <div class="row">
                    <div class="col-12 col-md-3">
                        <h3 class="text-primary m-0">{{ bookingDetail.home_name }}</h3>
                    </div>
                    <div class="col-12 col-md-6 text-md-center">
                        <i class="bi bi-calendar2-check"></i> Check-in: <b>{{ bookingDetail.checkin_date }}</b> | <i class="bi bi-calendar2-check"></i> Check-out: <b>{{ bookingDetail.checkout_date }}</b>
                    </div>
                    <div class="col-12 col-md-3 text-md-end">
                        Total Amount: <b>INR {{ currFormat(bookingDetail.payable_amount) }}</b>
                    </div>
                    <div class="col-12 pt-3">
                        <div class="bg-secondary text-white py-2 px-3 text-center rounded-5">Total/Remaining:  <b>INR {{ currFormat(netPayableAmount) }}</b> / INR {{ currFormat(remainingAmount) }}</div>
                    </div>
                </div>
                <div v-if="remainingAmount > 0">
                    <div class="border pb-2 mt-4">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table align-middle mb-2">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Amount</th>
                                            <th>Payment Mode</th>
                                            <th class="text-secondary fw-semibold">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(obj, index) in rowCount" :key="index">
                                            <td>
                                                <div class="input-group">
                                                    <Field
                                                        class="form-control mw-150"
                                                        type="text"
                                                        rules="required"
                                                        name="customer_name[index]"
                                                        v-model="customer_name[index]"
                                                        placeholder = "Enter Name"
                                                    >
                                                    </Field>
                                                </div>
                                                <span class="text-danger">{{ customer_name_errors[index] }}</span>
                                            </td>
                                            <td>
                                                <Field
                                                    class="form-control mw-150"
                                                    type="text"
                                                    rules="required"
                                                    name="emails[index]"
                                                    v-model="emails[index]"
                                                    placeholder = "Enter Email ID"
                                                >
                                                </Field>
                                                <span class="text-danger">{{ email_errors[index] }}</span>
                                            </td>
                                            <td>
                                                <div class="input-group flex-nowrap">
                                                    <div class="input-group-text">INR</div>
                                                    <Field
                                                        class="form-control mw-100"
                                                        type="text"
                                                        rules="required"
                                                        name="amounts[index]"
                                                        v-model="amounts[index]"
                                                    >
                                                    </Field>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <Field
                                                        class="form-control mw-150"
                                                        as="select"
                                                        rules="required"
                                                        name="paymentModes[index]"
                                                        v-model="paymentModes[index]"
                                                    >
                                                        <option value="" selected disabled>Choose Payment Mode</option>
                                                        <option
                                                            v-for="(obj, index) in paymentModeList"
                                                            :value="obj">
                                                            {{ obj }}
                                                        </option>
                                                    </Field>
                                                </div>
                                                <span class="text-danger">{{ payment_mode_errors[index] }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-save btn-primary" @click="paymentRequest(index)" v-if="!paymentRequestStatus[index] && isPaymentRequest">Save</button>
                                                <div v-else>
                                                    <span><i class="bi bi-pencil-square" @click="editPaymentRequest(index)"></i></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </section>
    </div>
</template>

<script setup>
    import placeholder from '@assets/images/no-img.jpg'
    import Pagination from '@components/pagination.vue'
    import axios from 'axios'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { useDebouncedRef } from '@utils/debouncedRef'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'
    import * as bootstrap from 'bootstrap'
    import { useRouter, useRoute } from 'vue-router'
    import { format, currFormat } from '@utils/common'
    const router = useRouter();
    const route = useRoute();
    const isLoading = ref(false);
    const bookingDetail = ref([]);
    const emails = ref([]);
    const amounts = ref([]);
    const counts = ref([]);
    const paymentRequestCount = ref();
    const rowCount = ref(0);
    const paymentModeList = ref(['Razorpay', 'NEFT', 'Cash']);
    const paymentModes = ref([]);
    const payment_mode_errors = ref([]);
    const email_errors = ref([]);
    const remainingAmount = ref(0.00);
    const customer_name = ref([]);
    const customer_name_errors = ref([]);
    const paymentRequestStatus = ref([]);
    const isPaymentRequest = ref(true);
    const netPayableAmount = ref(0.00);
    const isAddRowEnabled  = ref(false);
    const isRemoveRowEnabled = ref(true);
    const paymentRequestServerIds = ref([]);
    const propertyBookingPaymentRequest  = ref([]);

    const editPaymentRequest = (i) =>{
        paymentRequestStatus.value[i] = false;
        isPaymentRequest.value = true;
        isAddRowEnabled.value = false
    }

    const getBookingDetail = () => {
        isLoading.value = true
        if(route.params.id){
            let maxCount = 10
            for(let i =1; i<=maxCount; i++){
                counts.value.push(i);
                payment_mode_errors.value.push('');
                email_errors.value.push('');
            }
            isLoading.value = true
            axios.get(`/api/property/booking/request/detail/${route.params.id}`).then(res => {
                if(res.data.status){
                    propertyBookingPaymentRequest.value = res.data.propertyBookingPaymentRequest
                    rowCount.value = 1;
                    bookingDetail.value = res.data.data;
                    customer_name_errors.value.push('');

                    paymentRequestStatus.value.push(false);
                    remainingAmount.value = res.data.availableAmount;
                    netPayableAmount.value = res.data.data.payable_amount;
                    emails.value.push(propertyBookingPaymentRequest.value.email);
                    amounts.value.push(propertyBookingPaymentRequest.value.amount);
                    customer_name.value.push(propertyBookingPaymentRequest.value.name);
                    paymentModes.value.push(propertyBookingPaymentRequest.value.payment_mode);
                    propertyBookingPaymentRequest.value = res.data.propertyBookingPaymentRequest;
                    paymentRequestServerIds.value.push(propertyBookingPaymentRequest.value.booking_request_id);
                    if(remainingAmount.value <=0){
                        router.push({name: 'booking-list'})
                    }
                    setTimeout(() => {
                        isLoading.value = false;
                    }, 400)
                }
            }).catch(error => {
                isLoading.value = false;
            })
        }
    }

    const paymentRequest = (i)=>{
        email_errors.value[i] = emails.value[i] == ''?'Please Enter Email ID':'';
        payment_mode_errors.value[i] = paymentModes.value[i] == ''?'Please Choose Payment Mode':'';
        customer_name_errors.value[i] = customer_name.value[i] == ''?'Please Enter Name':'';
        let isValidAmount = false;
        if(amounts.value[i] <= remainingAmount.value ){
            isValidAmount  = true;
        }
        else{
            toast("Amount can't be greater than Net Payable Amount", "error").show();
        }
        if(customer_name.value[i] !='' && emails.value[i] !='' && paymentModes.value[i] !='' && isValidAmount && remainingAmount.value > 0){
            isLoading.value = true
            let reqParameters = {
               'email':emails.value[i],
               'amount':amounts.value[i],
               'property_booking_id':bookingDetail.value.id,
               'payment_mode':paymentModes.value[i],
               'name':customer_name.value[i],
               'booking_request_id':paymentRequestServerIds.value[i]
            };
            axios.post('/api/property/booking/payment/request', reqParameters).then(res => {
                if(res.data.status){
                    setTimeout(() => {
                        toast(res.data.message, 'success').show();
                        paymentRequestServerIds.value[i] = res.data.data.booking_request_id;
                        isLoading.value = false;
                        paymentRequestStatus.value[i] = true;
                        remainingAmount.value = res.data.propertyBookingDetail.payable_amount - res.data.propertyBookingDetail.paid_amount;
                        if(remainingAmount.value <=0){
                            isPaymentRequest.value = false;
                        }
                        if(remainingAmount.value <=0){
                            router.push({name: 'booking-list'})
                        }
                        isAddRowEnabled.value = true;
                        isRemoveRowEnabled.value = false;
                    }, 400)
                }
            }).catch(error => {
                toast(error.response.data.message, 'error').show()
                isLoading.value = false;
            })
        }
    }

    onMounted(() => {
        getBookingDetail()
    })
</script>
