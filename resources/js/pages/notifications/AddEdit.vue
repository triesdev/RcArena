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
                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                    <label class="form-label">Judul</label>
                                    <input type="text" class="form-control mb-2" v-model="form.title">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('title')">
                                        {{ getMessage('title') }}
                                    </div>
                                </div>
                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                    <label class="form-label">Pesan</label>
                                    <input type="text" class="form-control mb-2" v-model="form.message">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('message')">
                                        {{ getMessage('message') }}
                                    </div>
                                </div>
                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                    <label class="form-label">Halaman</label>
                                    <input type="text" class="form-control mb-2" v-model="form.page_route">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('page_route')">
                                        {{ getMessage('page_route') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <router-link to="/panel/notifications" class="btn btn-light me-5">Batal</router-link>
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
import { useRouter, useRoute } from "vue-router";

export default {
    components: { Breadcrumb },
    setup() {
        const { postData, getData, patchData } = useAxios()
        const router = useRouter()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()
        const route = useRoute()
        // Cek Mode
        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
        })

        const param_id = route.params.id
        form_props.edit_mode = param_id !== 'add'

        const title = form_props.edit_mode ? "Edit Role" : "Tambah Role"
        const breadcrumb_list = ["Role", form_props.edit_mode ? "Edit" : "Tambah"];

        const form = reactive({
            id: '',
            title: '',
            message: '',
            page_route: '',
        })

        if (form_props.edit_mode) {
            getData('notifications/' + param_id)
                .then((data) => {
                    form.id = data.result.id
                    form.title = data.result.title
                    form.message = data.result.message
                    form.page_route = data.result.page_route
                })
        }

        function createData() {
            form_props.is_loading = true
            postData('notifications', form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/notifications')
                    resetErrors()
                } else {
                    setErrors(data.errors)
                }
            })
        }

        function editData() {
            form_props.is_loading = true
            patchData('notifications/' + param_id, form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/notifications')
                    resetErrors()
                } else {
                    setErrors(data.errors)
                }
            })
        }

        return {
            breadcrumb_list,
            title,
            form,
            form_props,
            createData,
            getStatus,
            getMessage,
            editData
        }
    }
}
</script>
