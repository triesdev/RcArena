<template>
    <div class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1 d-flex">
        <div v-for="(ls, l) in list" class="d-flex me-2">
            <div class="breadcrumb-item text-muted">
                <span class="text-muted text-hover-primary">{{ls}}</span>
            </div>
            <div class="breadcrumb-item" v-if="(l + 1) < list.length" >
                <span class="bullet bg-gray-400 w-5px h-2px"></span>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        list: {
            type: Array
        }
    }
}
</script>
