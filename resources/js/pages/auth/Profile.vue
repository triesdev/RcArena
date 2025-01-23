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
                <div class="form d-flex flex-column flex-lg-row fv-plugins-bootstrap5 fv-plugins-framework">
                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <div class="card card-flush py-4">
                            <div class="card-body pt-0">
                                <form>
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control mb-2" v-model="form.name">
                                        <div class="fv-plugins-message-container invalid-feedback"
                                            v-if="getStatus('name')">
                                            {{ getMessage('name') }}
                                        </div>
                                    </div>
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control mb-2" v-model="form.email"
                                            autocomplete="new-password">
                                        <div class="fv-plugins-message-container invalid-feedback"
                                            v-if="getStatus('email')">
                                            {{ getMessage('email') }}
                                        </div>
                                    </div>
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label class="form-label">
                                            <span>Ganti</span> Password
                                        </label>
                                        <input type="password" class="form-control mb-2" v-model="form.password"
                                            autocomplete="new-password">
                                        <div class="fv-plugins-message-container invalid-feedback"
                                            v-if="getStatus('password')">
                                            {{ getMessage('password') }}
                                        </div>
                                        <span class="text-small text-gray-600">
                                            Kosongkan bila tidak akan mengganti password
                                        </span>
                                    </div>
                                    <div class="mb-5 fv-row fv-plugins-icon-container">
                                        <label class="form-label">
                                            <span>Ganti</span>Password Konfirmasi
                                        </label>
                                        <input type="password" class="form-control mb-2" autocomplete="new-password"
                                            v-model="form.password_confirmation">
                                        <div class="fv-plugins-message-container invalid-feedback"
                                            v-if="getStatus('password_confirmation')">
                                            {{ getMessage('password_confirmation') }}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button :disabled="form_props.is_loading" @click="editData" class="btn btn-primary">
                                <span v-if="!form_props.is_loading">Simpan</span>
                                <span v-if="form_props.is_loading">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import { reactive } from "vue";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import Swal from "../../src/swal_toast"

export default {
    components: { Breadcrumb },
    setup() {
        const { getData, patchData } = useAxios()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()
        // Cek Mode
        const form_props = reactive({
            is_loading: false,
            errors: [],
        })

        const title = "Edit Profile"
        const breadcrumb_list = ["Edit Prodile"];

        const form = reactive({
            name: '',
            email: '',
            password: '',
            pin_key: '',
        })

        function editData() {
            form_props.is_loading = true
            patchData('profile', form).then((data) => {
                form_props.is_loading = false;
                Swal("Berhasil memperbarui data profil")
            })
        }

        function loadProfile() {
            getData('profile')
                .then((data) => {
                    form.name = data.result.name
                    form.email = data.result.email
                    form.password = data.result.password
                    form.pin_key = data.result.pin_key
                })
        }

        loadProfile()

        return {
            breadcrumb_list,
            title,
            form,
            form_props,
            getStatus,
            getMessage,
            editData
        }
    }
}
</script>
