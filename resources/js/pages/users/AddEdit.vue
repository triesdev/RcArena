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
                <div class="card card-flush py-4 mb-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Detail</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control mb-2" v-model="form.name">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('name')">
                                    {{ getMessage('name') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">User Code</label>
                                <input type="text" class="form-control mb-2" v-model="form.user_code">
                            </div>
                            <div class="mb-5  col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">No Telepon</label>
                                <input type="text" class="form-control mb-2" v-model="form.phone_number">
                                <div class="fv-plugins-message-container invalid-feedback"
                                    v-if="getStatus('phone_number')">
                                    {{ getMessage('phone_number') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control mb-2" v-model="form.email"
                                    autocomplete="new-password">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('email')">
                                    {{ getMessage('email') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">
                                    <span v-if="form_props.edit_mode">Ganti</span> Password
                                </label>
                                <input type="password" class="form-control mb-2" v-model="form.password"
                                    autocomplete="new-password">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('password')">
                                    {{ getMessage('password') }}
                                </div>
                                <span class="text-small text-gray-600" v-if="form_props.edit_mode">
                                    Kosongkan bila tidak akan mengganti password
                                </span>
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">
                                    Konfirmasi <span v-if="form_props.edit_mode">Ganti</span> Password
                                </label>
                                <input type="password" class="form-control mb-2" autocomplete="new-password"
                                    v-model="form.password_confirmation">
                                <div class="fv-plugins-message-container invalid-feedback"
                                    v-if="getStatus('password_confirmation')">
                                    {{ getMessage('password_confirmation') }}
                                </div>
                            </div>
                            <div class="mb-5  col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Tipe Akun</label>
                                <select class="form-control mb-2" v-model="form.user_type">
                                    <option value="cms">Admin Panel</option>
                                    <option value="mobile">Mobile Apps</option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback"
                                    v-if="getStatus('user_type')">
                                    {{ getMessage('user_type') }}
                                </div>
                            </div>
                            <div class="mb-5  col-md-6 fv-row fv-plugins-icon-container">
                                <div v-if="form.user_type === 'mobile'">
                                    <label class="form-label">Tipe Mobile Apps</label>
                                    <select class="form-control mb-2" v-model="form.user_type_mobile">
                                        <option value="coordinator">Koordinator</option>
                                        <option value="regular">Regular</option>
                                    </select>
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('user_type_mobile')">
                                        {{ getMessage('user_type_mobile') }}
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5  col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Status</label>
                                <select class="form-control mb-2" v-model="form.is_active">
                                    <option value="1">Aktif</option>
                                    <option value="0">Non Aktif</option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback"
                                    v-if="getStatus('is_active')">
                                    {{ getMessage('is_active') }}
                                </div>
                            </div>
                            <div class="mb-5  col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Role</label>
                                <select class="form-control mb-2" v-model="form.role_id">
                                    <option :value="role.id" v-for="role in form_props.roles">{{ role.name }}
                                    </option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('role_id')">
                                    {{ getMessage('role_id') }}
                                </div>
                            </div>
                            <!-- image_uri: '', -->
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <router-link to="/panel/users" class="btn btn-light me-5">Batal</router-link>
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
        </div>
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import { reactive } from "vue";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import { useRouter, useRoute } from "vue-router";
import { useFilterStore } from "../../src/store_filter";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    components: { Breadcrumb },
    setup() {
        const { postData, getData, patchData } = useAxios()
        const router = useRouter()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()
        const route = useRoute()
        const { app_store } = useFilterStore()

        // Cek Mode
        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
            clients: [],
            roles: [],
        })

        const param_id = route.params.id
        form_props.edit_mode = param_id !== 'add'

        const title = form_props.edit_mode ? "Edit User" : "Tambah User"
        const breadcrumb_list = ["User", form_props.edit_mode ? "Edit" : "Tambah"];

        const editor = ClassicEditor
        const editor_data = reactive({
            config: {},
        })

        const form = reactive({
            id: '',
            name: '',
            user_code: '',
            phone_number: '',
            email: '',
            password: '',
            password_confirmation: '',
            user_type: '',
            user_type_mobile: '',
            is_active: '',
            role_id: '',
            image_uri: '',
        })

        if (form_props.edit_mode) {
            getData('users/' + param_id)
                .then((data) => {
                    form.id = data.result.id
                    form.name = data.result.name
                    form.user_code = data.result.user_code
                    form.phone_number = data.result.phone_number
                    form.email = data.result.email
                    form.user_type = data.result.user_type
                    form.user_type_mobile = data.result.user_type_mobile
                    form.is_active = data.result.is_active
                    form.role_id = data.result.role_id
                    form.image_uri = data.result.image_uri
                })
        }

        function createData() {
            form_props.is_loading = true
            postData('users', form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/users')
                    resetErrors()
                } else {
                    console.log("Disini", data)
                    setErrors(data.errors)
                }
            })
        }

        function editData() {
            form_props.is_loading = true
            patchData('users/' + param_id, form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/users')
                    resetErrors()
                } else {
                    setErrors(data.errors)
                }
            })
        }

        function loadRoles() {
            getData('roles-list')
                .then((data) => {
                    form_props.roles = data.result
                })
        }

        loadRoles()

        return {
            breadcrumb_list,
            title,
            form,
            form_props,
            app_store,
            editor,
            editor_data,
            createData,
            getStatus,
            getMessage,
            editData
        }
    }
}
</script>
