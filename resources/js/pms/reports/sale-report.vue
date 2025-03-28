<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Sale Report</h1>
                </div>


                <div class="col-12">
                    <div class="search-filter">
                        <Form as="div" v-slot="{ errors, resetForm }">
                            <div class="row gy-3 gx-2">
                                <div class="col-12 col-md-4 col-lg">
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
                                            name="searchtype"
                                            as="select"
                                            class="form-control"
                                            v-model="searchtype">
                                            <option value="" selected disabled>Type</option>
                                            <option value="checkin">Check In</option>
                                            <option value="BookingDate">Booking Date</option>
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg">
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

                                <div class="col-12 col-md-4 col-lg">
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
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="payment_status"
                                            as="select"
                                            class="form-control"
                                            v-model="searchPaymentStatus">
                                            <option value="" selected disabled>Payment Status</option>
                                            <option value="Confirmed">Confirmed</option>
                                            <option value="Canceled">Canceled</option>
                                            
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-auto">
                                    <div class="btn-group gap-2">
                                        <button
                                            @click="getSaleReport"
                                            class="btn btn-primary btn-icon"
                                            :disabled=" !searchPropertyId && !searchDateRange  &&  !searchChannel && !searchPaymentStatus">
                                            <span class="icon-search"></span>
                                        </button>
                                        <button
                                            @click="clearFilter(resetForm)"
                                            class="btn btn-icon btn-clear btn-warning">
                                            <span class="bi bi-arrow-clockwise"></span>
                                        </button>
                                        <button @click="exportSaleReport" class="btn btn-export btn-secondary"><i class="bi bi-file-earmark-excel me-2 fs-6"></i>Export</button>
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
                                        <th>Booking ID</th>
                                        <th>Property Name</th>
                                        <th>Location</th>
                                        <th>Guest Name</th>
                                        <th>Channel</th>
                                        <th>Price</th>

                                        <th>Paid</th>
                                        <th>Tax</th>
                                        <th>Tax Amount</th>
                                    </tr>
                                </thead>
                                <tbody v-for="(obj, index) in list" :key="index">
                                    <tr>
                                        <td>{{ obj.booking_id }}</td>
                                        <td>{{ obj.home_name }}</td>
                                        <td>{{ obj.location }}</td>
                                        <td>{{ obj.guest_name }}</td>
                                        <td>{{ obj.channel }}</td>
                                        <td> {{ obj.payable_amount }}</td>

                                        <td> {{ obj.payment_received }}</td>
                                        <td>{{ obj.tax }}</td>
                                        <td>{{ obj.taxable_amount }}</td>
                                    </tr>
                                </tbody>
                            </table>
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
    import { format, currFormat, currFormatTotal, dateRangeFormat } from '@utils/common'
    import dayjs from 'dayjs'
    import * as XLSX from 'xlsx'


    const store = inject('store');
    const role = store.getters.user?.role;
    const userId = store.getters.user?.id;
    const isLoading = ref(false)


    const list = ref([])
    const checkInDate = ref('')
    const checkOutDate = ref('')
    const type = ref()
    const searchtype = ref('')

    const last_page = ref(0)
    const propertyList =ref([])
    const searchPropertyId = ref('')

    const checkOutDateRef = ref()

    const checkOutMinDate = ref('')
    const searchChannel = ref('')
    const searchPaymentStatus = ref('')

    const searchDateRange = ref()


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

    const getSaleReport = (pageNumber) => {
        isLoading.value = true
        let checkin_date = '';
        let checkout_date = '';
        if(type.value  && searchDateRange.value == undefined ){
            classDanger.value = true

            isLoading.value = false
            return false;
        }
        else{


            axios.post('/api/sale/report/'+role+'/'+userId, {
                checkin_date: searchDateRange.value?dayjs(searchDateRange.value[0]).format('YYYY-MM-DD'):'',
                checkout_date: searchDateRange.value?dayjs(searchDateRange.value[1]).format('YYYY-MM-DD'):'',
                searchPropertyId:searchPropertyId.value,
                searchChannel:searchChannel.value,
                searchPaymentStatus:searchPaymentStatus.value,
                searchtype: searchtype.value,
                role:role.value,
                userId:userId.value,
            }).then(res => {
                if(res.data.status){

                    propertyList.value = res.data.propertyList
                    list.value = res.data.data
                    dataRow.value = res.data.data



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

    const exportSaleReport = () => {
        axios.post(`/api/get/booking/sale/report/export`, {
                checkin_date: searchDateRange.value?dayjs(searchDateRange.value[0]).format('YYYY-MM-DD'):'',
                checkout_date: searchDateRange.value?dayjs(searchDateRange.value[1]).format('YYYY-MM-DD'):'',
                searchPropertyId:searchPropertyId.value,
                searchChannel:searchChannel.value,
                searchtype: searchtype.value,
                searchPaymentStatus:searchPaymentStatus.value,
                role:role.value,
                userId:userId.value,
            },
            {
                responseType: 'blob'

            }).then(response => {
                const blob = new Blob([response.data], { type: 'application/octet-stream' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'sale-report.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
        }).catch(error => {
            // console.log(error)
        })
    }


    const clearFilter = (resetForm) => {
        resetForm()
        nextTick(() => {
            getSaleReport()
        })
    }

    onMounted(() => {
        getSaleReport()
    })


</script>
