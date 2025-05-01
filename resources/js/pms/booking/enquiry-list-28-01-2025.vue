<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Booking Enquiry List</h1>
                </div>
                <div class="col-12 col-lg-4">
                    <!-- <form action="">
                        <div class="input-group input-icon">
                            <span class="icon-search"></span>
                            <input
                                type="text"
                                class="form-control form-control-sm"
                                v-model="vSearchQuery"
                                placeholder="Search by name"
                            >
                        </div>
                    </form> -->
                </div>
            </div>
        </div>

        <section class="section">

            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <template v-else>
                <div class="outer-wrapper" v-if="rowArray.length">
                    <div class="table-wrap" >
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 mw-lg">
                                <thead>
                                    <tr>
                                        <th width="35%" class="text-secondary fw-semibold">Property Name</th>
                                        <th class="text-secondary fw-semibold">Booking Detail</th>
                                        <th width="15%" class="text-secondary fw-semibold">Price</th>
                                        <th width="15%" class="text-secondary fw-semibold">Name</th>
                                        <th width="15%" class="text-secondary fw-semibold">Email</th>
                                        <th width="15%" class="text-secondary fw-semibold">Mobile No.</th>
                                        <th width="15%" class="text-secondary fw-semibold">Enquiry Message</th>
                                        <th style="width:90px;" class="text-secondary fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(obj, index) in rowArray">
                                        <td>{{ obj.property.home_name }}</td>
                                        <td>
                                            <td nowrap>
                                            <i class="bi bi-calendar2-check"></i> Check-in: {{ obj.checkin_date }} <br>
                                            <i class="bi bi-calendar2-check"></i> Check-out: {{ obj.checkout_date }}<br>
                                            <i class="bi bi-moon"></i> No of Nights: {{ obj.no_of_night }}<br>
                                            <i class="bi bi-people"></i> No of Guests: {{ obj.no_of_guest }}
                                        </td>
                                        </td>
                                        <td><b>INR {{ currFormat(obj.total_amount) }}</b></td>
                                        <td>{{ obj.name }}</td>
                                        <td>{{ obj.email }}</td>
                                        <td>{{ obj.phone_no }}</td>
                                        <td class="text-center"><a href="javascript:void(0)" class="btn btn-sm btn-save btn-dark"  data-bs-toggle="modal" data-bs-target="#viewEnquiryModal" @click.prevent="viewEnquiry(obj)">View</a></td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-footer pt-3">
                        <div class="row align-items-center">
                            <!-- <div class="col-auto">
                                <button
                                    class="btn btn-save btn-primary"
                                    @click="deleteMultiItem"
                                    :disabled="(!vCheckAll && vSelectCheckValue.length == 0) || vSelectCheckValue.length == 0 || dataRow.total == 0">
                                    Delete Selected
                                </button>
                            </div> -->
                            <div class="col" v-if="dataRow.last_page > 1">
                                <Pagination
                                    :links="dataRow.links"
                                    @pagination="getList"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="message text-center" v-else>
                    <h6 class="fw-bold">No Record Found</h6>
                </div>

                <div class="modal" id="viewEnquiryModal">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <a href="javascript:void(0)" class="closeModel" @click="closeModal"><i class="bi bi-x-lg fs-5"></i></a>
                                    <h6 class="modal-title text-white" id="exampleModalLabel" >Enquiry Message</h6>
                                </div>
                                <div class="modal-body p-3">
                                    <div class="row">
                                        <p>{{ enquiryDetail }}</p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
            </template>
        </section>
    </div>
</template>
<script setup>
    import Pagination from '@components/pagination.vue'
    import axios from 'axios'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { useDebouncedRef } from '@utils/debouncedRef'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'
    import { format, currFormat } from '@utils/common'
    import * as bootstrap from 'bootstrap'
    const rowArray = ref([])
    const dataRow = ref({})
    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const vSearchQuery = useDebouncedRef('', 800)
    const isLoading = ref(false)
    const enquiryDetail = ref('')

    const viewEnquiry = (obj) =>{
        enquiryDetail.value = obj.enquiry_message
    }

    // For get all data list
    const getList = (pageNumber) => {
        isLoading.value = true
        axios.get('/api/property/bookings/enquiry').then(res => {
            if(res.data.status){
                console.log(res.data)
                rowArray.value = res.data.data
                dataRow.value = res.data.data
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

    // For Search Query
    watch(vSearchQuery, (newVal, oldVal) => {
        isLoading.value = true
        axios.get(`/api/company`, {
            params: {
                search: newVal
            }
        }).then(res => {
            if(res.data.status){
                rowArray.value = res.data.data.data
                dataRow.value = res.data.data

                setTimeout(() => {
                    vCheckAll.value = false
                    checkedSelectAll.value = false
                    vSelectCheckValue.value = []
                    isLoading.value = false
                }, 200)
            }
        }).catch(error => {
            console.log(error)
        })
    })

    const closeModal = ()=>{
        bootstrap.Modal.getInstance(document.querySelector('#viewEnquiryModal')).hide()
    }

    // For check all list
    const onCheckAll = () => {
        if(vCheckAll.value){
            checkedSelectAll.value = true
            vSelectCheckValue.value = []

            selectCheckRef.value.forEach(v => {
                vSelectCheckValue.value.push(v.value)
            })
        }
        else{
            checkedSelectAll.value = false
            vSelectCheckValue.value = []
        }
    }

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

    // For delete single item
    const deleteItem = (v) => {
        dialog('Are you sure you want to delete?', confirm).show()

        function confirm(){
            axios.delete(`/api/company/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    nextTick(() => {
                        getList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }
    }

    // For delete multi list items
    const deleteMultiItem = () => {
        dialog('Are you sure you want to delete?', confirm).show()

        function confirm(){
            axios.post(`/api/company-delete-multiple`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    nextTick(() => {
                        getList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }
    }

    // For update status
    const updateStatus = (v, {target}) => {
        axios.post(`/api/company-update-status/${v}`, {
            status: Number(target.checked)
        }).then(res => {
            if(res.data.status = "success"){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }


    onMounted(() => {
        getList()
    })

</script>
