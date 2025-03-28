<template>
    <section class="section">
           <div class="d-flex justify-content-center py-5" v-if="isLoading">
               <div class="spinner-border" role="status"></div>
           </div>

           <template v-else>
               <div class="outer-wrapper">
                   <Form @submit="onSubmit" v-slot="{errors}" :initial-values="formInitialValues">
                       <div class="table-wrap">
                        
                           <FieldArray name="addMultiItem" v-slot="{ fields, push, remove }">
                               <div class="table-responsive">
                                   <table class="table table-list mb-0 mw-lg">
                                       <thead>
                                           <tr>
                                               <th class="fw-semibold">Image</th>
                                               <th class="fw-semibold">Title</th>
                                               <th class="fw-semibold">Description</th>
                                               <th style="width:90px;" class="text-secondary text-center fw-semibold">Action</th>
                                           </tr>
                                       </thead>
                                       <tbody>
                                           <tr v-for="(obj, index) in fields" :key="index">
                                            <td>
                                                <div class="form-group mb-0">
                                                    <UploadFile 
                                                    :name="`image${index}`"
                                                    id="image"
                                                    fileType="image"
                                                    acceptType="image/*"
                                                    info="Max size: 1mb | Image size: 100px . 100px"
                                                    changeText="To change the icon please click / drag new icon here!"
                                                    size="1"
                                                    ratio="35%"
                                                    class="upload-smaller"
                                                    :filePath="obj.value.image_url"
                                                    :fileName="obj.value.image"
                                                    :rules="obj.value.image ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                                                    @emitUploadFile="getUploadFile($event, index, fields)"
                                                    :isDeleteDisabled="true"
                                                    
                                                    />
                                                </div>
                                            </td>
                                               <td>
                                                   <div class="form-group mb-0">
                                                       <Field
                                                           :name="`title${index}`"
                                                           type="text"
                                                           class="form-control"
                                                           :class="{'border-danger': errors[`title${index}`]}"
                                                           v-model="obj.value.title"
                                                           rules="required"
                                                       />
                                                   </div>
                                               </td>
                                               <td>
                                                   <div class="form-group mb-0">
                                                       <Field
                                                           :name="`description${index}`"
                                                           type="text"
                                                           class="form-control"
                                                           :class="{'border-danger': errors[`description${index}`]}"
                                                           v-model="obj.value.description"
                                                           rules="required"
                                                       />
                                                   </div>
                                               </td>
                                               <td>
                                                   <ul class="action-btn-group justify-content-center mb-0 mw-0">
                                                       <li>
                                                           <button
                                                               v-if="obj.value.id"
                                                               type="button"
                                                               @click="deleteItem(obj.value.id)"
                                                               class="btn p-1 fs-5 text-black">
                                                               <i class="icon-bi bi-trash"></i>
                                                           </button>

                                                           <button
                                                               v-else
                                                               type="button"
                                                               @click="remove(index)"
                                                               class="btn p-1 fs-5 text-black">
                                                               <i class="icon-bi bi-trash"></i>
                                                           </button>
                                                       </li>
                                                   </ul>
                                               </td>
                                           </tr>
                                       </tbody>
                                   </table>
                               </div>

                               <div class="table-footer pt-3">
                                   <div class="row align-items-center">
                                       <div class="col-auto">
                                           <button
                                               type="button"
                                               class="btn btn-save btn-secondary"
                                               @click="push({
                                                  title: '',
                                                  description: '',
                                                  image: '',
                                                   id: '',

                                               })"
                                               :disabled="!perviousArrayItem(fields).title || !perviousArrayItem(fields).description">
                                               <i class="bi bi-plus-lg me-2"></i>Add More
                                           </button>
                                       </div>
                                   </div>
                               </div>
                           </FieldArray>
                       </div>

                       <div class="table-footer pt-4">
                           <div class="row align-items-center">
                               <div class="col-12">
                                   <button class="btn btn-save btn-primary">
                                       <div v-if="isSubmitLoading" class="spinner-border spinner-border-sm" role="status"></div>
                                       <span v-else>SUBMIT</span>
                                   </button>
                               </div>
                           </div>
                       </div>

                   </Form>
               </div>
           </template>
       </section>
</template>
<script setup>
   import { Form, Field, FieldArray } from 'vee-validate'
   import { useRoute } from 'vue-router'
   import { ref, reactive, toRef, onMounted, defineAsyncComponent } from 'vue'
   import { toast } from '@utils/toast'
   import { dialog } from '@utils/modal'

   const route = useRoute()
   const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))
   const uploadFile = ref({})
   const isLoading = ref(false)
   const isSubmitLoading = ref(false)

   const emit = defineEmits(['emitCurrentTab'])
   const props = defineProps({
       data: Object
   })

   //const homeEdit = toRef(props, 'data')
   const typeOptionList = ref(['Per_Night', 'Per_Stay']);
   const type_option = ref([]);
   // For initial value of addMultiItem
   const formInitialValues = reactive({
       addMultiItem: [
           {
               title: '',
               description: '',
               id: '',
               image: '',
           }
       ]
   })
   const getSlidesEdit = async () => {
    isLoading.value = true
    try {
      const res = await axios.get('/api/addventure-content');
      if (res.data.status) {
        const bannerData = res.data.data;
        console.log(bannerData,"bannerData");
        if(bannerData.length){
                formInitialValues.addMultiItem = bannerData;
            }
            else{
                formInitialValues.addMultiItem = [{
                    title: '',
                    description: '',
                    id: '',
                    image: '',
                }]
            }
       
       
      }
    } catch (error) {
      toast(error.response?.data?.message || 'Error loading data', 'error').show();
    } finally {
      isLoading.value = false;
    }
  };

   // For get pervious array item
   const perviousArrayItem = (value) => {
       let perviousItem = value[value.length - 1]
       return perviousItem.value
   }
   const getUploadFile = (value, idx, fields) => {
      uploadFile.value = value
      fields[idx].value.image = uploadFile.value.filename 
    }
   // For Delete item
   const deleteItem = (id, remove) => {
       dialog('Are you sure you want to remove?', confirm).show()

       function confirm(){
           axios.delete(`/api/delete-addventure/${id}`).then(res => {
               if(res.data.status){
                   toast(res.data.message, 'success').show()
                   getSlidesEdit();
                   setTimeout(() => {
                       emit('emitCurrentTab', 'Additional Charges')
                   }, 400)
               }
           }).catch(error => {
               toast(error.response.data.message, 'error').show()
           })
       }
   }


   // For form on submit
   const onSubmit = (v) => {
       isSubmitLoading.value = true

       axios.post('/api/save-addventure', {
        addventure_content: v.addMultiItem,
           //home_id: route.params.id
       }).then(res => {
           if(res.data.status){
               toast(res.data.message, 'success').show()
               getSlidesEdit();
               setTimeout(() => {
                   isSubmitLoading.value = false
                   emit('emitCurrentTab', 'Gallery')
               }, 400)
           }
       }).catch(error => {
           toast(error.response.data.message, 'error').show()
           isSubmitLoading.value = false
       })
   }
   onMounted(() => {
    getSlidesEdit();
  });
</script>
