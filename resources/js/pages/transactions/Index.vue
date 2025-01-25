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
                    <div class="py-6 px-8">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="Cari.."
                                    @keyup.enter="loadDataContent()" v-model="transaction_store.user_name">
                            </div>
                            <div class="col-md-3">
                                <VueCtkDateTimePicker v-model="transaction_store.dates" v-bind="date_config">
                                </VueCtkDateTimePicker>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" @change="loadDataContent()"
                                    v-model="transaction_store.status">
                                    <option value="">Semua</option>
                                    <option value="100">Select Event</option>
                                    <option value="110">Waiting Payment</option>
                                    <option value="120">Waiting Confirmation</option>
                                    <option value="200">Paid</option>
                                    <option value="400">Deleted</option>
                                </select>
                            </div>
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
                                            <th>Transaction</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        <tr v-if="response.data_content.total === 0">
                                            <td colspan="5" class="text-center"><i>Tidak ada data.</i></td>
                                        </tr>
                                        <tr v-for="(data, d) in response.data_content.data">
                                            <td>
                                                {{ response.data_content.per_page * (response.data_content.current_page
                                                    - 1) + d + 1 }}
                                            </td>
                                            <td>
                                                {{ data.number }}
                                                <div class="text-sm">{{ $filter.formatDateTime(data.created_at) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="border-b pb-1 mb-1"
                                                    v-if="data.user && data.user.email !== data.user_email">
                                                    <span>{{ data.user.job_type_code }}</span> <b>{{ data.user.name
                                                        }}</b>
                                                    <br>
                                                    <span>{{ data.user.email }}</span>
                                                    <br>
                                                    <a class="text-blue-800 hover:text-blue-700"
                                                        :href="'https://wa.me/' + data.user.phone">{{ data.user.phone
                                                        }}</a>
                                                </div>
                                                <div>
                                                    {{ data.job_type_code }} <b>{{ data.user_name }}</b>
                                                    <br>
                                                    <span>{{ data.user_email }}</span>
                                                    <br>
                                                    <a class="text-blue-800 hover:text-blue-700"
                                                        :href="'https://wa.me/' + data.user_phone">{{ data.user_phone
                                                        }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                {{ data.status_label }}
                                                <div v-if="data.total" class="text-sm">Rp {{
                                                    $filter.currency(data.total) }}</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light dropdown-toggle btn-sm"
                                                        data-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <router-link :to="'/panel/transactions/' + data.id"
                                                            class="dropdown-item">
                                                            Process
                                                        </router-link>
                                                        <router-link :to="'/panel/transactions-validate/' + data.id"
                                                            class="dropdown-item">
                                                            Validate
                                                        </router-link>
                                                        <button class="dropdown-item text-danger"
                                                            @click="notifWa(data.id)">
                                                            Message
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
                                    <PerPage :value="transaction_store.per_page" @change-per-page="changePerPage" />
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
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import PerPage from '../../components/PerPage'
import StatusDefault from '../../components/StatusDefault'
import useAxios from "../../src/service";
import DeleteModal from "./DeleteModal"
import { reactive, ref } from "vue";
import { container, promptModal } from "jenesius-vue-modal";
import SwalToast from '../../src/swal_toast'
import { useFilterStore } from "../../src/store_filter";

export default {
    components: { Breadcrumb, PerPage, WidgetContainerModal: container, StatusDefault },
    setup() {
        const title = "Data Transaction"
        const breadcrumb_list = ["Transaction", "Data"];
        const { getData, deleteData } = useAxios()
        const is_loading = ref(true)
        const { transaction_store, date_config } = useFilterStore()

        function notifWa(trx_id) {
            if (confirm("Send Notif?")) {
                getData('transaction-notify/' + trx_id)
            }
        }

        function loadDataContent(page = 1) {
            is_loading.value = true
            transaction_store.page = page

            getData('transactions', transaction_store).then((data) => {
                response.data_content = data
                is_loading.value = false
            }).catch(() => {
                is_loading.value = false
            })
        }

        loadDataContent(transaction_store.page)

        const response = reactive({
            data_content: {
                data: []
            }
        })

        function changePerPage(per_page) {
            transaction_store.per_page = per_page
            loadDataContent()
        }

        async function deleteModal(id) {
            const delete_modal = await promptModal(DeleteModal, { title: "Hapus data?" })
            if (delete_modal) {
                deleteData('transactions/' + id)
                    .then((data) => {
                        SwalToast('Berhasil menghapus data.')
                        loadDataContent(transaction_store.page)
                    })
            }
        }

        return {
            breadcrumb_list,
            title,
            response,
            is_loading,
            transaction_store,
            loadDataContent,
            changePerPage,
            deleteModal,
            date_config,
            notifWa
        }
    }
}
</script>
<style>
input.field-input {
    height: 42px !important;
}
</style>
