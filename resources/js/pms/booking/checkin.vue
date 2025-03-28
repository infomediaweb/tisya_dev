<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Guest Check In</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'booking-list' }" class="btn rounded-pill btn-secondary-light">
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
                <Form @submit="onSubmit" v-slot="{ errors }">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="row gx-5">
                                    <div class="col-auto">
                                        <span class="fw-bold">Booking ID :</span> {{ bookingID }}
                                    </div>
                                    <div class="col-auto">
                                        <span class="fw-bold">Accommodation :</span> {{ booking.home_name }}, {{ booking.location }}
                                    </div>
                                    <div class="col-auto">
                                        <span class="fw-bold">CheckIn :</span> {{ booking.checkin_date }}
                                    </div>
                                    <div class="col-auto">
                                        <span class="fw-bold">Checkout :</span> {{ booking.checkout_date }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  <pre>{{ guests }}</pre> -->
                        <div class="col-12">
                            <div class="row nth-row g-4">
                                <div class="col-12 col-lg-6" v-for="(obj, index) in guests">
                                    <div class="multi-row">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="small-action-btn">
                                                                <div class="btn btn-icon btn-secondary-light pe-none">{{ index + 1 }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row gx-3">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="">Name<span v-if="!index" class="text-danger">*</span></label>
                                                        <Field
                                                            type="text"
                                                            :name="`name-${index}`"
                                                            class="form-control"
                                                            :class="{'border-danger': errors[`name-${index}`]}"
                                                            v-model="obj.name"
                                                            :rules="!index ? 'required' : ''"
                                                        />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="">Email<span v-if="!index" class="text-danger">*</span></label>
                                                        <Field
                                                            type="text"
                                                            :name="`email-${index}`"
                                                            class="form-control"
                                                            :class="{'border-danger': errors[`email-${index}`]}"
                                                            v-model="obj.email"
                                                            @input="onInputChange($event, index, 'email')"
                                                            :rules="!index ? 'required|email' : 'email'"
                                                            data-bs-toggle="tooltip"
                                                        />
                                                        <Tooltip
                                                            :error="obj.guestUniqueEmailMessage"
                                                        />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label for="">Mobile No<span v-if="!index" class="text-danger">*</span></label>
                                                        <Field
                                                            type="text"
                                                            :name="`mobile_no-${index}`"
                                                            class="form-control"
                                                            :class="{'border-danger': errors[`mobile_no-${index}`]}"
                                                            v-model="obj.mobile_no"
                                                            @input="onInputChange($event, index, 'number')"
                                                            :rules="!index ? 'required|numeric|max:16|min:8' : 'numeric|max:16|min:8'"
                                                            data-bs-toggle="tooltip"
                                                        />
                                                        <Tooltip
                                                            :error="obj.guestUniqueNumberMessage"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                           <!--  <div class="col-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="">Id Proof<span v-if="!index" class="text-danger">*</span></label>
                                                    <UploadFile
                                                        :name="`image${index}`"
                                                        :id="`image${index}`"
                                                        fileType="image"
                                                        acceptType="image/*"
                                                        info="Max size: 1mb"
                                                        changeText="To change the image please click / drag new image here!"
                                                        size="1"
                                                        ratio="40%"
                                                        :isDeleteDisabled="true"
                                                        :filePath="obj.id_proof_img ? uploadFilePath + obj.id_proof_img : ''"
                                                        :fileName="obj.id_proof_img"
                                                        :rules="obj.id_proof_img ? 'image|size:1048|ext:jpg,jpeg,webp,svg,png' : !index ? 'required|image|size:1048|ext:jpg,jpeg,webp,svg,png' : ''"
                                                        @emitUploadFile="getUploadFile($event, index)"
                                                    />
                                                </div>
                                            </div> -->
                                            <div class="col-12 col-lg-12">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="">Id Proof<span v-if="!index" class="text-danger">*</span></label>
                                                            <div class="upload-wrapper" :class="{'border-danger': errors[`multipleFiles${index}`]}">
                                                                <Field
                                                                    type="file"
                                                                    :name="`multipleFiles${index}`"
                                                                    :id="`multipleFiles${index}`"
                                                                    multiple
                                                                    @change="uploadMultipleFiles($event, errors[`multipleFiles${index}`], index)"
                                                                    accept="image/*,application/pdf"
                                                                    :rules="obj.multipleFiles.length ? 'size:3072|ext:jpg,jpeg,png,pdf' : !index ? 'required|size:3072|ext:jpg,jpeg,png,pdf' : ''"
                                                                />

                                                                <div class="upload-info">
                                                                    <i class="icon-upload"></i>
                                                                    <div class="upload-info-text">
                                                                        <strong>Drag & Drop Or <span class="text-primary">Browse</span> Your File.</strong>
                                                                        <small>Max size: 3mb</small>
                                                                        <small class="text-danger mt-2 fs-12">
                                                                            <ErrorMessage :name="`multipleFiles${index}`" />
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="image-wrapper">
                                                            <div class="row g-3">
                                                                <div 
                                                                    class="col-6 col-sm-4 col-md-4 col-xxl-2"
                                                                    v-for="(objImage, idx) in obj.multipleFiles">
                                                                    <div class="imgWrapper multifile-upload">
                                                                        <a :href="objImage.filepath" class="imgBoxNew" target="_blank">
                                                                            <template v-if="objImage.extension == 'pdf'">
                                                                                <div class="icon-wrap ">
                                                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                                                </div>
                                                                            </template>
                                                                            <template v-else>
                                                                                <img :src="objImage.filepath" class="w-100">
                                                                            </template>
                                                                        </a>

                                                                        <template v-if="objImage.id || objImage.extension">
                                                                            <button type="button" @click="deleteMultipleFileItem(index, idx, objImage.id, resetForm, values, obj.id)" class="btn btn-light btn-sm">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        </template>
                                                                        
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-4">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-save btn-primary">
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
<style>
.imgBoxNew{display: block; background-color: #ffffff; position: relative; padding-bottom: 100%; text-decoration: none; border-radius: 10px; overflow: hidden;}
.imgBoxNew img{position: absolute; object-fit: cover; width: 100%; height: 100%;}
.imgWrapper{position: relative;}
.imgWrapper button{position: absolute; top:10px; right: 10px;}
.imgBoxNew .icon-wrap{position: absolute; width: 100%; height: 100%; top: 0; left: 0; display: flex; align-items: center; justify-content: center;}
.imgBoxNew .icon-wrap i{font-size: 48px;}




</style>
<script setup>
    import Tooltip from '@components/tooltip.vue'
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { ref, onMounted, defineAsyncComponent, nextTick } from 'vue'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { toast } from '@utils/toast'
    import { useRoute } from 'vue-router'
    import { format, currFormat, currFormatTotal } from '@utils/common'
    import { dialog } from '@utils/modal'


    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))
    import { validateFile } from '@utils/validate-file'
    const route = useRoute()

    const booking = ref({})
    const uploadFilePath = ref('')
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)
    const bookingID = ref('')


    // For initial value of guest
    const guests = ref([
        {
            name: '',
            email: '',
            mobile_no: '',
            //id_proof_img: '',
            multipleFiles: []
        }
    ])

    const uploadMultipleFiles = async(e, errorMessage, idx) => {
        const files = e.target.files
        const formData = new FormData()

        for(let i = 0; i < files.length; i++){
            if(validateFile(files[i], errorMessage) == false){
                continue
            }

            formData.append('file', files[i])

            try{
                const res = await axios.post('/api/upload', formData)

                if(res.data.status){
                    guests.value[idx].multipleFiles.push(res.data)
                }
            }
            catch(error){

            }
            
            await formData.delete('file')
        }

        e.target.value = null
    }


 // For delete current file
 const deleteMultipleFileItem = (mainIdx, idx, id, resetForm, values, mainId) => {
        if(id == 0 || id){
            dialog('Are you sure you want to remove?', confirm).show()
            function confirm(){
                axios.post(`/api/property/booking/deleteguestfile`, {
                    image_id: id,
                    main_id: mainId,
                    property_booking_id: route.params.id,
                }).then(res => {
                    if(res.data.status){
                        guests.value[mainIdx].multipleFiles.splice(idx, 1)
                        toast(res.data.message, 'success').show()
                    }
                }).catch(error => {
                    toast(error.response.data.message, 'error').show()
                })
            }
        }
        else{
            guests.value[mainIdx].multipleFiles.splice(idx, 1)
        }

        const multipleFiles = values[`multipleFiles${mainIdx}`]?.filter((item, index) => index !== mainIdx)

        resetForm({
            values: {
                [`multipleFiles${idx}`]: multipleFiles
            }
        })
    }


   // For get guests data
   const getBookingDetail = () => {
        if(route.params.id){
            isLoading.value = true
            axios.get(`/api/property/booking/${route.params.id}`).then(res => {

                if(res.data.status){
                    bookingID.value = res.data.data.booking_id
                    booking.value = res.data.data
                    let guestsIds = res.data.data.bookingGuestsIds
                    
                    let guestIncludedNo = parseInt(res.data.data.no_of_adult)
                    guestIncludedNo = guestIncludedNo
                    let guestDetail = res.data.data.customer_detail
                    let guestFullName = guestDetail.first_name + ' ' + guestDetail.last_name

                    //console.log(guestDetail,"guestDetail");
                    if(guestsIds.length){
                        uploadFilePath.value = res.data.id_path
                        guests.value = guestsIds

                        for(let i = 0; i < guestsIds.length; i++){
                            //guests.value[i].isFileUpload = false
                            guests.value[i].multipleFiles = guests.value[i].id_proof_imgs
                        }

                    }
                    else{

                        for(let i = 0; i < guestIncludedNo; i++){
                            if(i == 0){
                                guests.value[i].name = guestFullName
                                guests.value[i].email = guestDetail.email
                                guests.value[i].mobile_no = guestDetail.mobile_number
                                guests.value[i].dob = guestDetail.dob
                                guests.value[i].anniversary = guestDetail.anniversary
                                //guests.value[i].isFileUpload = false
                                guests.value[i].id = '',
                                guests.value[i].multipleFiles = []
                            }
                            else{
                                guests.value.push({
                                    name: '',
                                    email: '',
                                    mobile_no: '',
                                    //id_proof_img: '',
                                    //isFileUpload: false,
                                    id: '',
                                    dob: '',
                                    anniversary: '',
                                    multipleFiles: []
                                })
                                
                            }



                        }
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


    // For upload file values
    const getUploadFile = (value, idx) => {
        guests.value[idx].id_proof_img = value.filename
        if(value.status){
            guests.value[idx].isFileUpload = true
        }
    }


    const onInputChange = ({ target }, idx, inputType) => {
        guests.value.forEach(async el => {
            if(inputType == 'email'){
                if(target.value && target.value.toLowerCase() === el.email.toLowerCase()){
                    guests.value[idx].guestUniqueEmailMessage = await 'Email ID already taken'
                }
                else{
                    guests.value[idx].guestUniqueEmailMessage = ''
                }
            }
            else if(inputType == 'number'){
                if(target.value && target.value === el.mobile_no){
                    guests.value[idx].guestUniqueNumberMessage = await 'Mobile no already taken'
                }
                else{
                    guests.value[idx].guestUniqueNumberMessage = ''
                }
            }

        })
    }

    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        axios.post('/api/property/booking/ids', {
            guest_list: guests.value,
            property_booking_id: route.params.id,
            checkin_date: dayjs(booking.value.checkin_date).format('YYYY-MM-DD'),
            checkout_date: dayjs(booking.value.checkout_date).format('YYYY-MM-DD')
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    getBookingDetail()
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }



    onMounted(() => {
        getBookingDetail()
    })


</script>
