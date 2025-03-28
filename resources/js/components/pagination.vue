<template>
    <nav class="pagination-nav" aria-label="Page navigation example">
        <ul class="pagination justify-content-end mb-0 fw-normal">

            <li 
                v-for="(obj, index) in links" 
                :key="index"
                :class="['page-item', {'active': obj?.active}, { 'disabled': !obj?.url }]">
                <a 
                    class="page-link" 
                    href="javascript:void(0)" 
                    @click="callback(obj?.url)">
                    {{ obj?.label }}
                    
                    <template v-if="!obj?.label">
                        <i class="icon-chevron-thin-left" v-if="index == 0"></i>
                        <i class="icon-chevron-thin-right" v-if="!index == 0"></i>
                    </template>
                    
                </a>
            </li>
        </ul>
    </nav>
</template>
<script setup>
    import { inject, ref, watch, nextTick, onMounted } from 'vue'
    import { useRouter, useRoute } from 'vue-router'

    const store = inject('store')
    const route = useRoute()
    const router = useRouter()

    const paginationNumber = ref()

    const props = defineProps({
        links: Array
    })

    const emit = defineEmits(['pagination'])

    const callback = (v) => {
        emit('pagination', v.split('=')[1])
        //store.dispatch('currentPaginationNumber', v.split('=')[1])
    }
    
</script>