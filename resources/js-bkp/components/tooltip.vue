<template></template>
<script setup>
    import { onMounted, watch, ref } from 'vue'
    import * as bootstrap from 'bootstrap'
    import $ from 'jquery'

    const tooltipTriggerList = ref()
    const tooltipList = ref()

    const props = defineProps({
        error: String,
        message: String
    })

    watch(props, (newVal, oldVal) => {
        tooltipList.value = [...tooltipTriggerList.value].filter(item => {
            if($(item).is(':focus')){
                if(newVal.error){
                    //let error = newVal.error.split(/(\d+)/)[1] ? newVal.message : newVal.error
                    let error = !isNaN(parseInt(newVal.error.charAt(newVal.error.length - 1))) ? newVal.message : newVal.error
                    const tooltip = new bootstrap.Tooltip(item, {
                        customClass: 'error-tooltip',
                        title: error,
                        trigger: 'focus manual'
                    })

                    tooltip.show()

                    item.addEventListener('show.bs.tooltip', () => {
                        tooltip.hide()
                    }) 
                }
                else{
                    const tooltip = bootstrap.Tooltip.getInstance(item)
                    tooltip?.dispose()
                }
            }
        })
    })

    onMounted(() => {
        tooltipTriggerList.value = document.querySelectorAll('[data-bs-toggle="tooltip"]') 
    })

</script>