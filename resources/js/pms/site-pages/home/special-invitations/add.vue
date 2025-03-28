<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">{{ route.params.id ? 'Modify Special Offers' : 'Add Special Offers' }}</h1>
                </div>
                <div class="col-auto">
                    <router-link :to="{ name: 'list-special-offer' }" class="btn rounded-pill btn-secondary-light">
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

            <div class="outer-wrapper" v-else>
                <Form @submit="onSubmit" v-slot="{errors}">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-4 col-md-4">
                                    <div class="form-group">
                                        <label for="">Offer Name<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="offer_name"
                                            class="form-control" 
                                            :class="{'border-danger': errors.offer_name}"
                                            v-model="offerEditData.offer_name"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-4 col-md-4">
                                    <div class="form-group">
                                        <label for="">Coupon List<span class="text-danger">*</span></label>
                                        <Field 
                                        as="select"
                                        name="coupon_id"
                                        class="form-control"
                                        :class="{'border-danger': errors.coupon_id}"
                                        v-model="offerEditData.couponcode_id"
                                        rules="required">
                                        <option value="" selected disabled>Select Coupon Code</option>
                                        <option 
                                            v-for="(obj, index) in CouponCodeList" 
                                            :key="index"
                                            :value="obj.id">
                                            {{ obj.codes.split(',')[0].trim() }}
                                        </option>
                                    </Field>
                                    </div>

                                    


                                </div> 
                                <div class="col-4 col-md-4">
                                    <div class="form-group">
                                        <label for="">Valid Till<span class="text-danger">*</span></label>
                                        <Field name="date" v-model="date" rules="required" v-slot="{ field }">
                                            <DatePicker 
                                                v-bind="field"
                                                v-model="date" 
                                                :format="format" 
                                                :enable-time-picker="false" 
                                                :min-date="new Date()"
                                                :input-class-name="`form-control ${errors.date ? 'border-danger' : ''}`" 
                                                prevent-min-max-navigation
                                                auto-apply 
                                            />
                                        </Field>
                                    </div>
                                </div> 
                                <!-- <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Text<span class="text-danger">*</span></label>
                                        <Field 
                                            as="textarea"
                                            name="headline"
                                            class="form-control" 
                                            :class="{'border-danger': errors.headline}"
                                            v-model="offerEditData.headline"
                                            rows="5"
                                            rules="required"
                                        />
                                    </div>
                                </div>  -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">Image<span class="text-danger">*</span></label>
                                        <UploadFile 
                                            name="image"
                                            id="image"
                                            fileType="image"
                                            acceptType="image/*"
                                            info="Max size: 1mb | Image size: 500px . 500px"
                                            changeText="To change the image please click / drag new image here!"
                                            size="1"
                                            ratio="40%"
                                            :filePath="offerEditData.image_path"
                                            :fileName="offerEditData.image"
                                            :rules="offerEditData.image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                            @emitUploadFile="getUploadFile"
                                            @emitDeleteUploadFile="deleteUploadFile"
                                        />
                                    </div> 
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group ckeditor-form">
                                        <label for="">Details</label>
                                        <ckeditor
                                            name="description" 
                                            
                                            v-model:data="offerEditData.description"
                                        />
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-2">
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
<script setup>
    import ckeditor from '@components/ckeditor.vue'
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { Form, Field } from 'vee-validate'
    import { ref, onMounted, defineAsyncComponent } from 'vue'
    import { useRouter, useRoute } from 'vue-router'
    import { format } from '@utils/common'
    import { toast } from '@utils/toast'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const router = useRouter()
    const route = useRoute()
    const CouponCodeList = ref([])
    const date = ref()
    const uploadFile = ref({})
    const offerEditData = ref([])
    const submitApiUrl = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)

    

    // For special offer edit 
    const specialOfferEdit = () => {
        if(route.params.id){
            isLoading.value = true

            axios.get(`/api/special-invitation/${route.params.id}`).then(res => {
                if(res.data.status){
                    offerEditData.value = res.data.data
                    date.value = offerEditData.value.validity

                    setTimeout(() => {
                        isLoading.value = false
                    }, 400)
                }
            }).catch(error => {
                isLoading.value = false
                console.log(error);
            })
        }
    }

    const getCouponCode = () => {
        axios.get('/api/coupon_code').then(res => {
            if(res.data.status){
                CouponCodeList.value = res.data.data
                
            }
        }).catch(error => {
            console.log(error);
        })
    }

    // For upload file values
    const getUploadFile = (value) => {
        uploadFile.value = value
    }


    // For delete uploaded file
    const deleteUploadFile = (id) => {
        axios.get(`/api/special-invitation-delete-image/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                offerEditData.value.image = ''
                offerEditData.value.image_path = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        if(route.params.id){
            submitApiUrl.value = `/api/special-invitation/${route.params.id}`
        }
        else{
            submitApiUrl.value = '/api/special-invitation'
        }

        axios.post(submitApiUrl.value, {
            offer_name: v.offer_name,
           // headline: v.headline,
            image: uploadFile.value.filename,
            validity: dayjs(date.value).format('YYYY-MM-DD'),
            description: v.description,
            couponcode_id: v.coupon_id
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false 
                    router.push({name: 'list-special-offer'})
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        specialOfferEdit()
        getCouponCode()
    })

</script>