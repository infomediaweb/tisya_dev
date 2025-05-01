<template>
    <Form @submit="onSubmit" v-slot="{ errors, resetForm }">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Video</label>
                    <UploadFile 
                        name="video"
                        id="video"
                        fileType="video"
                        acceptType="video/mp4"
                        info="Max size: 200mb"
                        changeText="To change the video please click / drag new video here!"
                        size="200"
                        rules="required|size:204800|ext:mp4"
                        @emitUploadFile="getUploadFile"
                    />
                </div>
            </div>   
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group mb-0">
                    <button class="btn btn-save btn-primary">
                        <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                        <span v-else>SUBMIT</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="row pt-5" v-if="videoList.length">
            <div class="col-12">
                <div class="form-group">
                    <h6 class="fw-bold mb-0">Uploaded Videos</h6>
                </div>
            </div>
            <div class="col-12">
                <div class="video-wrapper">
                    <VueDraggableNext
                        class="row"
                        :list="videoList"
                        :move="onDragover"
                        @end="() => activeDrag = null"
                        @change="onDragChange"
                        filter="button">
                        <div 
                            class="col-12 col-sm-6 col-lg-4 col-xxl-3 drag-el"
                            v-for="(obj, index) in videoList"
                            :key="index"
                            :class="{'active': activeDrag == index}">
                            <div class="form-group">
                                <div class="video-card">
                                    <div class="ratio ratio-16x9">
                                        <video controls :src="obj.filepath"></video>
                                    </div>
                                    
                                    <button 
                                        type="button" 
                                        class="btn btn-light btn-sm"
                                        @click="deleteItem(index, obj.id)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </VueDraggableNext>
                </div>
            </div>
        </div>
    </Form>
</template>
<script setup>
    import axios from 'axios'
    import { Form } from 'vee-validate'
    import { useRoute } from 'vue-router'
    import { ref, toRef, onMounted, nextTick, defineAsyncComponent  } from 'vue'
    import { VueDraggableNext } from 'vue-draggable-next'
    import { toast } from '@utils/toast'
    import { dialog } from '@utils/modal'

    const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))

    const route = useRoute()

    const videoList = ref([])
    const uploadFile = ref({}) 
    const activeDrag = ref(null)
    const isSubmitLoading = ref(false)

    const emit = defineEmits(['emitCurrentTab'])
    const props = defineProps({
        data: Object
    })

    const homeEdit = toRef(props, 'data')


    // For upload file values
    const getUploadFile = (value) => {
        uploadFile.value = value
    }


    // For delete uploaded video
    const deleteItem = (idx, id) => {
        dialog('Are you sure you want to remove?', confirm).show()

        function confirm(){
            axios.get(`/api/delete-image-video/${id}`).then(res => {
                if(res.data.status){
                    videoList.value.splice(idx, 1)
                    toast(res.data.message, 'success').show()
                }
            }).catch(error => {
                toast(error.response.data.message, 'error').show()
            })
        }
    }


    // For active drag video
    const onDragover = (e) => {
        activeDrag.value = e.draggedContext.index
    }


    // For remove active class and position change of video list
    const onDragChange = (e) => {
        activeDrag.value = null

        let videoListFilter = videoList.value.map(item => item.id)

        axios.post('/api/home-video-position', {
            type: 'video',
            home_id: route.params.id,
            position: videoListFilter
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }


    // For on submit form 
    const onSubmit = () => {
        isSubmitLoading.value = true

        axios.post('/api/home-video', {
            type: 'video',
            filename: uploadFile.value.filename,
            filepath: uploadFile.value.filepath,
            home_id: route.params.id 
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

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
        //For get video
        if(homeEdit.value.home_video?.length && route.params.id){
            nextTick(async () => {
                videoList.value = await homeEdit.value.home_video
            })
        }
    })

</script>