import './bootstrap'
import './assets/scss/app.scss'
import '@utils/auth'
import router from './routes'
import veeValidate from './utils/vee-validate'
import store from './store'
import CKEditor from '@ckeditor/ckeditor5-vue'


import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

import VueCookies from 'vue-cookies'

import App from './App.vue'

import { createApp } from 'vue'

const app = createApp(App)

app.component('DatePicker', VueDatePicker)

// console.log('working', process.env.NODE_ENV)

app.provide("cookies", VueCookies)
app.provide("store", store)
app.use(veeValidate)
app.use(CKEditor)
app.use(router)
app.mount("#app")
