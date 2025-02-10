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
                                        <th>Nama</th>
                                        <th>Jumlah Variasi Tiket</th>
                                        <th>Jumlah Peserta</th>
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
                                        <td>{{ data.name }}</td>
                                        <td>{{data.total_tickets}}</td>
                                        <td>{{ data.total_participants }}</td>
                                        <td class="text-end flex justify-end">
                                            <button @click="getTicketByClassId(data.id)" class="btn flex items-center btn-sm btn-primary">
                                                <v-icon name="bi-eye" class="mr-2" /> Varian
                                            </button>
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
        </div>
        <!-- Modal -->
        <div class="modal fade" id="modalListVariant" tabindex="-1" role="dialog" aria-labelledby="modalListVariantLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fw-bolder" id="modalListVariantLabel">List Variant</h2>
                    </div>
                    <div class="modal-body">
                        <Loading :active="form_props.is_loading_modal_tickets" :loader="'dots'" :is-full-page="false" />
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(ticket, t) in form_props.tickets">
                                            <td>{{t + 1}}</td>
                                            <td>{{ ticket.name }}</td>
                                            <td>{{ $filter.currency(ticket.price) }}</td>
                                            <td>{{ ticket.total_participants }}
                                            </td>
                                            <td>
                                                <router-link :to="`/panel/`" class="btn btn-primary btn-sm" @click="editModal(ticket.id)">
                                                    <v-icon name="bi-eye" />
                                                </router-link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="closemodalListVariant" class="btn btn-secondary">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import PerPage from '../../components/PerPage'
import StatusDefault from '../../components/StatusDefault'
import useAxios from "../../src/service";
import {onMounted, reactive, ref} from "vue";
import { container, promptModal } from "jenesius-vue-modal";
import SwalToast from '../../src/swal_toast'
import { useFilterStore } from "../../src/store_filter";
import {useRoute} from "vue-router";

export default {
    components: { Breadcrumb, PerPage, WidgetContainerModal: container, StatusDefault },
    setup() {
        const title = "Kelas Event"
        const breadcrumb_list = ["Event", "Kelas"];
        const { getData, deleteData } = useAxios()
        const is_loading = ref(true)
        const { event_store } = useFilterStore()
        const route = useRoute()

        // Const
        const eventId = ref(null)
        eventId.value = route.params.id;

        function loadDataContent(page = 1) {
            is_loading.value = true
            event_store.page = page

            getData('events-class/' + eventId.value, event_store)
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

        //Initiate Modal
        const form_props = reactive({
            is_loading_modal_tickets: false,
            tickets: []
        })

        const modalListVariant = ref(null)
        onMounted(() => {
            // Initiate Bootstrap Modal
            modalListVariant.value = new bootstrap.Modal(document.getElementById('modalListVariant'), {
                keyboard: false,
                backdrop: 'static'
            })
        })

        const getTicketByClassId = (id) => {
            showModalListVarian()
            form_props.is_loading_modal_tickets = true
            getData('events-class-variants/' + id)
                .then((data) => {
                    if (data.success) {
                        form_props.tickets = data.result
                    }
                    form_props.is_loading_modal_tickets = false
                })
        }

        const showModalListVarian = () => {
            $('#modalListVariant').modal('show')
        }

        const closemodalListVariant = () => {
            $('#modalListVariant').modal('hide')
        }

        return {
            breadcrumb_list,
            title,
            response,
            is_loading,
            event_store,
            form_props,
            loadDataContent,
            changePerPage,
            deleteModal,
            showModalListVarian,
            closemodalListVariant,
            getTicketByClassId
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
