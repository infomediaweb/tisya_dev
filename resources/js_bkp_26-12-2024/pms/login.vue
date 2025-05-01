<template>
     <div class="login-page" :style="`background-image: url(${backgroundImage});`">
          <div class="wrapper">
               <div class="container">
                    <div class="row justify-content-end">
                         <div class="col-12 col-lg-auto">
                              <div class="form-box d-flex login-form">
                                   <div class="my-auto col-12 py-5">
                                        <div class="form-logo mb-5 pb-1">
                                             <img src="@assets/images/logo.png" alt="V are FAMILY">
                                        </div>
                                        <Form @submit="onSubmit" v-slot="{errors}">
                                             <div class="form-group mb-4">
                                                  <Field
                                                       type="text"
                                                       name="email"
                                                       placeholder="Username"
                                                       class="form-control form-control-lg bg-transparent"
                                                       :class="{'border-danger': errors.email}"
                                                       rules="required|email"
                                                  />
                                             </div>
                                             <div class="form-group mb-4">
                                                  <div class="password-group">
                                                       <Field
                                                            :type="passwordType"
                                                            name="password"
                                                            placeholder="Password"
                                                            class="form-control form-control-lg bg-transparent"
                                                            :class="{'border-danger': errors.password}"
                                                            rules="required"
                                                       />
                                                       <button
                                                            type="button"
                                                            class="input-group-text"
                                                            @click="() => passwordType = passwordType === 'password' ? 'text' : 'password'">
                                                            <i
                                                                 :class="['fs-5', {
                                                                      'bi bi-eye-fill': passwordType === 'password',
                                                                      'bi bi-eye-slash-fill': passwordType === 'text'
                                                                 }]">
                                                            </i>
                                                       </button>
                                                  </div>

                                             </div>
                                             <div class="form-group">
                                                  <button type="submit" class="btn btn-primary btn-lg w-100">
                                                       <div v-if="isLoading" class="spinner-border spinner-border-lg" role="status"></div>
                                                       <span v-else>LOGIN</span>
                                                  </button>
                                             </div>
                                        </Form>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</template>
<script setup>
     import backgroundImage from '@assets/images/login-bg.jpg'
     import logoImage from '@assets/images/logo-bg.png'
     import { Form, Field, ErrorMessage } from 'vee-validate'
     import axios from 'axios'
     import { ref, inject, onMounted } from 'vue'
     import { useRouter } from 'vue-router'
     import { toast } from '@utils/toast'

     const store = inject('store')
     const cookies = inject('cookies')
     const router = useRouter()
     const passwordType = ref('password')
     const isLoading = ref(false)

     const onSubmit = (v) => {
          isLoading.value = true
          axios.post('/api/pms-login', {
               email: v.email,
               password: v.password
          }).then(res => {
               //cookies.set('v-are-family-token', res.data.token)

               store.dispatch('user', res.data)

               router.push({name: 'booking-list'})
               toast(res.data.message, 'success').show()
               isLoading.value = false



          }).catch(error => {
               let message = error.response.data.message
               toast(message, 'error').show()
               isLoading.value = false
          })
     }

     const checkAuth = () => {
        if(store.getters.user?.token){
            router.push({name: 'booking-list'})
        }
     }

     onMounted(() => {
          checkAuth()
     })

</script>
