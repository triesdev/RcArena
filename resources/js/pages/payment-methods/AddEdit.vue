<template>
    <div class="d-flex flex-column flex-column-fluid" style="min-height: calc(100vh - 130px)">
        <div class="app-toolbar py-3 py-lg-6">
            <div class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ title }}</h1>
                    <Breadcrumb :list="breadcrumb_list"></Breadcrumb>
                </div>
            </div>
        </div>
        <div class="app-content flex-column-fluid">
            <div class="app-container container-xxl">
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Nama Bank</label>
                                    <input type="text" class="form-control mb-2" v-model="form.name">
                                    <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('name')">
                                        {{ getMessage('name') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Kode Bank</label>
                                    <input type="text" class="form-control mb-2" v-model="form.code">
                                    <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('code')">
                                        {{ getMessage('code') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Nama Akun</label>
                                    <input type="text" class="form-control mb-2" v-model="form.account_name">
                                    <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('account_name')">
                                        {{ getMessage('account_name') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Nomor Akun</label>
                                    <input type="text" class="form-control mb-2" v-model="form.account_number">
                                    <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('account_number')">
                                        {{ getMessage('account_number') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Tipe</label>
                                    <select class="form-control mb-2" v-model="form.type">
                                        <option value="bank">Bank</option>
                                        <option value="qris">Qris</option>
                                    </select>
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('type')">
                                        {{ getMessage('type') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Poster</label>
                                    <input type="file" class="form-control mb-2" @change="uploadPhoto"
                                           ref="poster_photo">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                         v-if="getStatus('image_uri')">
                                        {{ getMessage('image_uri') }}
                                    </div>
                                    <div :style="'background-image: url(' + form.image_uri + ')'"
                                         style="width: 200px; height: 200px; background-color: aliceblue; background-size: contain; background-position: center; background-repeat: no-repeat;"
                                         alt=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <router-link to="/panel/events" class="btn btn-light me-5">Batal</router-link>
                        <button v-if="!form_props.edit_mode" :disabled="form_props.is_loading" @click="createData"
                            class="btn btn-primary">
                            <span v-if="!form_props.is_loading">Tambah</span>
                            <span v-if="form_props.is_loading">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <button v-if="form_props.edit_mode" :disabled="form_props.is_loading" @click="editData"
                            class="btn btn-primary">
                            <span v-if="!form_props.is_loading">Simpan</span>
                            <span v-if="form_props.is_loading">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import { reactive, ref } from "vue";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import { useRouter, useRoute } from "vue-router";
import { handleFileProcessData } from "../../src/firebase_upload";
import { VueEditor } from "vue3-editor";

export default {
    components: { Breadcrumb, VueEditor },
    setup() {
        const { postData, getData, patchData } = useAxios()
        const router = useRouter()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()
        const route = useRoute()
        const poster_photo = ref(null)
        // Cek Mode
        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
            organizers: [],
        })

        const param_id = route.params.id
        form_props.edit_mode = param_id !== 'add'

        const title = form_props.edit_mode ? "Edit Metode Pembayaran" : "Tambah Metode Pembayaran"
        const breadcrumb_list = ["Metode Pembayaran", form_props.edit_mode ? "Edit" : "Tambah"];

        const form = reactive({
            id: '',
            name: '',
            code: '',
            account_name: '',
            account_number: '',
            type: 'bank',
            image_uri: '',
        })

        if (form_props.edit_mode) {
            getData('payment-methods/' + param_id)
                .then((data) => {
                    form.id = data.result.id
                    form.name = data.result.name
                    form.code = data.result.code
                    form.account_name = data.result.account_name
                    form.account_number = data.result.account_number
                    form.type = data.result.type
                    form.image_uri = data.result.image_uri
                })
        }

        function createData() {
            form_props.is_loading = true
            postData('payment-methods', form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/payment-methods')
                    resetErrors()
                } else {
                    setErrors(data.errors)
                }
            })
        }

        function editData() {
            form_props.is_loading = true
            patchData('payment-methods/' + param_id, form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/payment-methods')
                    resetErrors()
                } else {
                    setErrors(data.errors)
                }
            })
        }

        async function uploadPhoto() {
            const input = poster_photo.value;
            if (input && input.files.length > 0) {
                form.image_uri = await handleFileProcessData(input.files[0])
            }
        }

        return {
            breadcrumb_list,
            title,
            form,
            form_props,
            createData,
            getStatus,
            getMessage,
            editData,
            uploadPhoto,
            poster_photo
        }
    }
}
</script>
