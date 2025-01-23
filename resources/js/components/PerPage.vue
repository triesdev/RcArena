<template>
    <select class="form-select form-select-sm form-select-solid w-75px" v-model="data.per_page"
            @change="$emit('change-per-page', data.per_page)">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
    </select>
</template>
<script>
import {reactive} from "vue";
export default {
    props: ['value'],
    setup(){
        const data = reactive({
            per_page: 25
        })
        return {
            data
        }
    }
}
</script>
