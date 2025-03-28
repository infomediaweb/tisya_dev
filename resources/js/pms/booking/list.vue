<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Booking List</h1>
                </div>
                <div class="col-3" v-if="role=='Owner'">
                    <div class="form-group mb-0">
                        <Field
                            name="occupancyDateRange"
                            v-model="occupancyDateRange"

                            v-slot="{ field }"
                            >
                            <DatePicker
                               input-class-name="form-control"
                                ref="occupancyDateRangeRef"
                                v-bind="field"
                                v-model="occupancyDateRange"
                                :enable-time-picker="false"
                                placeholder="Occupancy"
                                :format="dateRangeFormat"
                                auto-apply
                                range multi-calendars
                                :disabledDates="disableDatesArray"
                                aria-readonly="true"

                            />
                            </Field>
                    </div>
                </div>
                <div class="col text-end">
                    <router-link :to="{name: 'by-location'}" class="btn rounded-pill btn-secondary-light" v-if="role=='Admin' || role=='Reservations' || role=='Finance'">
                        <i class="bi bi-plus-lg fs-6 lh-1 me-2"></i>
                        New Booking
                    </router-link>
                </div>
                <div class="col-12">
                    <div class="search-filter">
                        <Form as="div" v-slot="{ errors, resetForm }">
                            <div class="row gy-3 gx-2">
                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="booking_id"
                                            type="text"
                                            class="form-control"
                                            :class="{'border-danger': errors.booking_id}"
                                            placeholder="Booking ID"
                                            rules="numeric"
                                            v-model="searchBookingId"
                                        />
                                    </div>
                                </div>
                                <!-- <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="created_by"
                                            as="select"
                                            class="form-control"
                                            v-model="searchFromCreatedBy">
                                            <option value="" selected disabled>Created By</option>
                                            <option
                                                v-for="(obj, index) in users"
                                                :key="index"
                                                :value="obj.id">
                                                {{ obj.name }}
                                            </option>
                                        </Field>
                                    </div>
                                </div> -->

                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="property_name"
                                            as="select"
                                            class="form-control"
                                            v-model="searchPropertyId">
                                            <option value="" selected disabled>Property Name</option>
                                            <option
                                                v-for="(obj, index) in propertyList"
                                                :key="index"
                                                :value="obj.id">
                                                {{ obj.home_name }}
                                            </option>
                                        </Field>
                                    </div>
                                </div>


                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="type"
                                            as="select"
                                            class="form-control"
                                            v-model="type">
                                            <option value="" selected disabled>Type</option>
                                            <option value="checkin">Check In</option>
                                            <option value="checkout">Check Out</option>
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="searchDateRange"
                                            v-model="searchDateRange"
                                            v-slot="{ field }"
                                            >
                                            <DatePicker
                                                ref="searchDateRangeRef"
                                                v-bind="field"
                                                v-model="searchDateRange"
                                                :enable-time-picker="false"
                                                placeholder="From-To"
                                                :format="dateRangeFormat"
                                                auto-apply
                                                range multi-calendars
                                                :input-class-name="classDanger ? 'form-control border-danger' : 'form-control'"
                                            />
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="payment-status"
                                            as="select"
                                            class="form-control"
                                            v-model="searchPaymentStatus">
                                            <option value="" selected disabled>Payment Status</option>
                                            <option value="paid">Paid</option>
                                            <option value="pending">Pending</option>
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="booking_status"
                                            as="select"
                                            class="form-control"
                                            v-model="searchBookingStatus">
                                            <option value="" selected disabled>Booking Status</option>
                                            <option value="Confirmed">Confirmed</option>
                                            <option value="Not Confirmed">Not Confirmed</option>
                                            <option value="Canceled">Canceled</option>
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="channel"
                                            as="select"
                                            class="form-control"
                                            v-model="searchChannel">
                                            <option value="" selected disabled>Channel</option>
                                            <option value="Agoda">Agoda</option>
                                            <option value="Airbnb">Airbnb</option>
                                            <option value="Booking.com">Booking.com</option>
                                            <option value="MakeMyTrip">MakeMyTrip</option>
                                            <option value="PMS">PMS</option>
                                            <option value="Website">Website</option>
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-auto">
                                    <div class="btn-group gap-2">
                                        <button
                                            @click="getBookingList"
                                            class="btn btn-primary btn-icon"
                                            :disabled="!searchBookingId && !searchPropertyId && !checkInDate && !checkOutDate  && !searchPaymentStatus && !searchBookingStatus && !searchChannel  && !searchPaymentStatus && !searchFromCreatedBy || errors.booking_id">
                                            <span class="icon-search"></span>
                                        </button>
                                        <button
                                            @click="clearFilter(resetForm)"
                                            class="btn btn-icon btn-clear btn-warning">
                                            <span class="bi bi-arrow-clockwise"></span>
                                        </button>
                                        <button @click="exportBookingExcel" class="btn btn-export btn-secondary"><i class="bi bi-file-earmark-excel me-2 fs-6"></i>Export</button>
                                    </div>
                                </div>
                            </div>
                        </Form>
                    </div>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>
            <template v-else>
                <div class="outer-wrapper" v-if="list.length">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-list-2 align-middle mb-0 mw-lg">
                                <thead>
                                    <tr>
                                        <th width="30"></th>
                                        <th>Booking ID</th>
                                       <!--  <th>Created By</th> -->
                                        <th>Guest Detail</th>
                                        <th>Booking Detail</th>
                                        <th>Property Name</th>
                                        <th>Channel</th>
                                        <th>Price</th>
                                        <th nowrap>Payment Status</th>
                                        <th>Booking Status</th>
                                        <!-- <th align="center">Invoice</th> -->
                                        <th width="125" v-if="role=='Admin' || role=='Finance' ||  role =='Front Office' ||  role =='Reservations'">Action</th>
                                    </tr>
                                </thead>
                                <tbody v-for="(obj, index) in list" :key="index">
                                    <tr class="valignTop">
                                        <td v-if="obj.booking_from != 'ru'">
                                            <a href="javascript:void(0)"  class="bookingDetail fw-bold" @click="bookinToggle(index)">
                                                <i class="bi bi-chevron-up fw-bold fs-6" v-if="index==activeRowIndex"></i>
                                                <i class="bi bi-chevron-down fw-bold fs-6"  v-else></i>
                                            </a>
                                        </td>
                                        <td v-else></td>
                                        <td nowrap>
                                            <h6 class="m-0">{{ obj.booking_id }}</h6>
                                            <!-- <small><i class="bi bi-calendar2-check"></i> 01 May 2024 <i class="bi bi-clock"></i> 12:05 PM</small> -->
                                            <small><i class="bi bi-calendar2-check"></i> {{ obj.created_at }}</small>
                                        </td>
                                       <!--  <td nowrap>
                                           {{ obj.user?.role }}
                                        </td> -->
                                        <td nowrap>
                                            <h6 class="m-0">{{ obj.customer_detail.first_name }} {{ obj.customer_detail.last_name }}</h6>
                                            <small>{{ obj.customer_detail.mobile_number }} | {{ obj.customer_detail.email }}</small>
                                        </td>
                                        <td nowrap>
                                            <i class="bi bi-calendar2-check"></i> Check-in: {{ obj.checkin_date }} | Check-out: {{ obj.checkout_date }}<br>
                                            <i class="bi bi-moon"></i> No. of nights: {{ noOfNights(obj.checkin_date, obj.checkout_date) }}<br>
                                            <i class="bi bi-people"></i> No. of guests: {{ obj.no_of_adult }} Adult<span v-if="obj.no_of_adult >1">s</span> | {{ obj.no_of_children }} Children
                                        </td>
                                        <td>{{ obj.home_name }}</td>
                                        <td>{{ obj.channel }}</td>
                                        <td nowrap>
                                            <b>INR {{ currFormat(obj.payable_amount) }}</b>
                                            <div class="text-danger" v-if="obj.payable_amount > obj.paid_amount && obj.booking_from !='ru'"><i class="bi bi-exclamation-diamond-fill"></i> <b>INR {{ currFormat(obj.payable_amount - obj.paid_amount) }}</b></div>
                                        </td>
                                        <td nowrap v-if="obj.booking_from != 'ru'">
                                            <h6 class="text-success" v-if="obj.booking_status=='Paid' || obj.booking_status=='paid'"><i class="bi bi-patch-check-fill"></i> <span>{{ obj.booking_status }}</span></h6>
                                            <h6 class="text-danger" v-else><i class="bi bi-exclamation-diamond-fill"></i> <span>{{ obj.booking_status }}</span></h6>
                                        </td>
                                        <td v-else>
                                            <i class="bi bi-patch-check-fill"></i> <span>Paid</span>
                                        </td>

                                        <td class="bookingRow bg-22" v-if="obj.property_booking_status=='Confirmed'">
                                            {{ obj.property_booking_status }}
                                        </td>
                                        <td class="bookingRow bg-11" v-else>
                                            {{ obj.property_booking_status }}
                                        </td>
                                        <!-- <td align="center">
                                            <div v-if="obj.invoice_download_loader" class="spinner-border spinner-border-sm" role="status"></div>
                                            <button
                                                v-else
                                                @click.prevent="downloadInvoice(index, obj.id, obj.booking_id)"
                                                class="btn p-1 fs-5 text-black">
                                                <i class="bi bi-download"></i>
                                            </button>
                                        </td> -->
                                        <td v-if="role=='Admin' || role=='Reservations' ||  role =='Front Office' || role=='Finance'">
                                            <ul class="action-btn-group mb-0 mw-0" v-if="obj.booking_from != 'ru'">
                                                <li v-if="(obj.payable_amount > obj.paid_amount) && (role=='Admin' || role=='Finance') && obj.property_booking_status !='Canceled'" >
                                                    <router-link :to="{name:'booking/payment-request', params:{ id: obj.id }}" class="btn btn-sm btn-save btn-primary">
                                                        Payment Request
                                                    </router-link>
                                                </li>
                                                <li v-if="(obj.booking_status == 'Paid' || obj.booking_status == 'paid' && role=='Admin' || role=='Finance' ||  role =='Front Office' || role=='Reservations') && !obj.invoice && obj.property_booking_status !='Canceled'">
                                                    <router-link :to="{name:'booking/checkin', params:{ id: obj.id }}" class="btn btn-sm btn-save btn-info">
                                                        Check In
                                                    </router-link>
                                                </li>
                                                <li v-if="obj.invoice && obj.property_booking_status !='Canceled'">
                                                    <a :href="url(obj)" class="btn btn-sm btn-save btn-info" target="blank">
                                                        Download Invoice
                                                    </a>
                                                </li>
                                                <li v-if="role !='Reservations' && role !='Front Office' && obj.property_booking_status !='Canceled'">
                                                    <router-link
                                                        :to="{name: obj.type == 'Location' ? 'by-location' : 'by-property', params: { id: obj.id }}"
                                                        class="btn ps-0 p-1 fs-5 text-black">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </router-link>
                                                </li>
                                                <li  class="pt-1" v-if="role=='Admin' && obj.booking_from != 'ru' && obj.property_booking_status != 'Canceled'">
                                                    <a class="btn btn-success btn-save btn-sm bg-danger" @click="cancelBooking(obj.id)">Cancel</a>
                                                </li>
                                                <li v-if="role=='Admin' || role=='Finance'">
                                                    <button  @click.prevent="deleteItem(obj.id)" class="btn p-1 fs-5 text-black">
                                                        <i class="icon-bi bi-trash"></i>
                                                    </button>
                                                </li>
                                            </ul>
                                            <ul  class="action-btn-group mb-0 mw-0" v-else>
                                                <li v-if="(role=='Admin' || role=='Finance') && !obj.invoice">
                                                    <router-link :to="{name:'booking/checkin', params:{ id: obj.id }}" class="btn btn-sm btn-save btn-info">
                                                        Check In
                                                    </router-link>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr v-if="index == activeRowIndex && role=='Admin' || role=='Finance' || role=='Front Office'">
                                        <td colspan="10" v-if="obj.payment_requests.length != 0" class="p-0" style="background: #F1F2E3 !important;">
                                            <table class="table bg-transparent toggleTbl mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="32"></th>
                                                        <th width="228">Payment Req ID</th>
                                                        <th>Person Detail</th>
                                                        <th nowrap >Amount</th>
                                                        <th>Payment Mode</th>
                                                        <th>Status</th>
                                                        <th v-if="role=='Admin' || role=='Finance'">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(paymentRequestObj, rindex) in obj.payment_requests" :key="rindex" class="bookingRow">
                                                        <td></td>
                                                        <td>{{ paymentRequestObj.booking_request_id }}</td>
                                                        <td>
                                                            {{ paymentRequestObj.name }} (<small>{{ paymentRequestObj.email }}</small>)
                                                        </td>
                                                        <td nowrap ><b>INR {{ currFormat(paymentRequestObj.amount) }}</b></td>
                                                        <td>{{ paymentRequestObj.payment_mode }}</td>
                                                        <td>{{ paymentRequestObj.booking_request_status }}</td>
                                                        <td v-if="role=='Admin' || role=='Finance'">
                                                            <router-link :to="{name:'booking/payment-request-edit', params:{ id: paymentRequestObj.id }}" v-if="paymentRequestObj.booking_request_status=='Pending'" class="btn btn-sm btn-save btn-primary me-2">
                                                                Edit
                                                            </router-link>
                                                            <a href="javascript:void(0)" class="btn btn-sm btn-save btn-dark"  data-bs-toggle="modal" data-bs-target="#updatePaymentRequestModal" @click.prevent="updatePaymentRequest(paymentRequestObj, rindex)">Change Status</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td colspan="9" style="background: #F1F2E3 !important;" v-else>
                                            <span class="text-danger">No Payment Request Found!</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal" id="updatePaymentRequestModal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <a href="javascript:void(0)" class="closeModel" @click="closeModal"><i class="bi bi-x-lg fs-5"></i></a>
                                    <h5 class="modal-title text-white" id="exampleModalLabel" >Update Payment Status</h5>
                                </div>
                                <div class="modal-body p-3">
                                    <div class="row">
                                        <h4>Payment Req ID - {{ bookinReqServerID }}</h4>
                                        <h4>Amount - INR {{ currFormat(paymentReqAmount) }}</h4>
                                        <div class="col-12 mt-2">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <div class="row gy-4">
                                                            <div class="col-12">
                                                                <label for="">Payment Status<span class="text-danger">*</span></label>
                                                                <Field
                                                                    class="form-control"
                                                                    :class="payment_status_error_class"
                                                                    name="paymentStatus"
                                                                    as="select"
                                                                    rules="required"
                                                                    v-model="paymentStatus"
                                                                >
                                                                    <option value="" selected>Choose Status</option>
                                                                    <option
                                                                        v-for="(obj, statusi) in statusList"
                                                                        :value="obj">
                                                                        {{ obj }}
                                                                    </option>
                                                                </Field>
                                                            </div>
                                                        </div>

                                                        <div class="row gy-4 mt-1">
                                                            <div class="col-12">
                                                                <label for="">Note/Bank Detail<span class="text-danger">*</span></label>
                                                                <Field
                                                                    as="textarea"
                                                                    name="note"
                                                                    class="form-control"
                                                                    :class="note_error_class"
                                                                    rows="8"
                                                                    rules="required"
                                                                    v-model="note"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-1">
                                        <div class="col-12">
                                            <div class="form-group mb-0">
                                                <button class="btn btn-save btn-primary" @click="updatePaymentRequestNote">
                                                    <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                                    <span v-else>SUBMIT</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="table-footer pt-3">
                        <div class="row align-items-center">
                            <div class="col" v-if="dataRow.last_page > 1">
                                <Pagination
                                    :links="dataRow.links"
                                    @pagination="getBookingList"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="message text-center" v-else>
                    <h6 class="fw-bold">No Record Found</h6>
                </div>
            </template>
        </section>
    </div>
