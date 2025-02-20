<template>
    <div class="card w-500px">
        <div class="card-header !items-center">
            <h5 class="fw-bold">{{ title }}</h5>
        </div>
        <div class="card-body">
            <div class="form">
                <div class="form-group mb-4" v-if="$props.confirm_type == 'pending'">
                    <label class="fw-bold">Batas Waktu Upload Ulang</label>
                    <VueCtkDateTimePicker v-model="form.payment_limit_date" v-bind="date_config">
                    </VueCtkDateTimePicker>
                </div>
                <div class="form-group">
                    <label class="fw-bold" for="note">Catatan</label>
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

        const { basePatchData } = useAxios();

        const form = ref({
            note: "",
            payment_limit_date: "",
            confirm_type: props.confirm_type
        });

        function saveButton() {
            /*Update Data*/
            basePatchData(`transaction-payment-process/${props.payment_id}`, form.value)
                .then(() => {
                    console.log('success')
                    emit(Modal.EVENT_PROMPT, 1);
                })
                .catch(() => {
                    console.log('failed')
                    emit(Modal.EVENT_PROMPT, 0);
                });
        }

        function rejectButton() {
            emit(Modal.EVENT_PROMPT, 0);
        }

        const date_config = ref(
            {
                'range': false,
                'no-shortcuts': true,
                'no-label': true,
                'no': true,
                'formatted': 'hh:mm',
                'format': 'hh:mm',
                'locale': "id",
                'only-time': true,
                'label': 'Batas Waktu Upload Ulang'
            }
        )

        return {
            rejectButton,
            saveButton,
            form,
            date_config
        }
    }
}
</script>
