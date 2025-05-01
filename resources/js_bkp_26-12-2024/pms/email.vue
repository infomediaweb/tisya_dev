<template>
    <div class="page-wrap property-add">
        <div class="page-title mb-4">
            <div class="row gy-3 align-items-center">
                <div class="col align-self-end">
                    <h1 class="h2 mb-0">Email Setting</h1>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="d-flex justify-content-center py-5" v-if="isLoading">
                <div class="spinner-border" role="status"></div>
            </div>

            <div class="page-content" v-else>
                <Form @keypress.enter.prevent @submit="onSubmit" v-slot="{ errors, resetForm }">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <div class="form-group">
                                <div class="tags-input-wrapper" :class="{'border-danger': errors.tags}">
                                    <ul>
                                        <li 
                                            v-for="(obj, index) in tags"
                                            class="tag-item">
                                            <div class="tag-content">
                                                <div class="tag-text">{{ obj.text }}</div>
                                                <button @click="removeTag(index)" type="button" class="tag-action">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </li>
                                        <li class="tag-input">
                                            <Field 
                                                name="tags"
                                                type="text"
                                                class="form-control"
                                                placeholder="Add Email"
                                                :rules="tags.length ? 'email' : 'required|email'"
                                                @keyup.enter="addTags($event, errors.tags, resetForm)"  
                                            />
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-save btn-primary">
                                <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                <span v-else>SUBMIT</span>
                            </button>
                        </div>
                    </div>
                </Form>
            </div>
        </section>
    </div>
</template>
<script setup>
    import axios from 'axios'
    import { ref, onMounted, nextTick } from 'vue'
    import { Form, Field } from 'vee-validate'
    import { toast } from '@utils/toast'

    const tags = ref([])

    const isLoading = ref(false)
    const isSubmitLoading = ref(false)


    const getEmails = () => {
        isLoading.value = true

        axios.get('/api/setting').then(res => {
            if(res.data.status){
                let convertJSON = JSON.parse(res.data.data[0]?.emails)
                tags.value = convertJSON

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isLoading.value = false
        })
    }
    

    const addTags = (e, error, resetForm) => {
        const email = e.target.value.trim()
        if(email && !error && !tags.value.some(tag => tag.text === email)){
            tags.value.push({ text: email })

            resetForm()
        }
    }


    const removeTag = (idx) => {
        tags.value.splice(idx, 1)
    }


    const onSubmit = () => {
        isSubmitLoading.value = true

        axios.post('/api/update-email-setting', {
            emails: tags.value
        }).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        getEmails()
    })

</script>
