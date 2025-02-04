<template>
    <div class="card w-500px">
        <div class="card-header !items-center">
            <h5 class="fw-bold">{{ title }}</h5>
        </div>
        <div class="card-body">

        </div>
        <div class="card-footer">
            <button @click="closeButton" class="btn btn-secondary mx-2 btn-sm">
                Selesai
            </button>
        </div>
    </div>
</template>
<script>
import {}
import useAxios from "../../src/service";
import {ref} from "vue";
export default {
    props: {
        title: String,
        payment_id: Number,
        confirm_type: String, // confirm, reject, reupload
    },
    setup(props, {emit}) {

        const { patchData } = useAxios();

        const form = ref({
            note: "",
            confirm_type: props.confirm_type
        });

        function saveButton() {
            /*Update Data*/
            patchData(`transaction-payment-process/${props.payment_id}`, form.value)
                .then(() => {
                    emit(Modal.EVENT_PROMPT, 1);
                })
                .catch(() => {
                    emit(Modal.EVENT_PROMPT, 0);
                });
        }

        function closeButton() {
            emit(Modal.EVENT_PROMPT, 0);
        }

        return {
            rejectButton,
            saveButton,
            form
        }
    }
}
</script>
