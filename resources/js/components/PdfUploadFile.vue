<template>
    <div class="upload-wrapper" :class="{'border-danger': !!errorMessage}">
        <div class="d-flex justify-content-center py-5" v-if="isLoading">
            <div class="spinner-border" role="status"></div>
        </div>

        <template v-else>
            <input 
                type="file"
                :name="name" 
                :id="id"
                :accept="acceptType"
                @input="handleChange" 
                @blur="handleBlur"
                @change="uploadFile" 
            />

            <div class="upload-info">
                <template v-if="getUploadFile.filepath">
                    
                    <a :href="getUploadFile.filepath" target="_blank">{{ getUploadFile.fileName }} <i class="bi bi-filetype-pdf fs-1"></i></a>
                    
                    <div class="upload-info-text">
                        <button 
                            v-if="!isDeleteDisabled" 
                            type="button" 
                            class="btn-link-icon text-danger mb-2" 
                            @click="deleteItem(), resetField()">
                            <i class="bi bi-trash"></i>
                        </button>
                        <small>{{ changeText }}</small>
                        <small v-if="errorMessage" class="text-danger mt-2 fs-12">{{ errorMessage }}</small>
                    </div>
                </template>

                <template v-else>
                    <i class="icon-upload"></i>
                    <div class="upload-info-text">
                        <strong>Drag & Drop Or <span class="text-primary">Browse</span> Your PDF File.</strong>
                        <small>{{ info }}</small>
                        <small class="text-danger mt-2 fs-12">{{ errorMessage }}</small>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>
<style scoped lang="scss">
.upload-info {
    z-index: 1;
    .pdf-preview {
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    .upload-info-text {
        button {
            pointer-events: all;
        }
    }
}
</style>

<script setup>
import axios from 'axios'
import { ref, toRef, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { useField } from 'vee-validate'
import { validateFile } from '@utils/validate-file'
import { dialog } from '@utils/modal'
const route = useRoute()
const props = defineProps({
    name: String,
    id: String,
    rules: String,
    info: String,
    changeText: String,
    isDeleteDisabled: Boolean,
    fileName: String,
    filePath: String,
    size: String,
    acceptType: String
})

const emit = defineEmits(['emitUploadFile', 'emitDeleteUploadFile', 'emitDeleteFrontendUploadFile'])

const getUploadFile = ref({})
const isLoading = ref(false)

const filePath = toRef(props, 'filePath')
const filedName = toRef(props, 'name')
const fileRules = toRef(props, 'rules')

const { errorMessage, handleBlur, handleChange, resetField } = useField(filedName, fileRules)

// Upload PDF logic
const uploadFile = async (e) => {
    const file = e.target.files[0]
    const formData = new FormData()
    e.target.value = null

    // Validate file type and size
    if (file.type !== 'application/pdf') {
        errorMessage.value = 'Only PDF files are allowed!'
        getUploadFile.value = {}
        return
    }
    if (validateFile(file, errorMessage.value, Number(props.size)) === false) {
        getUploadFile.value = {}
        return
    }

    formData.append('file', file)

    try {
        isLoading.value = true

        const res = await axios.post('/api/upload', formData)
        if (res.data.status) {
            getUploadFile.value = res.data
            emit('emitUploadFile', getUploadFile.value)

            setTimeout(() => {
                isLoading.value = false
            }, 300)
        }
    } catch (error) {
        console.error('File upload failed', error)
    }
}

// Delete PDF logic
const deleteItem = () => {
        if(getUploadFile.value.uploadType){
            getUploadFile.value = {}

            emit('emitDeleteFrontendUploadFile')
        }
        else{
            dialog('Are you sure you want to delete permanently?', confirm).show()

            function confirm(){
                getUploadFile.value = {}

                if(route.params.id){
                    emit('emitDeleteUploadFile', route.params.id)
                }
                else{
                    emit('emitDeleteUploadFile')
                }
            }
        }
    }

onMounted(() => {
    if (filePath.value) {
        getUploadFile.value = { 
            filepath: props.filePath,
            filename: props.fileName
        }

        nextTick(() => {
            emit('emitUploadFile', getUploadFile.value)
        })
    }
})
</script>
