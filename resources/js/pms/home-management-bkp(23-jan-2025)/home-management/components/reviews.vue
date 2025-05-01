<template>
    <Form @submit="onSubmit" v-slot="{ errors }">
        <div class="row g-5">
            <div class="col-12 col-xxl-6">
                <div class="d-flex justify-content-center py-5" v-if="isLoading">
                    <div class="spinner-border" role="status"></div>
                </div>

                <template v-else> 
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Guest Name<span class="text-danger">*</span></label>
                                <Field 
                                    type="text" 
                                    name="guest_name"
                                    class="form-control"
                                    :class="{'border-danger': errors.guest_name}" 
                                    v-model="reviewEdit.guest_name"
                                    rules="required"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Review Type<span class="text-danger">*</span></label>
                                <Field 
                                    as="select"
                                    name="review_type"
                                    class="form-control"
                                    :class="{'border-danger': errors.review_type}"
                                    v-model="reviewEdit.review_type" 
                                    rules="required">
                                    <option value="" disabled selected>Select Review Type</option>
                                    <option value="Booking.com">Booking.com</option>
                                    <option value="MakeMyTrip">MakeMyTrip</option>
                                    <option value="Airbnb">Airbnb</option>
                                    <option value="Google">Google</option>
                                    <option value="Tisya">Tisya</option>
                                </Field>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Date<span class="text-danger">*</span></label>
                                <Field name="date" v-model="date" rules="required" v-slot="{ field }">
                                    <DatePicker 
                                        v-bind="field"
                                        v-model="date" 
                                        :format="format" 
                                        :enable-time-picker="false" 
                                        :max-date="new Date()"
                                        :input-class-name="`form-control ${errors.date ? 'border-danger' : ''}`" 
                                        prevent-min-max-navigation
                                        auto-apply 
                                    />
                                </Field>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Rating<span class="text-danger">*</span></label>
                                <Field 
                                    as="select"
                                    name="review_rating"
                                    class="form-control"
                                    :class="{'border-danger': errors.review_rating}"
                                    v-model="reviewEdit.rating" 
                                    rules="required">
                                    <option value="" disabled selected>Select Rating</option>
                                    <option value="1">1</option>
                                    <option value="1.5">1.5</option>
                                    <option value="2">2</option>
                                    <option value="2.5">2.5</option>
                                    <option value="3">3</option>
                                    <option value="3.5">3.5</option>
                                    <option value="4">4</option>
                                    <option value="4.5">4.5</option>
                                    <option value="5">5</option>
                                </Field>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Comment<span class="text-danger">*</span></label>
                                <Field 
                                    as="textarea"
                                    name="comment"
                                    class="form-control"
                                    :class="{'border-danger': errors.comment}" 
                                    v-model="reviewEdit.comment"
                                    rows="6"
                                    rules="required"
                                />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Icon<span class="text-danger">*</span></label>
                                <UploadFile 
                                    name="icons_image"
                                    id="image"
                                    fileType="image"
                                    acceptType="image/*"
                                    info="Max size: 1mb | Image size: 100px . 100px"
                                    changeText="To change the icon please click / drag new icon here!"
                                    size="1"
                                    ratio="15%"
                                    :filePath="reviewEdit.icons_image"
                                    :fileName="reviewEdit.icons_image_name"
                                    :rules="reviewEdit.icons_image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                    @emitUploadFile="getUploadFile"
                                    @emitDeleteUploadFile="deleteUploadFile(reviewEdit.id)"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-save btn-primary">
                                    <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                    <span v-else>SUBMIT</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="col-12 col-xxl-6 " v-if="reviewList.length">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <h6 class="fw-bold mb-0">Reviews List</h6>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
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
                                            <th class="text-secondary fw-semibold">Icon</th>
                                            <th class="text-secondary fw-semibold">Review Type</th>
                                            <th class="text-secondary fw-semibold">Guest Name</th>
                                            <th width="20%" class="text-secondary fw-semibold">Date</th>
                                            <th class="text-secondary fw-semibold text-center">Rating</th>
                                            <th width="90px" class="text-secondary fw-semibold text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr 
                                            v-for="(obj, index) in reviewList" 
                                            :key="index">
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
                                            <td>
                                                <img
                                                :src="obj.icon_url ? obj.icon_url : placeholder"
                                                height="20"
                                            >
                                               
                                            
                                            </td>
                                            <td>{{ obj.review_type }}</td>
                                            <td>{{ obj.guest_name }}</td>
                                            <td>{{ dayjs(obj.review_date).format('D MMM, YYYY') }}</td>
                                            <td class="text-center">{{ obj.rating }}</td>
                                            <td>
                                                <ul class="action-btn-group justify-content-center mb-0 mw-0">
                                                    <li>
                                                        <button type="button" @click="getEditItem(obj.id)" class="btn ps-0 p-1 fs-5 text-black">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" @click="deleteItem(obj.id)" class="btn p-1 fs-5 text-black">
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
                                        type="button"
                                        class="btn btn-save btn-primary" 
                                        @click="deleteMultiItem" 
                                        :disabled="(!vCheckAll && vSelectCheckValue.length == 0) || vSelectCheckValue.length == 0">
                                        Delete Selected
                                    </button>
                                </div>
                            </div>
                        </div> 

                    </div>
                </div>
            </div>
           
        </div>

    </Form>