</template>
<style scoped>
    .toggleTbl th, .toggleTbl td{
        background: #F1F2E3 !important;
    }
    table{
        font-size: 12px !important;
    }
</style>
<script setup>
    //import * as bootstrap from 'bootstrap'
    import placeholder from '@assets/images/no-img.jpg'
    import Pagination from '@components/pagination.vue'
    import axios from 'axios'
    import { ref, onMounted, watch, nextTick, inject } from 'vue'
    import { useDebouncedRef } from '@utils/debouncedRef'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'
    import * as bootstrap from 'bootstrap'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { dateRangeFormat, currFormat } from '@utils/common'
    import dayjs from 'dayjs'
    import * as XLSX from 'xlsx'

    const store = inject('store');
    const role = store.getters.user?.role;
    const userId = store.getters.user?.id;

    const list = ref([])
    const dataRow = ref({})
    const propertyList = ref([])

    const checkedSelectAll = ref(false)
    const isSubmitLoading = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const vSearchQuery = useDebouncedRef('', 800)
    const isLoading = ref(false)
    const property_type = ref()
    const property_name = ref()
    const location = ref()
    const activeRowIndex = ref()

    // For get all location list
    const note = ref()
    const bookinReqServerID = ref()
    const bookinReqID = ref()
    const note_errors = ref()
    const payment_status_errors = ref()

    const note_error_class = ref()
    const payment_status_error_class = ref()

    const maini = ref()
    const childi = ref()
    const statusList =  ref(['Pending', 'Declined', 'Payment Received'])
    const paymentStatus = ref('')
    const paymentReqAmount = ref(0.00)
    const modal = ref()


    const currentDay = dayjs()
    const checkInDate = ref([])
    const checkOutDate = ref()
    const checkOutMinDate = ref()
    const checkInDateRef = ref()
    const checkOutDateRef = ref()

    const type = ref()


    // Search Model
    const searchBookingId = ref()
    const searchPropertyId = ref()
    const searchPaymentStatus = ref()
    const searchBookingStatus = ref()
    const searchChannel = ref()
    const searchDateRange = ref()
    const classDanger = ref(false)
    const occupancyDateRange = ref()
    const disableDatesArray = ref()

    const searchFromCreatedBy =ref()
    const users = ref([])

    const userRole = ref('');
    const roleId = ref('');

    const url = (obj)=>{
       return 'https://tisyastays.rentals.management/storage/invoice/'+obj.invoice
    }

    const clearFilter = (resetForm) => {
        searchBookingId.value = ''
        searchPropertyId.value = ''
        searchPaymentStatus.value = ''
        searchBookingStatus.value = ''
        searchFromCreatedBy.value = ''
        resetForm()
        nextTick(() => {
            getBookingList()
        })
    }


    const closeModal = ()=>{
        bootstrap.Modal.getInstance(document.querySelector('#updatePaymentRequestModal')).hide()
    }

    const noOfNights = (checkinDate, checkoutDate)=>{
        return dayjs(checkoutDate).diff(dayjs(checkinDate), 'day')
    }

    const parseCustDetail = (obj) =>{
        return JSON.parse(obj)
    }

    const changeStatus = (i, paymentRequestObj) => {
        let reqParameters = {
            'booking_request_status':paymentStatus.value,
            'id':paymentRequestObj.id
        };
        axios.post('/api/property/booking/payment/request/status/update', reqParameters).then(res => {
            if(res.data.status){
                setTimeout(() => {
                    list.value[maini.value].payment_requests[i].booking_request_status = paymentStatus.value;
                    checkBookingPaymentStatus(maini.value, list.value[maini.value])
                    paymentStatus.value = ''
                    toast(res.data.message, 'success').show();
                    isSubmitLoading.value = false
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }

    const checkBookingPaymentStatus = (i, obj)=>{
        // let status = 'paid';
        // let totPaidAmount = 0;
        // if(obj.payment_requests.length !=0){
        //     obj.payment_requests.forEach(element => {
        //         if(element.booking_request_status != 'Payment Received'){
        //             status = 'pending';
        //         }
        //         totPaidAmount +=element.amount
        //     });
        //     if(obj.payable_amount> totPaidAmount){
        //         status = 'pending';
        //     }
        // }
        // else{
        //     status = 'pending';
        // }
        // list.value[i].booking_status = status;
    }


    const downloadInvoice = (idx, id, bookingId) => {
        list.value[idx].invoice_download_loader = true

        axios.get(`/api/boooking-invoice/${id}`, { responseType: 'blob' }).then(res => {
            const blob = new Blob([res.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob)

            const link = document.createElement('a')
            link.href = url

            link.setAttribute('download', `invoice-${bookingId}.pdf`)

            document.body.appendChild(link)
            link.click()

            document.body.removeChild(link)
            window.URL.revokeObjectURL(url)

            setTimeout(() => {
                list.value[idx].invoice_download_loader = false
            }, 400)

        }).catch(error => {
            console.log(error)
        })
    }

    const showProperty = (obj) =>{
        property_type.value = obj.home_type;
        property_name.value = obj.home_name;
        location.value = obj.location+', '+obj.state;
    }

    const updatePaymentRequest = (obj, c)=>{
        bookinReqServerID.value = obj.booking_request_id
        bookinReqID.value = obj.id
        note.value = obj.note
        childi.value = c
        paymentReqAmount.value = obj.amount
        paymentStatus.value = obj.booking_request_status
    }

    const getBookingList = (pageNumber) => {
        userRole.value = store.getters.user.role
        roleId.value = store.getters.user.id
        isLoading.value = true
        let checkin_date = '';
        let checkout_date = '';
        if(type.value  && searchDateRange.value == undefined ){
            classDanger.value = true

            isLoading.value = false
            return false;
        }
        else{

            if(searchDateRange.value){
                checkin_date = dayjs(searchDateRange.value[0]).format('YYYY-MM-DD');
                checkout_date = dayjs(searchDateRange.value[1]).format('YYYY-MM-DD');
            }
            axios.get('/api/property/booking/list/test/'+role+'/'+userId, {
                params: {
                    page: pageNumber,
                    booking_id: searchBookingId.value,
                    property_id: searchPropertyId.value,
                    payment_status: searchPaymentStatus.value,
                    booking_status: searchBookingStatus.value,
                    checkin_date: checkin_date,
                    checkout_date: checkout_date,
                    channel: searchChannel.value,
                    type: type.value,
                    role:role.value,
                    userId:userId.value,
                    created_by:searchFromCreatedBy.value,
                }
            }).then(res => {
                if(res.data.status){
                    list.value = res.data.data.data
                    dataRow.value = res.data.data
                    propertyList.value = res.data.proeprtyList
                    disableDatesArray.value = res.data.disableDatesArray
                    users.value = res.data.users
                    list.value.forEach((element,i) => {
                        checkBookingPaymentStatus(i, element)
                    });
                    setTimeout(() => {
                        vCheckAll.value = false
                        checkedSelectAll.value = false
                        vSelectCheckValue.value = []
                        isLoading.value = false
                    }, 400)
                }
            }).catch(error => {
                isLoading.value = false
            })

        }
    }


    // For Search Query
    watch(vSearchQuery, (newVal, oldVal) => {
        isLoading.value = true
    })

    // For check all item selected or not
    watch(vSelectCheckValue, (newVal, oldVal) => {
        if(newVal.length != selectCheckRef?.value?.length){
            vCheckAll.value = false
        }
        else{
            if(newVal.length){
                vCheckAll.value = true
            }
        }
    })

    const bookinToggle = (l) =>{
        maini.value = l;
        if(activeRowIndex.value != l){
            activeRowIndex.value = l;
        }
        else{
            activeRowIndex.value = -1;
        }
    }

    const updatePaymentRequestNote = (i)=>{
        note_error_class.value = note.value == '' || note.value == null?'border-danger':'';
        payment_status_error_class.value = paymentStatus.value == ''?'border-danger':'';
        if(note.value !='' && paymentStatus.value !=''){
            isSubmitLoading.value = true
            let reqParameters = {
               'note':note.value,
               'booking_request_status':paymentStatus.value,
               'id':bookinReqID.value
            };
            axios.post('/api/property/booking/payment/request/update', reqParameters).then(res => {
                if(res.data.status){
                    setTimeout(() => {
                        list.value[maini.value].payment_requests[childi.value].note = note.value;
                        list.value[maini.value].payment_requests[childi.value].booking_request_status = paymentStatus.value;
                        list.value[maini.value].booking_status = res.data.status;
                        toast(res.data.message, 'success').show();
                        bootstrap.Modal.getInstance(document.querySelector('#updatePaymentRequestModal')).hide();
                        isSubmitLoading.value = false
                    }, 400)
                }
            }).catch(error => {
                toast(error.response.data.message, 'error').show()
                isSubmitLoading.value = false

            })
        }
    }

    const deleteItem = (v) => {
        dialog('Are you sure you want to delete?', confirm).show()

        function confirm(){
            axios.delete(`/api/delete-booking/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    nextTick(() => {
                        getBookingList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }
    }

    const exportBookingExcel = () => {
        let checkin_date = '';
        let checkout_date = '';
        if(searchDateRange.value){
            checkin_date = dayjs(searchDateRange.value[0]).format('YYYY-MM-DD');
            checkout_date = dayjs(searchDateRange.value[1]).format('YYYY-MM-DD');
        }
        let reqParameters = {
            booking_id: searchBookingId.value,
            property_id: searchPropertyId.value,
            payment_status: searchPaymentStatus.value,
            booking_status: searchBookingStatus.value,
            checkin_date: checkin_date,
            checkout_date: checkout_date,
            channel: searchChannel.value,
            created_by:searchFromCreatedBy.value,
            //role:role.value,
           // userId:userId.value,

           role: userRole.value,
           roleId:roleId.value
        };
        axios.post(`/api/booking/export` , reqParameters, { responseType: 'blob' }).then(response => {
            const blob = new Blob([response.data], { type: 'application/octet-stream' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'booking.xlsx');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }).catch(error => {
            console.log(error)
        })
    }


    const cancelBooking = (id)=>{
        dialog('Are you sure you want to cancel this booking?', confirm).show()

        function confirm(){
            axios.get(`/api/cancel/booking/${id}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    nextTick(() => {
                        getBookingList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }

    }


    onMounted(() => {
        getBookingList()
    })


</script>


