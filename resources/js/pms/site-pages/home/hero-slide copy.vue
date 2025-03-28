<template>
    <div class="page-wrap">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Banner Slide</h1>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form @submit="onSubmit" v-slot="{ errors, values, resetForm }">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group title-editor">
                                <label for="">Title<span class="text-danger">*</span></label>
                                <ckeditor
                                    name="hero_title"
                                    :toolbar="['italic', 'fontColor']"
                                    v-model:data="slideEdit.heading"
                                    rules="required"
                                />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Subtitle<span class="text-danger">*</span></label>
                                <Field
                                    type="text"
                                    name="hero_subtitle"
                                    class="form-control"
                                    :class="{'border-danger': errors.hero_subtitle}"
                                    v-model="slideEdit.subtitle"
                                    rules="required"
                                />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Images<span class="text-danger">*</span></label>
                                <div class="upload-wrapper" :class="{'border-danger': errors.multipleImages}">
                                    <Field
                                        type="file"
                                        name="multipleImages"
                                        id="multipleImages"
                                        accept="image/*"
                                        multiple
                                        @change="multipleImages($event, errors.multipleImages)"
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

                        <template v-if="imagesList.length">
                            <div class="col-12" >
                                <div class="image-wrapper form-group">
                                    <VueDraggableNext
                                        class="row gy-4"
                                        :list="imagesList"
                                        :move="onDragover"
                                        @end="onDragend"
                                        @change="onDragChange"
                                        filter="button, .img-status">
                                        <div
                                            class="col-6 col-sm-4 col-md-3 col-lg-2 drag-el"
                                            v-for="(obj, index) in imagesList"
                                            :key="index"
                                            :class="{'active': activeDrag == index}">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="upload-img">
                                                        <img :src="obj.filepath" class="w-100">
                                                        <div class="img-status">
                                                            <input
                                                                type="checkbox"
                                                                :id="`status-${obj}`"
                                                                name="status"
                                                                class="form-check-input rounded-pill"
                                                                :checked="obj.status == 1"
                                                                v-model="obj.status"
                                                                @change="updateStatus($event, obj.id)"
                                                                true-value="1"
                                                                false-value="0"
                                                            >
                                                            <label>{{ obj.status == true ? 'Active' : 'Inactive' }}</label>
                                                        </div>

                                                        <button type="button" @click="deleteItem(index, obj.id, resetForm, values)" class="btn btn-light btn-sm">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </VueDraggableNext>
                                </div>
                            </div>
                        </template>

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
    import { ref, onMounted } from 'vue'
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import { validateFile } from '@utils/validate-file'
    import { VueDraggableNext } from 'vue-draggable-next'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'

    const imagesList = ref([])
    const slideEdit = ref({})
    const activeDrag = ref(null)
    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


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
                    imagesList.value[imagesList.value.length - 1].status = 1
                }
            }
            catch(error){

            }

            await formData.delete('file')
        }

        e.target.value = null
    }


    // For get slides data
    const getSlidesEdit = () => {
        isLoading.value = true

        axios.get('/api/home-banner').then(res => {
            if(res.data.status){
                slideEdit.value = res.data.data[0]

                imagesList.value = res.data.data.map(item => ({
                    id: item.id,
                    filename: item.image,
                    filepath: item.image_path,
                    status: item.status
                }))

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            if(error.response.data.message){
                toast(error.response.data.message, 'error').show()
            }
            isLoading.value = false
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


    // For change position of slide images
    const onDragChange = () => {
        // let uploadImagesListFilter = imagesList.value.map(item => item.id)

        // axios.post('/api/home-banner-save-position', {
        //     position: uploadImagesListFilter
        // }).then(res => {
        //     if(res.data.status){
        //         toast(res.data.message, 'success').show()
        //     }
        // }).catch(error => {
        //     toast(error.response.data.message, 'error').show()
        // })
    }


    // For change current image status
    const updateStatus = ({ target }, id) => {
        if(id){
            axios.post(`/api/home-banner-update-status/${id}`, {
                status: Number(target.checked)
            }).then(res => {
                if(res.data.status){
                    toast(res.data.message, 'success').show()
                }
            }).catch(error => {
                console.log(error)
            })
        }
    }


    // For delete current image
    const deleteItem = (idx, id, resetForm, values) => {
        if(id){
            dialog('Are you sure you want to remove?', confirm).show()

            function confirm(){
                axios.get(`/api/home-banner-delete-image/${id}`).then(res => {
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

            const multipleImages = values?.multipleImages?.filter((item, index) => index !== idx)

            resetForm({
                values: {
                    multipleImages: multipleImages
                }
            })
        }
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        let imagesListFilter = imagesList.value.map(item => ({
            filename: item.filename,
            filepath: item.filepath,
            status: item.status
        }))

        axios.post('/api/home-banner', {
            title: v.hero_title.replace(/^<[^>]+>|<[^>]+>$/g, ''),
            subtitle: v.hero_subtitle,
            images: imagesListFilter,
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                    getSlidesEdit()
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getSlidesEdit()
    })

</script>
