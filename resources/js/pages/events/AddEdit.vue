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
                            <!-- user_organizer_id
                                     -->
                            <div class="row">
                                <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Nama</label>
                                    <input type="text" class="form-control mb-2" v-model="form.name">
                                    <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('name')">
                                        {{ getMessage('name') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Deskripsi</label>
                                    <textarea class="form-control mb-2" v-model="form.description"></textarea>
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('description')">
                                        {{ getMessage('description') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Lokasi</label>
                                    <input type="text" class="form-control mb-2" v-model="form.location_name">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('location_name')">
                                        {{ getMessage('location_name') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Alamat</label>
                                    <input type="text" class="form-control mb-2" v-model="form.location_address">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('location_address')">
                                        {{ getMessage('location_address') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Jadwal</label>
                                    <VueEditor v-model="form.schedules"></VueEditor>
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('schedules')">
                                        {{ getMessage('schedules') }}
                                    </div>
                                </div>

                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Launching</label>
                                    <input type="datetime-local" class="form-control mb-2"
                                        v-model="form.event_launch_at">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('event_launch_at')">
                                        {{ getMessage('event_launch_at') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Mulai Penjualan Tiket</label>
                                    <input type="date" class="form-control mb-2" v-model="form.ticket_purchasing_at">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('ticket_purchasing_at')">
                                        {{ getMessage('ticket_purchasing_at') }}
                                    </div>
                                </div>

                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Tanggal Acara</label>
                                    <input type="date" class="form-control mb-2" v-model="form.event_date">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('event_date')">
                                        {{ getMessage('event_date') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Penyelenggara</label>
                                    <select class="form-control mb-2" v-model="form.user_organizer_id">
                                        <option value="">Pilih Penyelenggara</option>
                                        <option v-for="organizer in form_props.organizers" :value="organizer.id">{{
                                            organizer.name
                                            }}</option>
                                    </select>
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('user_organizer_id')">
                                        {{ getMessage('user_organizer_id') }}
                                    </div>
                                </div>

                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Mulai Acara</label>
                                    <input type="datetime-local" class="form-control mb-2" v-model="form.event_start">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('event_start')">
                                        {{ getMessage('event_start') }}
                                    </div>
                                </div>
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Selesai Acara</label>
                                    <input type="datetime-local" class="form-control mb-2" v-model="form.event_end">
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('event_end')">
                                        {{ getMessage('event_end') }}
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
                                <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                    <label class="form-label text-slate-400">Status</label>
                                    <select class="form-control mb-2" v-model="form.is_active">
                                        <option value="1">Aktif</option>
                                        <option value="0">Non Aktif</option>
                                    </select>
                                    <div class="fv-plugins-message-container invalid-feedback"
                                        v-if="getStatus('is_active')">
                                        {{ getMessage('is_active') }}
                                    </div>
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

        const title = form_props.edit_mode ? "Edit Event" : "Tambah Event"
        const breadcrumb_list = ["Event", form_props.edit_mode ? "Edit" : "Tambah"];

        const form = reactive({
            id: '',
            user_organizer_id: '',
            name: '',
            image_uri: '',
            description: '',
            event_launch_at: '',
            ticket_purchasing_at: '',
            location_name: '',
            location_address: '',
            event_date: '',
            event_start: '',
            event_end: '',
            schedules: '',
            is_active: '',
        })

        if (form_props.edit_mode) {
            getData('events/' + param_id)
                .then((data) => {
                    form.id = data.result.id
                    form.user_organizer_id = data.result.user_organizer_id
                    form.name = data.result.name
                    form.image_uri = data.result.image_uri
                    form.description = data.result.description
                    form.event_launch_at = data.result.event_launch_at
                    form.ticket_purchasing_at = data.result.ticket_purchasing_at
                    form.location_name = data.result.location_name
                    form.location_address = data.result.location_address
                    form.event_date = data.result.event_date
                    form.event_start = data.result.event_start
                    form.event_end = data.result.event_end
                    form.schedules = data.result.schedules
                    form.is_active = data.result.is_active
                })
        }

        function createData() {
            form_props.is_loading = true
            postData('events', form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/events')
                    resetErrors()
                } else {
                    setErrors(data.errors)
                }
            })
        }

        function editData() {
            form_props.is_loading = true
            patchData('events/' + param_id, form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/events')
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

        function loadUsers() {
            getData('users', { role_id: '5', per_page: 100 }).then((data) => {
                form_props.organizers = data.result.data
            })
        }

        loadUsers()

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