</template>
<script setup>
    import axios from 'axios'
    import dayjs from 'dayjs'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { useRoute, useRouter } from 'vue-router'
    import { ref, toRef, nextTick, onMounted, defineAsyncComponent, watch } from 'vue'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'
    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))
    const route = useRoute()
    const router = useRouter()

    const reviewList = ref([])
    const reviewEdit = ref({})
    const editId = ref()
    const date = ref()

    const checkedSelectAll = ref(false)
    const vCheckAll = ref(false)
    const vSelectCheckValue = ref([])
    const selectCheckRef = ref()
    const uploadFile = ref({})

    const isLoading = ref(false)
    const submitApiUrl = ref(null)
    const isSubmitLoading = ref(false)

    const emit = defineEmits(['emitCurrentTab'])
    const props = defineProps({
        data: Object
    })

    const homeEdit = toRef(props, 'data')


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


    // For date format
    const format = (date) => {
        return dayjs(date).format('D MMM, YYYY')
    }


    // For get edit item 
    const getEditItem = (id) => {
        isLoading.value = true
        editId.value = id

        axios.get(`/api/show-home-review/${editId.value}`).then(res => {
            if(res.data.status){
                reviewEdit.value = res.data.data
                date.value = reviewEdit.value.review_date 

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isLoading.value = false
        })
    }


    // For delete single item
    const deleteItem = (id) => {
        dialog('Are you sure you want to delete?', confirm).show()

        function confirm() {
            axios.post(`/api/delete-home-reviews`, {
                home_id: route.params.id,
                id: id
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        emit('emitCurrentTab', 'Reviews')
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

        function confirm() {
            axios.post(`/api/delete-multiple-home-reviews`, {
                ids: vSelectCheckValue.value
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                    
                    nextTick(() => {
                        emit('emitCurrentTab', 'Reviews')
                    })
                }
            }).catch(error => {
                console.log(error)
            })
        }
    }
    

    //For form on submit
    const onSubmit = (v) => {
       
        isSubmitLoading.value = true
        submitApiUrl.value = editId.value ? `/api/save-home-review/${editId.value}` :  '/api/save-home-review'
        axios.put(submitApiUrl.value, {
           
            home_id: route.params.id,
            guest_name: v.guest_name,
            review_date: dayjs(date.value).format('YYYY-MM-DD'),
            rating: v.review_rating,
            comment: v.comment,
            review_type: v.review_type,
            icons_image: uploadFile.value.filename
        }).then(res => {
            if(res.data.status){
                editId.value ? toast('Successfully Updated.', 'success').show() : toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    emit('emitCurrentTab', 'Reviews')
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        //For get Review list 
        if(homeEdit.value.home_reviews?.length && route.params.id){
            nextTick(async () => {
                reviewList.value = await homeEdit.value.home_reviews
            })
        }
    })

    const getUploadFile = (value) => {
        uploadFile.value = value
    }

 // For delete uploaded file
 const deleteUploadFile = (id) => {
        axios.get(`/api/icons-image-delete/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                reviewEdit.value.icons_image = ''
                reviewEdit.value.icons_image_name = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }
</script>