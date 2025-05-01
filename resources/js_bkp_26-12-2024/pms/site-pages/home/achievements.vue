<template>
    <div class="page-wrap property-add">
       <div class="page-title mb-4">
           <div class="row gy-3 align-items-center">
               <div class="col">
                   <h1 class="h2 mb-0">Achievements</h1>
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
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="">Managing Luxury Homes Since<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="home_since"
                                            class="form-control" 
                                            :class="{'border-danger': errors.home_since}"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="">Dedicated Home Staff<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="home_staff"
                                            class="form-control" 
                                            :class="{'border-danger': errors.home_staff}"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="">Hosted Families<span class="text-danger">*</span></label>
                                        <Field 
                                            type="text" 
                                            name="hosted_families"
                                            class="form-control" 
                                            :class="{'border-danger': errors.hosted_families}"
                                            rules="required"
                                        />
                                    </div>
                                </div> 
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
           </div>
       </section>
   </div>
</template>
<script setup>
   import axios from 'axios'
   import { Form, Field, ErrorMessage } from 'vee-validate'
   import { ref, onMounted } from 'vue'
   import { toast } from '@utils/toast'


   const achievementData = ref([])
   const isLoading = ref(false)
   const isSubmitLoading = ref(false)


    // For get achievement data
    const getAchievement = () => {
        isLoading.value = true

        axios.get(`/api/`).then(res => {
            if(res.data.status){
                achievementData.value = res.data.data

                setTimeout(() => {
                    isLoading.value = false
                }, 400)
            }
        }).catch(error => {
            isLoading.value = false
            console.log(error);
        })
    }


    // For form on submit
    const onSubmit = (v) => {
        isSubmitLoading.value = true

        axios.post('', v).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()

                setTimeout(() => {
                    isSubmitLoading.value = false 
                    getAchievement()
                }, 400)
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
            isSubmitLoading.value = false
        })
    }


    onMounted(() => {
        //getAchievement()
    })

</script>