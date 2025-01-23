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
                <div class="card card-flush py-4">
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
                            <div class="mb-5  col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Tipe</label>
                                <select class="form-control mb-2" v-model="form.type">
                                    <option value="reviewer">Reviewer</option>
                                    <option value="speaker">Speaker</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('type')">{{
                                    getMessage('type') }}
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
                            <div class="mb-5 col-12 fv-row fv-plugins-icon-container">
                                <label class="form-label">
                                    Curiculum Vitae
                                </label>
                                <ckeditor :editor="editor" v-model="form.biography" :config="editor_data.config">
                                </ckeditor>
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control mb-2" v-model="form.phone">
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Institusi</label>
                                <input type="text" class="form-control mb-2" v-model="form.institution">
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Kota</label>
                                <input type="text" class="form-control mb-2" v-model="form.city">
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Provinsi</label>
                                <input type="text" class="form-control mb-2" v-model="form.province">
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control mb-2" v-model="form.job_type_code">
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Deskripsi</label>
                                <input type="text" class="form-control mb-2" v-model="form.desc">
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control mb-2" v-model="form.slug">
                            </div>
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

        const title = form_props.edit_mode ? "Edit Staff" : "Tambah Staff"
        const breadcrumb_list = ["Klien", form_props.edit_mode ? "Edit" : "Tambah"];

        const editor = ClassicEditor
        const editor_data = reactive({
            config: {},
        })

        const form = reactive({
            id: '',
            status: 100,
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            type: '',
            biography: '',
            phone: '',
            institution: '',
            city: '',
            province: '',
            job_type_code: '',
            image: '',
            desc: '',
            slug: '',
            identity_photo: '',
            role_id: '',
        })

        if (form_props.edit_mode) {
            getData('users/' + param_id)
                .then((data) => {
                    form.id = data.result.id
                    form.status = data.result.status
                    form.name = data.result.name
                    form.email = data.result.email
                    form.type = data.result.type
                    form.biography = data.result.biography ?? ''
                    form.phone = data.result.phone
                    form.institution = data.result.institution
                    form.city = data.result.city
                    form.province = data.result.province
                    form.job_type_code = data.result.job_type_code
                    form.image = data.result.image
                    form.desc = data.result.desc
                    form.role_id = data.result.role_id
                    form.slug = data.result.slug
                    form.identity_photo = data.result.identity_photo
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
