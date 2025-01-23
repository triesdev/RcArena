<template>
    <div>
        <div class="badge badge-light-success fw-bold" v-if="status === 1">Aktif</div>
        <div class="badge badge-light-secondary fw-bold" v-if="status === 0">Non Aktif</div>
        <div class="badge badge-light-warning fw-bold" v-if="status !== 1 && status !== 0">{{status}}</div>
    </div>
</template>
<script>
export default {
    props: {
        status: {
            type: Number
        }
    },
    setup(props){

    }
}
</script>
