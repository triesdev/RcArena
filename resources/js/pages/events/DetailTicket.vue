<template>
    <div class="card card-flush mb-4">
        <div class="card-body">
            <div class="flex justify-between mb-4">
                <div class="text-xl font-bold mb-4">Kelas</div>
                <div>
                    <button @click="showAddModal" class="btn btn-primary btn-sm text-center">
                        Tambah Kelas
                    </button>
                </div>
            </div>
            <div>
                <div class="w-full rounded-xl p-4 bg-slate-50 mb-2" v-for="item in form_props.classes">
                    <div class="flex justify-content-between border-b border-black">
                        <div>
                            <div>
                                <span class="font-semibold">{{ item.name }}</span> <span class="ml-2">({{
                                    item.ticket.length }} varian)</span>
                            </div>
                            <div class="text-sm">
                                <span>Rp {{ $filter.currency(item.price) }}</span><span class="ml-2">
                                    {{ item.is_active === 1 ? "Aktif" : "Tidak Aktif" }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle btn-sm" data-toggle="dropdown">
                                    Aksi
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <button class="dropdown-item" @click="showEditModal(item)">
                                        Edit
                                    </button>
                                    <button class="dropdown-item" @click="showAddVariant(item)">
                                        Tambah Varian
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between my-1" v-for="ticket in item.ticket">
                        <div>
                            <span class="font-semibold">{{ ticket.name }}</span>
                        </div>
                        <div>
                            <span @click="showEditVariant(item, ticket)"
                                class="mx-1 fw-semibold text-blue-500 cursor-pointer hover:text-blue-600">edit</span>
                            <span class="mx-1 fw-semibold text-red-500 cursor-pointer hover:text-red-600">hapus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editAddModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fw-bolder">Kelas</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                <label class="form-label text-slate-400">Nama Kelas</label>
                                <input type="text" class="form-control form-control-sm mb-2" v-model="form.name">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('name')">
                                    {{ getMessage('name') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                <label class="form-label text-slate-400">Harga</label>
                                <input type="text" class="form-control mb-2" v-model="form.price">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('price')">
                                    {{ getMessage('price') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                <label class="form-label text-slate-400">Hadiah</label>
                                <VueEditor class="mb-2" v-model="form.reward"></VueEditor>
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('reward')">
                                    {{ getMessage('reward') }}
                                </div>
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
                        <div class="d-flex justify-content-end">
                            <button v-if="!form_props.edit_mode" :disabled="form_props.is_loading" @click="createClass"
                                class="btn btn-primary">
                                <span v-if="!form_props.is_loading">Tambah</span>
                                <span v-if="form_props.is_loading">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <button v-if="form_props.edit_mode" :disabled="form_props.is_loading" @click="editClass"
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
        </div>
        <div class="modal fade" id="editAddVariantModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fw-bolder">Varian</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                <label class="form-label text-slate-400">Nama</label>
                                <input type="text" class="form-control form-control-sm mb-2"
                                    v-model="form_variant.name">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('name')">
                                    {{ getMessage('name') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                <label class="form-label text-slate-400">Kuota</label>
                                <input type="text" class="form-control mb-2" v-model="form_variant.quota">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('price')">
                                    {{ getMessage('price') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
                                <label class="form-label text-slate-400">Harga</label>
                                <input type="text" class="form-control mb-2" v-model="form_variant.price">
                                <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('price')">
                                    {{ getMessage('price') }}
                                </div>
                            </div>
                            <div class="mb-5 col-md-6 fv-row fv-plugins-icon-container">
                                <label class="form-label text-slate-400">Status</label>
                                <select class="form-control mb-2" v-model="form_variant.is_active">
                                    <option value="1">Aktif</option>
                                    <option value="0">Non Aktif</option>
                                </select>
                                <div class="fv-plugins-message-container invalid-feedback"
                                    v-if="getStatus('is_active')">
                                    {{ getMessage('is_active') }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button v-if="!form_props.edit_mode_variant" :disabled="form_props.is_loading"
                                @click="createVariant" class="btn btn-primary">
                                <span v-if="!form_props.is_loading">Tambah</span>
                                <span v-if="form_props.is_loading">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                            <button v-if="form_props.edit_mode_variant" :disabled="form_props.is_loading"
                                @click="editVariant" class="btn btn-primary">
                                <span v-if="!form_props.is_loading">Simpan</span>
                                <span v-if="form_props.is_loading">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { reactive, ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import { VueEditor } from "vue3-editor";

export default {
    components: { VueEditor },
    setup() {
        const route = useRoute()
        const { postData, getData, patchData } = useAxios()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()

        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
            edit_mode_variant: false,
            classes: [],
        })

        const form = reactive({
            id: "",
            event_id: "",
            event_name: "",
            name: "",
            price: "",
            reward: "",
            is_active: "",
        })

        const form_variant = reactive({
            id: "",
            class_id: "",
            event_id: "",
            ticket_bundle_id: "",
            name: "",
            ticket_type: "",
            price: "",
            quota_left: "",
            quota: "",
        })

        let editAddModal = ref(null);
        onMounted(() => {
            const editAddModalElement = document.getElementById('editAddModal');
            if (editAddModalElement) {
                editAddModal.value = new bootstrap.Modal(editAddModalElement);
            }
        });

        let editAddVariantModal = ref(null);
        onMounted(() => {
            const editAddVariantModalElement = document.getElementById('editAddVariantModal');
            if (editAddVariantModalElement) {
                editAddVariantModal.value = new bootstrap.Modal(editAddVariantModalElement);
            }
        });

        function loadClasses() {
            getData("panel-classes", {
                event_id: route.params.id
            }).then((data) => {
                form_props.classes = data.result
            })
        }

        loadClasses()

        function showAddModal() {
            form_props.edit_mode = false
            resetErrors()
            form.id = ""
            form.event_id = route.params.id
            form.event_name = ""
            form.name = ""
            form.price = ""
            form.reward = ""

            editAddModal.value.show();
        }

        function showEditModal(data) {
            form_props.edit_mode = true
            editAddModal.value.show();
            resetErrors()

            form.id = data.id
            form.event_id = data.event_id
            form.event_name = data.event_name
            form.name = data.name
            form.price = data.price
            form.reward = data.reward
            form.is_active = data.is_active
        }

        function createClass() {
            postData("panel-classes", form).then((data) => {
                if (data.success) {
                    form_props.is_loading = false
                    loadClasses()
                    editAddModal.value.hide()
                } else {
                    form_props.is_loading = false
                    setErrors(data.errors)
                }
            })
        }

        function editClass() {
            patchData("panel-classes/" + form.id, form).then((data) => {
                if (data.success) {
                    form_props.is_loading = false
                    loadClasses()
                    editAddModal.value.hide()
                } else {
                    form_props.is_loading = false
                    setErrors(data.errors)
                }
            })
        }

        function showAddVariant(item) {
            form_props.edit_mode_variant = false
            resetErrors()

            form_variant.id = ""
            form_variant.class_id = item.id
            form_variant.event_id = route.params.id
            form_variant.ticket_bundle_id = null
            form_variant.name = ""
            form_variant.ticket_type = "regular"
            form_variant.price = ""
            form_variant.quota_left = ""
            form_variant.quota = ""

            editAddVariantModal.value.show();
        }

        function showEditVariant(item, ticket) {
            form_props.edit_mode_variant = true
            editAddVariantModal.value.show();
            resetErrors()

            form_variant.id = ticket.id
            form_variant.class_id = item.id
            form_variant.event_id = item.event_id
            form_variant.ticket_bundle_id = ticket.ticket_bundle_id
            form_variant.name = ticket.name
            form_variant.ticket_type = ticket.ticket_type
            form_variant.price = ticket.price
            form_variant.quota_left = ticket.quota_left
            form_variant.quota = ticket.quota
            form_variant.is_active = ticket.is_active
        }

        function createVariant() {
            postData("tickets", form_variant).then((data) => {
                if (data.success) {
                    form_props.is_loading = false
                    loadClasses()
                    editAddVariantModal.value.hide()
                } else {
                    form_props.is_loading = false
                    setErrors(data.errors)
                }
            })
        }

        function editVariant() {
            patchData("tickets/" + form_variant.id, form_variant).then((data) => {
                if (data.success) {
                    form_props.is_loading = false
                    loadClasses()
                    editAddVariantModal.value.hide()
                } else {
                    form_props.is_loading = false
                    setErrors(data.errors)
                }
            })
        }

        return {
            showAddModal,
            showEditModal,
            form_props,
            form,
            getStatus,
            createClass,
            getMessage,
            editClass,
            showAddVariant,
            showEditVariant,
            form_variant,
            createVariant,
            editVariant
        }
    }
}
</script>
