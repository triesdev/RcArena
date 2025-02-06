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
                    <router-link to="/panel/payment-methods/add" class="btn flex items-center btn-sm fw-bold btn-primary">
                       <v-icon name="bi-plus" class="mr-2"></v-icon> Tambah Data
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
                                <input type="text" v-model="payment_method_store.name" @keyup.enter="loadDataContent()"
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
                                            <th>Nama Akun</th>
                                            <th>Nomor Akun</th>
                                            <th>Tipe</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        <tr v-if="response.data_content.total === 0">
                                            <td colspan="7" class="text-center"><i>Tidak ada data.</i></td>
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
                                            <td>{{ data.account_name }}</td>
                                            <td>{{ data.account_number }}</td>
                                            <td>{{data.type.toUpperCase()}}</td>
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light dropdown-toggle btn-sm"
                                                        data-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <router-link :to="'/panel/payment-methods/' + data.id"
                                                            class="dropdown-item">
                                                            Edit
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
                                    <PerPage :value="payment_method_store.per_page" @change-per-page="changePerPage" />
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
        const title = "Data Event"
        const breadcrumb_list = ["Event", "Data"];
        const { getData, deleteData } = useAxios()
        const is_loading = ref(true)
        const { payment_method_store } = useFilterStore()

        function loadDataContent(page = 1) {
            is_loading.value = true
            payment_method_store.page = page

            getData('payment-methods', payment_method_store)
                .then((data) => {
                    if (data.success) {
                        response.data_content = data.result
                    }
                    is_loading.value = false
                })
        }

        loadDataContent(payment_method_store.page)

        const response = reactive({
            data_content: {
                data: []
            }
        })

        function changePerPage(per_page) {
            payment_method_store.per_page = per_page
            loadDataContent()
        }

        async function deleteModal(id) {
            const delete_modal = await promptModal(DeleteModal, { title: "Hapus data?" })
            if (delete_modal) {
                deleteData('payment-methods/' + id)
                    .then((data) => {
                        SwalToast('Berhasil menghapus data.')
                        loadDataContent(payment_method_store.page)
                    })
            }
        }

        return {
            breadcrumb_list,
            title,
            response,
            is_loading,
            payment_method_store,
            loadDataContent,
            changePerPage,
            deleteModal
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
