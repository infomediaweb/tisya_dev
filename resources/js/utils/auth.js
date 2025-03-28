import axios from 'axios'
import store from '../store'
import router from '../routes'
import cookies from 'vue-cookies'
import { ref } from 'vue'
import { toast } from '@utils/toast'


axios.defaults.baseURL = "https://tisya.tempsite.in/"

axios.interceptors.request.use(function (config) {
    if(store.getters.user?.token){
        config.headers['Authorization'] = 'Bearer ' + store.getters.user?.token
    }
    return config
}, function (error) {
    return Promise.reject(error)
})

axios.interceptors.response.use(function (response) {
    return response
}, function (error) {
    if (error.response.status === 401) { 
        store.dispatch('user', '')
        router.push({name: 'login'})
    }
    return Promise.reject(error)
})


// For session expired 
const events = ref(['click', 'mousemove', 'mousedown', 'touchmove', 'scroll', 'keypress', 'load'])
const sessionTimer = ref(null)

const setSessionTimer = () => {
    sessionTimer.value = setTimeout(sessionExpired, 20 * 60 * 1000)
}

const sessionExpired = () => {
    if(router.currentRoute.value.name != "login"){
        store.dispatch('user', '')
        router.push({name: 'login'})

        toast('Oops! your session has expired. Please log in again to continue.', 'error').show()
    } 
}

const resetSessionTimer = () => {
    clearTimeout(sessionTimer.value)
    setSessionTimer()
}

events.value.forEach(event => {
    window.addEventListener(event, resetSessionTimer, { passive: true })
})

setSessionTimer()

