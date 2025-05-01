<template>
    <Form @submit="onSubmit" v-slot="{ errors, values, resetForm }">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Images<span class="text-danger">*</span></label>
                    <div class="upload-wrapper" :class="{'border-danger': errors.multipleImages}">
                        <Field
                            type="file"
                            name="multipleImages"
                            id="multipleImages"
                            multiple
                            @change="multipleImages($event, errors.multipleImages)"
                            accept="image/*"
                            :rules="imagesList.length ? 'image|size:2048|ext:jpg,jpeg,webp,svg,png' : 'required|image|size:2048|ext:jpg,jpeg,webp,svg,png'"
                        />

                        <div class="upload-info">
                            <i class="icon-upload"></i>
                            <div class="upload-info-text">
                                <strong>Drag & Drop Or <span class="text-primary">Browse</span> Your File.</strong>
                                <small>Max size: 2mb | Image size: 1600px . 1080px</small>
                                <small class="text-danger mt-2 fs-12">
                                    <ErrorMessage name="multipleImages" />
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="image-wrapper">
                    <VueDraggableNext
                        class="row"
                        :list="imagesList"
                        :move="onDragover"
                        @end="onDragend"
                        filter=".form-check-input, button">
                        <div
                            class="col-6 col-sm-4 col-md-3 col-lg-2 drag-el"
                            v-for="(obj, index) in imagesList"
                            :key="index"
                            :class="{'active': activeDrag == index}">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <div class="upload-img">
                                        <img :src="obj.filepath" class="w-100">
                                        <!-- <input
                                            type="radio"
                                            name="default_image"
                                            value="1"
                                            class="form-check-input"
                                            ref="defaultImageRef"
                                            v-model="obj.default"
                                            @change="setDefaultImage(index)"
                                        > -->

                                        <button type="button" @click="deleteItem(index, obj.id, resetForm, values)" class="btn btn-light btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <!-- <div class="form-group">
                                        <input
                                            type="text"
                                            name="image_title"
                                            class="form-control"
                                            placeholder="Title"
                                            v-model="obj.title"
                                        >
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </VueDraggableNext>
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
    </Form>
</template>
<script setup>
    import axios from 'axios'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { useRoute } from 'vue-router'
    import { ref, toRef, onMounted, nextTick } from 'vue'
    import { validateFile } from '@utils/validate-file'
    import { VueDraggableNext } from 'vue-draggable-next'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'

    const route = useRoute()

    const imagesList = ref([])
    const activeDrag = ref(null)
    const defaultImageRef = ref()

    const isSubmitLoading = ref(false)

    const emit = defineEmits(['emitCurrentTab'])
    const props = defineProps({
        data: Object
    })
    const homeEdit = toRef(props, 'data')


    // For upload multiple image
    const multipleImages = async (e, errorMessage) => {
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
                    imagesList.value.push(res.data)
                }
            }
            catch(error){

            }
            await formData.delete('file')
        }

        e.target.value = null
    }


    // For on change default images
    const setDefaultImage = (i) => {
        defaultImageRef.value.forEach((item, index) => {
            if(i != index){
                imagesList.value[index].default = 0
            }
        })
    }


    // For delete current image
    const deleteItem = (idx, id, resetForm, values) => {
        if(id){
            dialog('Are you sure you want to remove?', confirm).show()
            function confirm(){
                axios.get(`/api/delete-image-video/${id}`).then(res => {
                    if(res.data.status){
                        imagesList.value.splice(idx, 1)
                        toast(res.data.message, 'success').show()
                    }
                }).catch(error => {
                    toast(error.response.data.message, 'error').show()
                })
            }
        }
        else{
            imagesList.value.splice(idx, 1)
        }
        const multipleImages = values.multipleImages?.filter((item, index) => index !== idx)
        resetForm({
            values: {
                multipleImages: multipleImages
            }
        })
    }


    // For active drag image
    const onDragover = (e) => {
        activeDrag.value = e.draggedContext.index
    }


    // For remove active class
    const onDragend = (e) => {
        activeDrag.value = null
    }


    // For form on submit
    const onSubmit = () => {
        isSubmitLoading.value = true

        let imagesListFilter = imagesList.value.map(item => ({
            filename: item.filename,
            filepath: item.filepath,
            default: item.default,
            title: item.title
        }))

        axios.post('/api/home-gallery', {
            type: 'image',
            filename: imagesListFilter,
            home_id: route.params.id
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    emit('emitCurrentTab', 'Videos')
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }



    onMounted(() => {
        //For get images list
        if(homeEdit.value.home_image?.length && route.params.id){
            nextTick(async () => {
                imagesList.value = await homeEdit.value.home_image
            })
        }
    })


</script>
