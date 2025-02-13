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
                <div class="card card-flush fw-bold gap-4 mb-4 py-4 px-8">
                   <div class="flex gap-1">
                       <span class="w-25">
                           Nama Event
                       </span>
                       <span class="w-[1rem]">:</span>
                       <span>
                           {{ticket_data.event_name}}
                       </span>
                   </div>
                    <div class="flex gap-1">
                       <span class="w-25">
                           Kelas
                       </span>
                        <span class="w-[1rem]">:</span>
                        <span>
                           {{ticket_data.class_name}}
                       </span>
                    </div>
                    <div class="flex gap-1">
                       <span class="w-25">
                           Variant
                       </span>
                        <span class="w-[1rem]">:</span>
                        <span>
                           {{ticket_data.name}}
                       </span>
                    </div>
                    <div class="gap-1 mt-4">
                        <div class="mb-2">
                           <span class="w-25 flex items-center">
                              <v-icon name="bi-exclamation-circle-fill" class="mr-2 text-info"></v-icon> Kursi Tersedia
                           </span>
                        </div>
                        <div>
                            <span v-for="item in available_seat" class="badge badge-sm badge-success mr-2">
                                {{item}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card card-flush">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5"
                         data-select2-id="select2-data-124-lq0k">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                    <v-icon name="bi-search" />
                                </span>
                                <input type="text" v-model="tickets_participant_store.name" @keyup.enter="loadDataContent"
                                       class="form-control form-control-solid w-250px ps-14" placeholder="ID Tiket/Nama Pendaftar">
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
                                            <th>ID Tiket</th>
                                            <th>Peserta</th>
                                            <th>Nama</th>
                                            <th class="w-[10rem]">No Kursi</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        <tr v-if="response.data_content.total === 0">
                                            <td colspan="5" class="text-center"><i>Tidak ada data.</i></td>
                                        </tr>
                                        <tr v-for="(data, d) in response.data_content.data">
                                            <td>
                                                {{ response.data_content.per_page * (response.data_content.current_page - 1) + d + 1 }}
                                            </td>
                                            <td>
                                                {{ data.ticket_number }}
                                            </td>
                                            <td>
                                                {{
                                                   data.user_name
                                                }}
                                            </td>
                                            <td>
                                               <input class="form-control" v-model="data.participant_name"
                                                placeholder="Nama Peserta"
                                                :disabled="data.is_locked"
                                               >
                                            </td>
                                            <td>
                                                <vSelect
                                                    class="w-100 custom-v-select-participants"
                                                    append-to-body
                                                    v-model="data.participant_chair_number"
                                                    :options="available_seat"
                                                    :disabled="data.is_locked"
                                                >
                                                </vSelect>
                                            </td>
                                            <td class="flex justify-end">
                                                <button v-if="!data.is_locked" @click="updateParticipant(data, d)" class="btn btn-sm btn-primary">
                                                    <v-icon name="bi-save"></v-icon>
                                                </button>
                                                <button v-else class="btn btn-sm btn-warning"
                                                @click="openModalEditParticipants(data)"
                                                >
                                                    <v-icon name="bi-pen"></v-icon>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div
                                    class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                                    <PerPage :value="tickets_participant_store.per_page" @change-per-page="changePerPage" />
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
        <!-- Modal Edit Participant -->
        <div id="modalEditParticipant" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content static">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bolder text-[1.2rem]">Edit Peserta</h5>
                    </div>
                    <form @submit.prevent="updateParticipantWithPassword">
                        <div class="modal-body static">
                            <div class="w-100">
                                <label class="mb-1">Nama Peserta</label>
                                <input v-model="edit_participant_form.participant_name" class="form-control" placeholder="Nama Peserta">
                                {{getMessage('participant_name')}}
                            </div>
                            <div class="w-100 mt-3 static">
                                <label class="mb-1">No Kursi</label>
                                <v-select
                                    class="w-100 custom-v-select-participants custom-v-select-static"
                                    v-model="edit_participant_form.participant_chair_number"
                                    :options="available_seat"
                                >
                                </v-select>
                                {{getMessage('participant_chair_number')}}
                            </div>
                            <hr class="my-5" />
                            <div class="w-100">
                                <label class="mb-1">Password</label>
                                <input v-model="edit_participant_form.password" type="password" class="form-control" placeholder="Password">
                                {{getMessage('password')}}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="closeModalEditParticipant" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button :disabled="is_loading_edit_participants" type="submit" class="btn btn-warning flex items-center">
                                <v-icon name="bi-pen" class="mr-2"></v-icon> Simpan
                            </button>
                        </div>
                    </form>
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
import { reactive, ref, onMounted, onBeforeUnmount } from "vue";
import { container, promptModal } from "jenesius-vue-modal";
import SwalToast from '../../src/swal_toast'
import { useFilterStore } from "../../src/store_filter";
import {useRoute} from "vue-router";
import useValidation from "../../src/validation";

export default {
    components: { Breadcrumb, PerPage, WidgetContainerModal: container, StatusDefault },
    setup() {
        const title = "Detail Data Pendaftar"
        const breadcrumb_list = ["Event","Variants", "Participants"];
        const { getData, basePatchData } = useAxios()
        const { setErrors, getMessage, resetErrors, getStatus, removeError } = useValidation()
        const is_loading = ref(true)
        const { tickets_participant_store } = useFilterStore()
        const route = useRoute()
        const ticket_id = route.params.id

        const ticket_data = ref({})
        const available_seat = ref([]);

        const fillAvaliableSeat = (quota) => {
            return Array.from({length: quota}, (v, k) => k + 1)
        }

        const filterAvailableSeatBaseOnDataTicket = (participants_data) => {
            return available_seat.value.filter((seat) => {
                return !participants_data.some((participant) => participant.participant_chair_number === seat)
            })
        }

        function getTicketById(){
            getData('tickets/' + ticket_id)
                .then((data) => {
                    if (data.success) {
                        ticket_data.value = data.result
                        available_seat.value = fillAvaliableSeat(ticket_data.value.quota)

                        loadDataContent(tickets_participant_store.page)
                    }
                })
        }

        getTicketById();

        function loadDataContent(page = 1) {
            is_loading.value = true
            tickets_participant_store.page = page

            getData('tickets/participants/' + ticket_id, tickets_participant_store)
                .then((data) => {
                    if (data.success) {
                        response.data_content = data.result
                        // Filter Available Seat
                        available_seat.value = filterAvailableSeatBaseOnDataTicket(data.result.data)
                    }
                    is_loading.value = false
                })
        }

        const response = reactive({
            data_content: {
                data: []
            }
        })

        function changePerPage(per_page) {
            tickets_participant_store.per_page = per_page
            loadDataContent()
        }

        async function deleteModal(id) {
            // const delete_modal = await promptModal(DeleteModal, { title: "Hapus data?" })
            // if (delete_modal) {
            //     deleteData('events/' + id)
            //         .then((data) => {
            //             SwalToast('Berhasil menghapus data.')
            //             loadDataContent(tickets_participant_store.page)
            //         })
            // }
        }

        const updateParticipant = (data, index) => {
            const transaction_detail_user_id = data.id

            is_loading.value = true
            basePatchData('transaction-detail-users/' + transaction_detail_user_id, {
                participant_name: data.participant_name,
                participant_chair_number: data.participant_chair_number,
                password: '',
                edit: false,
            }).then(() => {
                // Change is locked base on index from response data content
                response.data_content.data[index].is_locked = true

                // Filter Available Seat
                available_seat.value = filterAvailableSeatBaseOnDataTicket(response.data_content.data)

                SwalToast('Berhasil mengubah data.')
                is_loading.value = false
            }).catch((error) => {
                console.log(error);
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal Rubah Data',
                    text: 'Silahkan cek kembali data anda !',
                    // text: error,
                });
                is_loading.value = false
            })
        }

        const modalEditParticipant = ref(null)
        const openModalEditParticipants = (data) => {
            modalEditParticipant.value.show()
            edit_participant_form.value.id = data.id;
            edit_participant_form.value.participant_name = data.participant_name;
            edit_participant_form.value.participant_chair_number = data.participant_chair_number;
            edit_participant_form.value.password = '';

            // Add existing chair number to option participant_chair_number list and sort asc
            available_seat.value.push(data.participant_chair_number);
            available_seat.value.sort((a, b) => a - b);

        }

        onMounted(() => {
            modalEditParticipant.value = new bootstrap.Modal(document.getElementById('modalEditParticipant'), {
                keyboard: false,
                backdrop: 'static'
            })
        })

        const edit_participant_form = ref({
            id: '',
            participant_name: '',
            participant_chair_number: '',
            password: '',
        })

        const is_loading_edit_participants = ref(false)

        const updateParticipantWithPassword = () => {
            is_loading_edit_participants.value = true
            basePatchData('transaction-detail-users/' + edit_participant_form.value.id, {
                participant_name: edit_participant_form.value.participant_name,
                participant_chair_number: edit_participant_form.value.participant_chair_number,
                password: edit_participant_form.value.password,
                edit: true,
            }).then(() => {
                // Filter Available Seat
                available_seat.value = filterAvailableSeatBaseOnDataTicket(response.data_content.data)

                SwalToast('Berhasil mengubah data.')
                modalEditParticipant.value.hide()

                loadDataContent()
            }).catch((error) => {
                // IF STATUS 401
                if (error.response.status === 401) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password Salah',
                        text: 'Silahkan cek kembali data anda !',
                        // text: error,
                    });
                }

                // IF STATUS 400 (Validation Field)
                if (error.response.status === 400) {
                    setErrors(error.response.data.errors)
                }

            }).finally(() => {
                is_loading_edit_participants.value = false
            })
        }

        const closeModalEditParticipant = () => {
            modalEditParticipant.value.hide()
            resetErrors()

            available_seat.value = filterAvailableSeatBaseOnDataTicket(response.data_content.data)
        }

        return {
            breadcrumb_list,
            title,
            response,
            is_loading,
            tickets_participant_store,
            ticket_data,
            available_seat,
            edit_participant_form,
            is_loading_edit_participants,
            loadDataContent,
            changePerPage,
            deleteModal,
            updateParticipant,
            openModalEditParticipants,
            closeModalEditParticipant,
            updateParticipantWithPassword,

            //Element
            modalEditParticipant,

            // Validation
            getMessage,
            setErrors,
            resetErrors,
        }
    }
}
</script>
<style>

.custom-v-select-participants .vs__dropdown-toggle,
.custom-v-select-participants .vs__dropdown-menu {
    color: var(--kt-input-color);
    background-color: var(--kt-input-bg);
    box-shadow: none !important;
    border: 1px solid var(--kt-input-border-color);
    padding: .660rem .200rem;
    font-size: 1.1rem;
    font-weight: 500;
    line-height: 1.5;
    border-radius: .475rem;
}
.custom-v-select-static .vs__dropdown-toggle,
.custom-v-select-static.v-select {
    position: static !important;
}

.custom-v-select-static .vs__dropdown-menu{
    top: auto !important;
    width: 90% !important;
    left: auto !important;
}
</style>
