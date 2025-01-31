<template>
    <div class="card w-500px">
        <div class="card-header !items-center">
            <h5 class="fw-bold">{{ title }}</h5>
        </div>
        <div class="card-body">
            <div class="form">
                <div class="form-group">
                    <label for="note">Catatan</label>
                    <textarea v-model="form.note" class="form-control" id="note" rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button @click="rejectButton" class="btn btn-secondary mx-2 btn-sm">
                Batal
            </button>
            <button @click="saveButton" class="btn btn-danger mx-2 btn-sm">
                Simpan
            </button>
        </div>
    </div>
</template>
<script>
import {Modal} from "jenesius-vue-modal";
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

        function rejectButton() {
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
