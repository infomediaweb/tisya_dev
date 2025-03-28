<template>
    <div class="page-wrap">
      <!-- Page Title Section -->
      <div class="page-title mb-4">
        <div class="row gy-3 align-items-center">
          <div class="col align-self-end">
            <h1 class="h2 mb-0">Footer Banner Content</h1>
          </div>
        </div>
      </div>
  
      <!-- Main Section -->
      <section class="section">
        <!-- Loading Spinner -->
        <div class="d-flex justify-content-center py-5" v-if="isLoading">
          <div class="spinner-border" role="status"></div>
        </div>
         
        <!-- Form Content (shown after loading is complete) -->
        <div class="page-content" v-else>
          <Form @submit="onSubmit" v-slot="{ errors, values }" :initial-values="formInitialValues">
            <!-- Title Input -->
            <div class="row">
                  <div class="col-12">
                    <div class="form-group title-editor">
                      <label for="title">Title <span class="text-danger">*</span></label>
                      <ckeditor
                        name="title"
                        :toolbar="['italic', 'fontColor']"
                        v-model:data="slideEdit.title"
                        rules="required"
                      />
                    </div>
                  </div>
              <!-- Subtitle Input -->
                    <div class="col-12">
                      <div class="form-group">
                        <label for="sub_title">Subtitle <span class="text-danger">*</span></label>
                        <Field
                          type="text"
                          name="sub_title"
                          class="form-control"
                          :class="{'border-danger': errors.sub_title}"
                          v-model="slideEdit.sub_title"
                          rules="required"
                        />
                      </div>
                    </div>
            </div>
            <div class="row">
                  <div class="col-6">
                    <div class="form-group">
                      <label for="sub_title">Image Banner First <span class="text-danger">*</span></label>
                      <UploadFile 
                          name="image_banner_first"
                          id="image"
                          fileType="image"
                          acceptType="image/*"
                          info="Max size: 1mb | Image size: 100px . 100px"
                          changeText="To change the icon please click / drag new icon here!"
                          size="1"
                          ratio="15%"
                          :filePath="slideEdit.image_banner_first_show"
                          :fileName="slideEdit.image_banner_first"
                          :rules="slideEdit.image_banner_first ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                          @emitUploadFile="getUploadFileFirst"
                          :isDeleteDisabled="true"
                          />
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group">
                      <label for="sub_title">Image Banner Second <span class="text-danger">*</span></label>
                      <UploadFile 
                          name="image_banner_second"
                          id="image"
                          fileType="image"
                          acceptType="image/*"
                          info="Max size: 1mb | Image size: 100px . 100px"
                          changeText="To change the icon please click / drag new icon here!"
                          size="1"
                          ratio="15%"
                          :filePath="slideEdit.image_banner_second_show"
                          :fileName="slideEdit.image_banner_second"
                          :rules="slideEdit.image_banner_second ? 'image|ext:jpg,jpeg,webp,svg,png|size:1048' : 'required|image|ext:jpg,jpeg,webp,svg,png|size:1048'" 
                          @emitUploadFile="getUploadFileSecond"
                          :isDeleteDisabled="true"
                          />
                    </div>
                  </div>
            </div>
            <!-- Table for Dynamic Fields -->
            <div class="table-wrap">
              <FieldArray name="addMultiItem" v-slot="{ fields, push, remove }">
                <div class="table-responsive">
                  <table class="table table-list mb-0 mw-lg">
                    <thead>
                      <tr>
                        <th class="fw-semibold" width="30%">Footer List Image</th>
                        <th class="fw-semibold">Footer List Title</th>
                        <th class="fw-semibold">Footer List Describtion</th>
                        <th style="width: 90px;" class="text-secondary text-center fw-semibold">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Loop through dynamic fields -->
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
                              :name="`list_title${index}`"
                              type="text"
                              class="form-control"
                              :class="{'border-danger': errors[`list_title${index}`]}"
                              v-model="obj.value.list_title"
                              rules="required"
                            />
                          </div>
                        </td>
                        <td>
                          <div class="form-group mb-0">
                            <Field
                              :name="`list_content${index}`"
                              type="text"
                              class="form-control"
                              :class="{'border-danger': errors[`list_content${index}`]}"
                              v-model="obj.value.list_content"
                              rules="required"
                            />
                          </div>
                        </td>
                        <td>
                          <ul v-if="fields.length > 1" class="action-btn-group justify-content-center mb-0 mw-0">
                            <li>
                              <button @click="remove(index)" type="button" class="btn p-1 fs-5 text-black">
                                <i class="icon-bi bi-trash"></i>
                              </button>
                            </li>
                          </ul>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
  
                <!-- Add More Button -->
                <div class="table-footer pt-3">
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <button
                        type="button"
                        class="btn btn-save btn-secondary"
                        @click="push({ list_content: '', list_title: '', image: ''})"
                      >
                        <i class="bi bi-plus-lg me-2"></i>Add More
                      </button>
                    </div>
                  </div>
                </div>
              </FieldArray>
            </div>
  
            <!-- Submit Button -->
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
  import { ref, reactive, onMounted, defineAsyncComponent } from 'vue';
  import { Form, Field, FieldArray } from 'vee-validate';
  import axios from 'axios';
  import ckeditor from '@components/ckeditor.vue';
  import { toast } from '@utils/toast';
  const UploadFile = defineAsyncComponent(() => import('@components/upload-file.vue'))
  const uploadFile = ref({})
  const uploadFileFirst = ref({})
  const uploadFileSecond = ref({})
  const isLoading = ref(true);
  const isSubmitLoading = ref(false);
  const submitApiUrl = ref(null)
  const slideEdit = ref([]);
  const formInitialValues = reactive({
    addMultiItem: [{ 
      list_title: '',
      list_content: '',
      image: '',
      
     }] 
  });
  
  // Fetch existing data from API
  const getSlidesEdit = async () => {
    try {
      const res = await axios.get('/api/footer-banner-content');
      if (res.data.status) {
        const bannerData = res.data.data[0] || res.data.data;
        slideEdit.value = bannerData;

        //console.log(slideEdit.value)


        const listContent = bannerData.list_content.map(item => ({
          list_title: item.list_title,
          list_content: item.list_content,
          image_url: item.image_url,
          image: item.image,
        }));
  
        formInitialValues.addMultiItem = listContent.length > 0 ? listContent : [{ list_content: '' }];
      }
    } catch (error) {
      toast(error.response?.data?.message || 'Error loading data', 'error').show();
    } finally {
      isLoading.value = false;
    }
  };
  
  // Form submission handler
  const onSubmit = async (values) => {
    console.log(values,"values");
    isSubmitLoading.value = true;
    
    const banner_id = slideEdit.value.id 
    if(banner_id){
           var submitApiUrl = `/api/home-footer-banner/${banner_id}`
        }
    else{
       var submitApiUrl = '/api/home-footer-banner'
    }
//console.log(submitApiUrl,"url");
  
    try {
      const response = await axios.post(submitApiUrl, {
        title: values.title.replace(/^<[^>]+>|<[^>]+>$/g, ''),
        subtitle: values.sub_title,
        image_banner_first: uploadFileFirst.value.filename,
        image_banner_second: uploadFileSecond.value.filename,
        addMultiItem: values.addMultiItem
      });
      if (response.data.status) {
        toast(response.data.message, 'success').show();
        getSlidesEdit(); 
      }
    } catch (error) {
      toast(error.response?.data?.message || 'Submission failed', 'error').show();
    } finally {
      isSubmitLoading.value = false;
    }
  };
  

  const getUploadFile = (value, idx, fields) => {
      uploadFile.value = value
      fields[idx].value.image = uploadFile.value.filename 
    }

    const getUploadFileFirst = (value) => {
        uploadFileFirst.value = value
    }
    const getUploadFileSecond = (value) => {
        uploadFileSecond.value = value
    }

// For delete uploaded file
const deleteUploadFile = (value, idx, fields) => {
        axios.get(`/api/multiple-image-delete/${id}`).then(res => {
            if(res.data.status){
                toast(res.data.message, 'success').show()
                locationEditData.value.image_name = ''
                locationEditData.value.image = '' 
            }
        }).catch(error => {
            toast(error.response.data.message, 'error').show()
        })
    }

  // Fetch data when the component is mounted
  onMounted(() => {
    getSlidesEdit();
  });
  </script>
  
  <style scoped>
  .page-wrap {
    padding: 20px;
  }
  
  .table-list {
    width: 100%;
  }
  
  .spinner-border {
    color: #007bff;
  }
  </style>
  