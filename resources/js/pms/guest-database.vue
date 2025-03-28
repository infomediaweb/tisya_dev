<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center justify-content-end">
                <div class="col-12 col-xxl">
                    <div class="row gy-3">
                        <div class="col align-self-end">
                            <h1 class="h2 mb-0">Guest Database</h1>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-xxl-auto">
                    <div class="search-filter">
                        <Form as="div" v-slot="{ errors, resetForm }">
                            <div class="row gy-3 gx-2">
                                <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="guest_title"
                                            type="text"
                                            class="form-control"
                                            placeholder="Name"
                                            v-model="searchGuestName"
                                        />
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="property_name"
                                            type="text"
                                            class="form-control"
                                            placeholder="Property Name"
                                            v-model="searchGuestPropertyName"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="guest_email"
                                            type="email"
                                            class="form-control"
                                            :class="{'border-danger': errors.guest_email}"
                                            placeholder="Email"
                                            rules="email"
                                            v-model="searchGuestEmail"
                                        />
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="guest_mobile"
                                            type="number"
                                            class="form-control"
                                            :class="{'border-danger': errors.guest_mobile}"
                                            placeholder="Mobile"
                                            rules="numeric|min:10|max:10"
                                            v-model="searchGuestMobile"
                                        />
                                    </div>
                                </div>

                                <!-- <div class="col-12 col-md-4 col-lg-3 col-xxl">
                                    <div class="form-group mb-0">
                                        <Field
                                            name="booking_status"
                                            as="select"
                                            class="form-control"
                                            v-model="searchGuestAdvance">
                                            <option value="" selected disabled>Advance Search</option>
                                            <option value="4">Guest With Email</option>
                                            <option value="3">Guest With Mobile</option>
                                            <option value="2">Guest With Email/Mobile</option>
                                            <option value="1">Blacklist</option>
                                        </Field>
                                    </div>
                                </div> -->

                                <div class="col-12 col-md-auto">
                                    <div class="btn-group gap-2">
                                        <button 
                                            @click="getGuestDatabase" 
                                            class="btn btn-primary btn-icon"
                                            :disabled="!searchGuestName && !searchGuestEmail && !searchGuestPropertyName && !searchGuestMobile || errors.guest_email || errors.guest_mobile">
                                            <span class="icon-search"></span>
                                        </button>
                                        <button 
                                            @click="clearFilter(resetForm)" 
                                            class="btn btn-icon btn-clear btn-warning">
                                            <span class="bi bi-arrow-clockwise"></span>
                                        </button>
                                        <button @click="exportGuestExcel" class="btn btn-export btn-secondary"><i class="bi bi-file-earmark-excel me-2 fs-6"></i>Export</button>
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
                <div class="outer-wrapper" v-if="guestDatabaseList.length">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table align-middle table-list mb-0 mw-lg">
                                <thead>
                                    <tr>
                                        <th class="text-secondary fw-semibold">Name</th>
                                        <th class="text-secondary fw-semibold">Email</th>
                                        <th class="text-secondary fw-semibold">Mobile</th>
                                        <th class="text-secondary fw-semibold">Property Name</th>
                                        <th class="text-secondary fw-semibold">Category</th>
                                        <th class="text-secondary fw-semibold">Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(obj, index) in guestDatabaseList" :key="index">
                                        <td>{{ obj.name }}</td>
                                        <td>{{ obj.email }}</td>
                                        <td>{{ obj.mobile_no }}</td>
                                        <td>{{ obj.home_name }}</td>
                                        <td>{{ obj.home_type }}</td>
                                        <td>{{ obj.location_name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-footer pt-3">
                        <div class="row align-items-center">
                            <div class="col" v-if="dataRow.last_page > 1">
                                <Pagination 
                                    :links="dataRow.links"
                                    @pagination="getGuestDatabase"
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
<script setup>
    import Pagination from '@components/pagination.vue'
    import axios from 'axios'
    import { ref, onMounted, nextTick } from 'vue'
    import { Form, Field } from 'vee-validate'

    const guestDatabaseList = ref([])
    const dataRow = ref({})

    const isLoading = ref(false)

    // Search Model
    const searchGuestName = ref()
    const searchGuestPropertyName = ref()
    const searchGuestEmail = ref()
    const searchGuestMobile = ref()
    //const searchGuestAdvance = ref()


    const clearFilter = (resetForm) => {
        searchGuestName.value = ''
        searchGuestPropertyName.value = ''
        searchGuestEmail.value = ''
        searchGuestMobile.value = ''
        //searchGuestAdvance.value = ''

        resetForm()

        nextTick(() => {
            getGuestDatabase()
        })
    }


    // For get all guest database list
    const getGuestDatabase = (pageNumber) => {
        isLoading.value = true

        axios.post('/api/guest/database', {
            page: pageNumber,
            name: searchGuestName.value,
            property_name: searchGuestPropertyName.value,
            email: searchGuestEmail.value,
            mobile: searchGuestMobile.value
        }).then(res => {
            if(res.data.status){
                guestDatabaseList.value = res.data.data.data
                dataRow.value = res.data.data

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isLoading.value = false
        })
    }


    const exportGuestExcel = () => {
        let reqParameters = {
            name: searchGuestName.value,
            property_name: searchGuestPropertyName.value,
            email: searchGuestEmail.value,
            mobile: searchGuestMobile.value
        }

        axios.post(`/api/guest/database/export` , reqParameters, { responseType: 'blob' }).then(res => {
            const blob = new Blob([res.data], { type: 'application/octet-stream' })
            const url = window.URL.createObjectURL(blob)
            const link = document.createElement('a')
            link.href = url
            link.setAttribute('download', 'guest-database.xlsx')
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
        }).catch(error => {
            console.log(error)
        })
    }



    onMounted(() => {
        getGuestDatabase()
    })


</script>
