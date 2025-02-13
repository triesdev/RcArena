<template>
    <div class="d-flex flex-column flex-column-fluid" style="min-height: calc(100vh - 130px)">
        <div class="app-toolbar py-3 py-lg-6">
            <div class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ title }}
                    </h1>
                    <Breadcrumb :list="breadcrumb_list"></Breadcrumb>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <router-link to="/panel/events/add" class="btn btn-sm fw-bold btn-primary">
                        Tambah Data
                    </router-link>
                </div>
            </div>
        </div>
        <div class="app-content flex-column-fluid">
            <div class="app-container container-xxl">
                <div class="card card-flush">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5"
                        data-select2-id="select2-data-124-lq0k">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                    <v-icon name="bi-search" />
                                </span>
                                <input type="text" v-model="event_store.name" @keyup.enter="loadDataContent"
                                    class="form-control form-control-solid w-250px ps-14" placeholder="Cari..">
                            </div>
                        </div>
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5"
                            data-select2-id="select2-data-123-4p2n">
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <Loading :active="is_loading" :loader="'dots'" :is-full-page="false" />
                                <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th>No</th>
                                            <th style="width: 90px;"></th>
                                            <th>Nama</th>
                                            <th>Tanggal</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        <tr v-if="response.data_content.total === 0">
                                            <td colspan="5" class="text-center"><i>Tidak ada data.</i></td>
                                        </tr>
                                        <tr v-for="(data, d) in response.data_content.data">
                                            <td>
                                                {{ response.data_content.per_page *
                                                    (response.data_content.current_page - 1) + d + 1 }}
                                            </td>
                                            <td>
                                                <div :style="'background-image: url(' + data.image_uri + ')'"
                                                    class="img-thumbnail"></div>
                                            </td>
                                            <td>{{ data.name }}</td>
                                            <td>{{ $filter.formatDate(data.event_date) }}</td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light dropdown-toggle btn-sm"
                                                        data-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <button @click="showModalToDaftarPendaftar(data)" class="dropdown-item">
                                                            Data Pendaftar
                                                        </button>
                                                        <router-link :to="'/panel/events/' + data.id"
                                                            class="dropdown-item">
                                                            Edit
                                                        </router-link>
                                                        <router-link :to="'/panel/events-detail/' + data.id"
                                                            class="dropdown-item">
                                                            Detail
                                                        </router-link>
                                                        <button class="dropdown-item text-danger"
                                                            @click="deleteModal(data.id)">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div
                                    class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                    <PerPage :value="event_store.per_page" @change-per-page="changePerPage" />
                                </div>
                                <div
                                    class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                    <Bootstrap4Pagination :data="response.data_content" :limit="2"
                                        @pagination-change-page="loadDataContent"></Bootstrap4Pagination>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <WidgetContainerModal />
        </div>
        <!-- Modal Daftar Pendaftar -->
        <div class="modal fade" id="modalDaftarPendaftar" tabindex="-1" aria-labelledby="modalDaftarPendaftarLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bolder" id="modalDaftarPendaftarLabel">Data Pendaftar</h5>
                    </div>
                    <div class="modal-body">
                        <div class="grid-cols-2">
                            <div class="col-span-2">
                                Buka Data Pendaftar Untuk Event:
                                <br>
                                <h4 v-if="modal_daftar_pendaftar_event" class="fw-bolder mt-2">{{modal_daftar_pendaftar_event.name}}</h4>
                            </div>
                            <div class="col-span-2 mt-4">
                                <h6 class="fw-bold">Kelas</h6>
                                <vSelect
                                    v-model="modal_daftar_form.class_id"
                                    :options="modal_daftar_classes"
                                    :reduce="item => item.id"
                                    label="name"
                                >
                                </vSelect>
                                <div v-if="modal_daftar_form_errors.class_id" class="text-danger mt-1">
                                    {{ modal_daftar_form_errors.class_id }}
                                </div>
                            </div>
                            <div class="col-span-2 mt-4">
                                <h6 class="fw-bold">Varian</h6>
                                <vSelect
                                    v-model="modal_daftar_form.variant_id"
                                    :options="modal_daftar_variants"
                                    :reduce="item => item.id"
                                    label="name"
                                >
                                </vSelect>
                                <div v-if="modal_daftar_form_errors.variant_id" class="text-danger mt-1">
                                    {{ modal_daftar_form_errors.variant_id }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeModalDaftarPendaftar()" type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button @click="redirectToDetailParticipants(modal_daftar_form.class_id)" type="button"
                            class="btn btn-primary">Lihat Data</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Daftar Pendaftar -->
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import PerPage from '../../components/PerPage'
import StatusDefault from '../../components/StatusDefault'
import useAxios from "../../src/service";
import DeleteModal from "./DeleteModal"
import {onMounted, reactive, ref, watch} from "vue";
import {closeModal, container, promptModal} from "jenesius-vue-modal";
import SwalToast from '../../src/swal_toast'
import { useFilterStore } from "../../src/store_filter";
import {useRouter} from "vue-router";

export default {
    methods: {closeModal},
    components: { Breadcrumb, PerPage, WidgetContainerModal: container, StatusDefault },
    setup() {
        const title = "Data Event"
        const breadcrumb_list = ["Event", "Data"];
        const { getData, deleteData } = useAxios()
        const is_loading = ref(true)
        const { event_store } = useFilterStore()
        const router = useRouter();

        function loadDataContent(page = 1) {
            is_loading.value = true
            event_store.page = page

            getData('events/', event_store)
                .then((data) => {
                    if (data.success) {
                        response.data_content = data.result
                    }
                    is_loading.value = false
                })
        }

        loadDataContent(event_store.page)

        const response = reactive({
            data_content: {
                data: []
            }
        })

        function changePerPage(per_page) {
            event_store.per_page = per_page
            loadDataContent()
        }

        async function deleteModal(id) {
            const delete_modal = await promptModal(DeleteModal, { title: "Hapus data?" })
            if (delete_modal) {
                deleteData('events/' + id)
                    .then((data) => {
                        SwalToast('Berhasil menghapus data.')
                        loadDataContent(event_store.page)
                    })
            }
        }


        const modalDaftarPendaftar = ref(null)
        onMounted(() => {
            // Initiate Bootstrap Modal
            modalDaftarPendaftar.value = new bootstrap.Modal(document.getElementById('modalDaftarPendaftar'), {
                keyboard: false,
                backdrop: 'static'
            })
        })


        const modal_daftar_pendaftar_event = ref(null);
        const modal_daftar_classes = ref([]);
        const modal_daftar_variants = ref([]);
        const modal_daftar_form = ref({
            event_id: null,
            class_id: null,
            variant_id: null
        })
        const modal_daftar_form_errors = ref({
            class_id: null,
            variant_id: null
        })
        function showModalToDaftarPendaftar(data) {
            // Refresh errors
            modal_daftar_form_errors.value = {
                class_id: null,
                variant_id: null
            }
            modalDaftarPendaftar.value.show()
            modal_daftar_pendaftar_event.value = data
            modal_daftar_form.value.event_id = data.id
            getDataModalClasses(data.id)
        }

        const getDataModalClasses = (event_id) => {
            // Reset Variant ID FORM
            modal_daftar_form.value.class_id = null

            getData('events-class/' + event_id, {
                per_page: 100,
            })
                .then((data) => {
                    if (data.success) {
                        modal_daftar_classes.value = data.result.data
                    }
                })
        }

        const getDataModalVariants = (class_id) => {
            getData('events-class-variants/' + class_id,{
                per_page: 100
            })
                .then((data) => {
                    if (data.success) {
                        modal_daftar_variants.value = data.result
                    }
                })
        }

        watch(() => modal_daftar_form.value.class_id, (newClassId, oldClassId) => {
            if (newClassId != oldClassId){
                getDataModalVariants(newClassId);
            }
        }, {deep: true});

        const redirectToDetailParticipants = (class_id) => {

            // Check Form Validation
            if (!modal_daftar_form.value.class_id) {
                modal_daftar_form_errors.value.class_id = 'Kelas harus diisi.'
                return
            }

            if (!modal_daftar_form.value.variant_id) {
                modal_daftar_form_errors.value.variant_id = 'Varian harus diisi.'
                return
            }

           router.push(`/panel/event-variant-participants/${class_id}`)
        }

        const closeModalDaftarPendaftar = () => {
            modalDaftarPendaftar.value.hide()
        }

        // Close After Redirect
        router.afterEach(() => {
            closeModalDaftarPendaftar()
        })

        return {
            breadcrumb_list,
            title,
            response,
            is_loading,
            event_store,
            modal_daftar_pendaftar_event,
            modal_daftar_variants,
            modal_daftar_classes,
            modal_daftar_form,
            modal_daftar_form_errors,
            loadDataContent,
            changePerPage,
            deleteModal,
            showModalToDaftarPendaftar,
            getDataModalVariants,
            redirectToDetailParticipants,
            closeModalDaftarPendaftar
        }
    }
}
</script>
<style>
.img-thumbnail {
    width: 70px;
    height: 70px;
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 5px;
    background-color: aliceblue;
}
</style>
