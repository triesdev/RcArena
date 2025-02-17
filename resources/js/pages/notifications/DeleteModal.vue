<template>
    <div class="card p-5">
        <div class="border-bottom-1 border-bottom pb-3">
            <h3 class="mb-0">{{ title }}</h3>
        </div>
        <div class="py-3 w-200px w-md-300px w-lg-400px">
<!--            Ini adalah isi-->
        </div>
        <div class="text-end">
            <button class="btn btn-secondary mx-2 btn-sm" @click="decline">
                Batal
            </button>
            <button class="btn btn-danger mx-2 btn-sm" @click="accept">
                Hapus
            </button>
        </div>
    </div>
</template>

<script>
import {Modal} from "jenesius-vue-modal";
export default {
    props: {
        title: String
    },
    methods:{
        accept(){
            this.$emit(Modal.EVENT_PROMPT, 1);
        },
        decline(){
            this.$emit(Modal.EVENT_PROMPT, 0);
        }
    }
}
</script>
