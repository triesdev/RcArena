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
                            <span @click="showEditModal(item)"
                                class="mx-1 fw-semibold text-blue-500 cursor-pointer hover:text-blue-600">edit</span>
                            <span class="mx-1 fw-semibold text-red-500 cursor-pointer hover:text-red-600">hapus</span>
                        </div>
                    </div>
                    <div class="flex justify-between my-1" v-for="ticket in item.ticket">
                        <div>
                            <span class="font-semibold">{{ ticket.name }}</span>
                        </div>
                        <div>
                            <span class="mx-1 fw-semibold text-blue-500 cursor-pointer hover:text-blue-600">edit</span>
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
                                <input type="text" class="form-control mb-2" v-model="form.name">
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
                                <textarea type="text" class="form-control mb-2" v-model="form.reward"></textarea>
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

export default {
    setup() {
        const route = useRoute()
        const { postData, getData, patchData } = useAxios()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()

        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
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

        let editAddModal = ref(null);
        onMounted(() => {
            const editAddModalElement = document.getElementById('editAddModal');
            if (editAddModalElement) {
                editAddModal.value = new bootstrap.Modal(editAddModalElement);
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

            form.id = ""
            form.event_id = ""
            form.event_name = ""
            form.name = ""
            form.price = ""
            form.reward = ""

            editAddModal.value.show();
        }

        function showEditModal(data) {
            form_props.edit_mode = true
            editAddModal.value.show();

            form.id = data.id
            form.event_id = data.event_id
            form.event_name = data.event_name
            form.name = data.name
            form.price = data.price
            form.reward = data.reward
        }

        return {
            showAddModal,
            showEditModal,
            form_props,
            form,
            getStatus
        }
    }
}
</script>
