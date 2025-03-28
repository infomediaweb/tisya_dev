<template>
    <button 
        class="form-control form-cus-select text-start" 
        type="button" 
        data-bs-toggle="dropdown" 
        data-bs-auto-close="outside"
        data-bs-offset="0,10"
        aria-expanded="false">
        <template v-if="multiSelectedValue.length && model.length">
            <span class="multi-select-name">{{ multiSelectedValue.join(', ') }}</span>
            <span class="multi-select-count">({{ multiSelectedValue.length }})</span>
        </template>
        <template v-else>{{ placeholder }}</template>
    </button>
    <ul class="dropdown-menu w-100">
        <template v-if="mapItemsArray.length">
            <li 
                v-for="(obj, index) in mapItemsArray"
                :key="index">
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input"
                        :name="`${name}-${index}`"
                        :id="`${name}-${index}`" 
                        :value="obj.id"
                        :data-name="obj.name"
                        v-model="model"
                        @change="emit('emitChange', $event)"
                    />
                    <label class="form-check-label" :for="`${name}-${index}`">
                        {{ obj.name }}
                    </label>
                </div>
            </li>
        </template>
        
        <li v-else class="no-result">No Result Found</li> 
    </ul>
</template>
<style scoped>
    .no-result{
        font-size: 13px;
        font-weight: 600;
    }
</style>
<script setup>
    import { ref, toRef, computed } from 'vue'

    const model = defineModel()
    const emit = defineEmits(['emitChange'])
    const props = defineProps({
        items: Array,
        itemName: String,
        itemId: String,
        name: String,
        placeholder: String,
    })

    const items = toRef(props, 'items')
    //const multiSelectedValue = ref([])

    const mapItemsArray = computed(() => {
        return items.value.map(item => ({
           id: item[props.itemId],
           name: item[props.itemName]
        }))
    })


    const multiSelectedValue = computed(() => {
        let selectValue = []

        mapItemsArray.value.forEach((value, idx) => {
            if(model.value.includes(value.id)){
                selectValue.splice(0, 0, value.name) 
            }
        })

        return selectValue
    })
    

    // const onChangeMultiHomeType = ({target}, value) => {
    //     if(target.checked){
    //         multiSelectedValue.value.push(value) 
    //     }
    //     else{
    //         let idx = multiSelectedValue.value.indexOf(value)
    //         multiSelectedValue.value.splice(idx, 1)
    //     }
    // }

</script>