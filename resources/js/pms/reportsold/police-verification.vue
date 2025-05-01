<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Police Verification Report</h1>
                </div>
                <div class="col-12">
                    <div class="search-filter">
                        <Form as="div" v-slot="{ errors, resetForm }">
                            <div class="row gy-3 gx-2">
                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="name"
                                            type="text"
                                            class="form-control"
                                            :class="{'border-danger': errors.name}"
                                            placeholder="Customer Name"

                                            v-model="name"
                                        />
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="email"
                                            type="text"
                                            class="form-control"
                                            :class="{'border-danger': errors.email}"
                                            placeholder="Email"

                                            v-model="email"
                                        />
                                    </div>
                                </div>


                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="mobile_no"
                                            type="text"
                                            class="form-control"
                                            :class="{'border-danger': errors.mobile_no}"
                                            placeholder="Mobile No."
                                            rules="numeric"
                                            v-model="mobile_no"
                                        />
                                    </div>
                                </div>

                                <!-- <div class="col-12 col-md-4 col-lg">
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
                                </div> -->

                                <div class="col-12 col-md-auto">
                                    <div class="btn-group gap-2">
                                        <button
                                            @click="getSaleReport"
                                            class="btn btn-primary btn-icon"
                                            :disabled="!name && !email  && !mobile_no">
                                            <span class="icon-search"></span>
                                        </button>
                                        <button
                                            @click="clearFilter(resetForm)"
                                            class="btn btn-icon btn-clear btn-warning">
                                            <span class="bi bi-arrow-clockwise"></span>
                                        </button>
                                        <button @click="exportExcel" class="btn btn-export btn-secondary"><i class="bi bi-file-earmark-excel me-2 fs-6"></i>Export</button>
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

                                        <th>Customer Name</th>
                                        <th>Email Id</th>
                                        <th>Mobile No.</th>
                                        <th>Checkin Date</th>
                                        <th>Checkout Date</th>
                                    </tr>
                                </thead>
                                <tbody v-for="(obj, index) in list" :key="index">
                                    <tr>
                                        <td>{{ obj.guest_name }}</td>
                                        <td>{{ obj.guest_email_id }}</td>
                                        <td>{{ obj.guest_mobile_no }}</td>
                                        <td>{{ obj.checkin_date }}</td>
                                        <td>{{ obj.checkout_date }}</td>
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
    import { dateRangeFormat, currFormat } from '@utils/common'
    import dayjs from 'dayjs'
    import * as XLSX from 'xlsx'

    const store = inject('store');
    const role = store.getters.user?.role;
    const userId = store.getters.user?.id;
    const isLoading = ref(false)

    const list = ref([])
    const checkInDate = ref([])
    const checkOutDate = ref()
    const type = ref()

    const searchDateRange  = ref()
    const last_page = ref(0)
    const name = ref()
    const email = ref()
    const mobile_no = ref()

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
            if(searchDateRange.value){
                checkin_date = dayjs(searchDateRange.value[0]).format('YYYY-MM-DD');
                checkout_date = dayjs(searchDateRange.value[1]).format('YYYY-MM-DD');
            }
            axios.post('/api/police/verification/report', {
                name:name.value,
                email:email.value,
                mobile_no:mobile_no.value,

            }).then(res => {
                if(res.data.status){
                    list.value = res.data.data
                    dataRow.value = res.data.data
                    propertyList.value = res.data.proeprtyList
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

    const exportExcel = () => {
        let checkin_date = '';
        let checkout_date = '';
        if(searchDateRange.value){
            checkin_date = dayjs(searchDateRange.value[0]).format('YYYY-MM-DD');
            checkout_date = dayjs(searchDateRange.value[1]).format('YYYY-MM-DD');
        }
        let reqParameters = {

        };
        axios.post(`/api/police/verification/report/export`, {
                name:name.value,
                email:email.value,
                mobile_no:mobile_no.value,

            }, { responseType: 'blob' }).then(response => {
            console.log(response.data)
            const blob = new Blob([response.data], { type: 'application/octet-stream' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'policy-verification-report.xlsx');
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
