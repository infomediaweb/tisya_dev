<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center justify-content-end">
                <div class="col-12">
                    <div class="row gy-3">
                        <div class="col align-self-end">
                            <h1 class="h2 mb-0">Coupon List</h1>
                        </div>
                        <div class="col-auto">
                            <router-link :to="{name: 'add-coupon'}" class="btn rounded-pill btn-secondary-light">
                                <i class="bi bi-plus-lg fs-6 lh-1 me-2"></i>
                                Add Coupon
                            </router-link>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-7">
                    <div class="search-filter">
                        <Form as="div" v-slot="{ errors, resetForm }">
                            <div class="row gy-3 gx-2">
                                <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="coupon_title"
                                            type="text"
                                            class="form-control"
                                            placeholder="Title"
                                            rules="numeric"
                                            v-model="searchCouponTitle"
                                        />
                                    </div>
                                </div>

                                <!-- <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="property_name"
                                            as="select"
                                            class="form-control"
                                            v-model="searchPropertyType">
                                            <option value="" selected disabled>Property Type</option>
                                            <option
                                                v-for="(obj, index) in propertyList"
                                                :key="index"
                                                :value="obj.id">
                                                {{ obj.home_name }}
                                            </option>
                                        </Field>
                                    </div>
                                </div> -->

                                <!-- <div class="col-12 col-md-4 col-lg-3 col-xxl">
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
                                </div> -->
                                

                                <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="booking_status"
                                            as="select"
                                            class="form-control"
                                            v-model="searchCouponStatus">
                                            <option value="" selected disabled>Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </Field>
                                    </div>
                                </div>

                                <div class="col-12 col-md-auto">
                                    <div class="btn-group gap-2">
                                        <button
                                            @click="getCouponList"
                                            class="btn btn-primary btn-icon"
                                            :disabled="!searchCouponTitle && !searchCouponStatus">
                                            <span class="icon-search"></span>
                                        </button>


                                        <button 
                                            @click="clearFilter(resetForm)" 
                                            class="btn btn-icon btn-clear btn-warning">
                                            <span class="bi bi-arrow-clockwise"></span>
                                        </button>
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
                <div class="outer-wrapper" v-if="couponList.length">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 mw-lg">
                                <thead>
                                    <tr>
                                        <th width="45px">
                                            <input
                                                class="form-check-input form-check-lg mt-0"
                                                type="checkbox"
                                                v-model="vCheckAll"
                                                @change="onCheckAll"
                                            >
                                        </th>
                                        <th width="20%" class="text-secondary fw-semibold">Title</th>
                                        <th width="15%" class="text-secondary fw-semibold">Coupon Code</th>
                                        <th class="text-secondary fw-semibold">Discount</th>
                                        <th class="text-secondary fw-semibold">Validity</th>
                                        <th class="text-secondary fw-semibold">Stay dates between</th>
                                        <th width="7%" class="text-secondary fw-semibold">Status</th>
                                        <th  style="width:90px;" class="text-secondary fw-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(obj, index) in couponList" :key="index">
                                        <td>
                                            <input
                                                class="form-check-input form-check-lg mt-0"
                                                ref="selectCheckRef"
                                                :checked="checkedSelectAll"
                                                :value="obj.id"
                                                v-model="vSelectCheckValue"
                                                type="checkbox"
                                            >
                                        </td>
                                        <td>{{ obj.title }}</td>
                                        <td>
                                        
                                            <div class="position-relative">
                                                <div class="btn-group d-flex align-items-center gap-3">
                                                    <button
                                                        type="button"
                                                        class="btn-none text-primary fw-semibold text-start"
                                                        v-clipboard="stringToArray(obj.codes)[0]"
                                                        data-bs-toggle="tooltip">{{ stringToArray(obj.codes)[0] }}
                                                    </button>



                                                   <template v-if="stringToArray(obj.codes).length > 1">
                                                        <div v-if="obj.coupon_download_loader" class="spinner-border spinner-border-sm" role="status"></div>

                                                        <button
                                                            v-else
                                                            type="button"
                                                            class="btn-none text-dark fw-semibold text-start"
                                                            @click="downloadCoupons(obj.id, index)">
                                                            <i class="bi bi-download fs-6"></i>
                                                        </button>
                                                   </template>

                                                </div>


                                                <Tooltip
                                                    custom-class="info-tooltip"
                                                    custom-hover-message="Copy"
                                                    custom-message="Copied!"
                                                    custom-trigger="hover manual"
                                                    custom-hide-time="600"
                                                />
                                            </div>
                                        </td>
                                        <td>{{ obj.discount }}{{ obj.discount_type == 'flat' ? '' : '%' }}</td>






                                        <td>{{ shortFormat(obj.start_date) }} to {{ shortFormat(obj.end_date) }}</td>
                                        <td>{{ shortFormat(obj.stay_date_from) }} to {{ shortFormat(obj.stay_date_to) }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    id="s1"
                                                    :checked="obj.status == 1"
                                                    @change="updateStatus(obj.id, $event)"
                                                    true-value="1"
                                                    false-value="0"
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="action-btn-group mb-0 mw-0">
                                                <li>
                                                    <router-link :to="{name: 'add-coupon', params: { id: obj.id }}" class="btn ps-0 p-1 fs-5 text-black">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </router-link>
                                                </li>
                                                <li>
                                                    <button @click="deleteItem(obj.id)" class="btn p-1 fs-5 text-black">
                                                        <i class="icon-bi bi-trash"></i>
                                                    </button>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-footer pt-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <button
                                    class="btn btn-save btn-primary"
                                    @click="deleteMultiItem"
                                    :disabled="(!vCheckAll && vSelectCheckValue.length == 0) || vSelectCheckValue.length == 0">
                                    Delete Selected
                                </button>
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
<script setup>
    import Tooltip from '@components/tooltip.vue'
    import axios from 'axios'
    import { ref, onMounted, watch, nextTick } from 'vue'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'
    import { shortFormat } from '@utils/common'

    const propertyList = ref([])
    const couponList = ref([])

    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()

    const isLoading = ref(false)

    // Search Model
    const searchCouponTitle = ref()
    const searchPropertyType = ref()
    const searchPropertyId = ref()
    const searchCouponStatus = ref()


    const clearFilter = (resetForm) => {
        searchCouponTitle.value = ''
        searchPropertyType.value = ''
        searchPropertyId.value = ''
        searchCouponStatus.value = ''

        resetForm()

        nextTick(() => {
            getCouponList()
        })
    }

    const stringToArray = (value) => {
        console.log(value,"value");
        return value.split(',')
    }

    // For get all coupon list
    const getCouponList = (pageNumber) => {
        isLoading.value = true

        axios.get('/api/get/coupon/code/list', {
            params: {
                title: searchCouponTitle.value,
                status: searchCouponStatus.value
            }
        }).then(res => {
            if(res.data.status){
                couponList.value = res.data.data

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
            axios.delete(`/api/coupon/code/delete/${v}`).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    nextTick(() => {
                        getCouponList()
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
            axios.post(`/api/coupon/code/delete/multiple`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()

                    nextTick(() => {
                        getCouponList()
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }
    }


    // For update status
    const updateStatus = (v, {target}) => {
        axios.post(`/api/coupon/code/update/status/${v}`, {
            status: Number(target.checked)
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            console.log(error)
        })
    }
    const downloadCoupons = (id, idx) => {
        couponList.value[idx].coupon_download_loader = true

        axios.post(`/api/couponcode/export` , {
            id: id
        }, { responseType: 'blob' }).then(response => {
            const blob = new Blob([response.data], { type: 'application/octet-stream' })
            const url = window.URL.createObjectURL(blob)

            const link = document.createElement('a')
            link.href = url

            link.setAttribute('download', 'coupon-codes.xlsx')
            document.body.appendChild(link)

            link.click()
            document.body.removeChild(link)

            setTimeout(() => {
                couponList.value[idx].coupon_download_loader = false
            }, 400)

        }).catch(error => {
            console.log(error)
        })
    }

    onMounted(() => {
        getCouponList()
    })


</script>
