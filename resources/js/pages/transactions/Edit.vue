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
                <Loading :active="form_props.is_loading" :loader="'dots'" :is-full-page="false" />
                <div class="grid grid-cols-1 lg:grid-cols-[70%_30%] xl:grid-cols-[78%_22%] gap-4">
                    <div>
                        <div class="card mb-4 card-flush">
                            <div class="card-body">
                                <div>
                                    <h2 class="fw-bold">Detail Tiket</h2>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <div class="form-group">
                                                <label>Pilih Event</label>
                                                <vSelect
                                                    v-model="form.event_id"
                                                    :options="eventProperties"
                                                    :reduce="event => event.id"
                                                    :disabled="form.details.length > 0"
                                                    label="name">
                                                </vSelect>
                                                {{getMessage('event_id')}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <div class="form-group">
                                                <label>Jenis Tiket</label>
                                                <vSelect
                                                    v-model="form.ticket_type"
                                                    :options="[
                                                        { value: 'regular', text: 'Regular' },
                                                        { value: 'community', text: 'Community' }
                                                    ]"
                                                    :reduce="option => option.value"
                                                    label="text"
                                                    >
                                                </vSelect>
                                            </div>
                                            {{getMessage('ticket_type')}}
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <div class="form-group">
                                                <button @click="openTicketModal" :disabled="!form.event_id" class="btn btn-primary btn-sm flex items-center">
                                                    <v-icon name="bi-ticket-detailed" class="mr-2"></v-icon> Pilih Tiket
                                                </button>
                                            </div>
                                            {{getMessage('details')}}
                                        </div>
                                    </div>
                                    <div id="detailOrders" class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <table class="w-100">
                                                <thead>
                                                <tr class="bg-gray-50">
                                                    <th class="text-gray-600 text-center">Kelas</th>
                                                    <th class="text-gray-60 text-center">Varian</th>
                                                    <th width="10%" class="text-gray-600 text-center">Qty</th>
                                                    <th class="text-gray-600 text-center">Harga</th>
                                                    <th class="text-gray-600 text-center">Subtotal</th>
                                                    <th class="text-gray-600 text-center">Aksi</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <template v-if="form.details.length > 0" v-for="(detail, index) in form.details" :key="index">
                                                    <tr class="p-2">
                                                        <td v-if="detail.type == 'bundle'" colspan="2" class="fw-normal py-2">
                                                            {{detail.ticket_name}} <span class="badge badge-sm badge-info">{{detail.type.toUpperCase()}}</span><br>
                                                            <span class="fw-bolder text-xs badge badge-sm badge-danger" v-if="getStatus(`details.${detail.id}.${detail.type}`)">
                                                                Out Of Stock !
                                                            </span>
                                                        </td>
                                                        <td v-if="detail.type == 'piece'" class="fw-normal py-2">{{detail.class_name}}
                                                            <span class="fw-bolder text-xs badge badge-sm badge-danger" v-if="getStatus(`details.${detail.id}.${detail.type}`)">
                                                                Out Of Stock !
                                                            </span>
                                                        </td>
                                                        <td v-if="detail.type == 'piece'" class="fw-normal py-2">{{detail.ticket_name}}</td>
                                                        <td class="text-right py-2 font-normal flex items-center space-x-1 no-spinner">
                                                            <button @click="decreaseQty(index)" class="px-2 py-1 text-white bg-gray-500 rounded-md hover:bg-gray-600 h-[2rem] w-[2rem]">-</button>
                                                            <input @keyup="updateQty(index)" type="number" v-model="detail.qty" class="w-12 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1" />
                                                            <button @click="increaseQty(index)" class="px-2 py-1 text-white bg-gray-500 h-[2rem] w-[2rem] rounded-md hover:bg-gray-600">+</button>
                                                        </td>
                                                        <td class="fw-normal py-2 text-center">{{ $filter.currency(detail.price) }}</td>
                                                        <td class="fw-normal py-2 text-center">{{ $filter.currency(detail.subtotal_price) }}</td>
                                                        <td>
                                                            <button @click="deleteTicket(index)" class="btn btn-sm btn-danger flex items-center justify-center w-1 h-[2rem]">
                                                                <v-icon name="bi-trash"></v-icon>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr v-if="detail.tickets.length > 0" class="ticket-detail-tr" v-for="detail_ticket in detail.tickets" :key="`detail-ticket-${detail_ticket.id}`">
                                                        <td class="p-2">
                                                            {{detail_ticket.class_name}}
                                                        </td>
                                                        <td class="p-2">
                                                            {{detail_ticket.ticket_name}}
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr class="p-4" v-else colspan="5">
                                                    <td class="text-center text-gray-700 p-4" colspan="6">Belum ada data</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-flush">
                            <div class="card-body">
                                <div >
                                    <h3 class="fw-bold">Detail Pemesanan</h3>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <div id="summaryTransaction" class="fw-bold">
                                                <div class="flex justify-between mb-2">
                                                    <span>Sub Total</span>
                                                    <span class="mr-4">
                                                        {{form.total_qty}}x Tiket
                                                    </span>
                                                    <span>
                                                        {{ $filter.currency(form.subtotal_price) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between mb-2">
                                                    <span>Kode Unik</span>
                                                    <span>
                                                        {{ $filter.currency(form.unique_code) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between mb-2">
                                                    <span>Diskon</span>
                                                    <input @keyup="syncTotal" type="number" min="0" class="border-gray-400 rounded-sm w-[5rem] text-right h-[2rem]" v-model="form.discount">
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Total</span>
                                                    <span>
                                                        {{ $filter.currency(form.total_price) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-100 mt-2 flex justify-end">
                            <button @click="storeTransaction" class="btn btn-sm btn-primary fw-bold flex items-center p-2 h-[2.5rem]">
                                <v-icon name="bi-save" class="mr-2"></v-icon>Buat Pesanan
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="card card-flush">
                            <div class="card-body">
                                <h3 class="fw-bold">Detail Pemesan</h3>
                                <input v-model="user_code_orderer" class="form-control mt-4 h-[2.5rem]" placeholder="Masukkan ID Pemesan">
                                {{getMessage('user_id')}}
                                <button @click="checkUser" class="btn btn-sm btn-primary h-[2.5rem] mt-2 justify-center flex items-center p-0 w-100">
                                    <v-icon name="bi-search" class="mr-2"></v-icon> Cek ID Pemesan
                                </button>
                                <div v-if="user_check_data" class="mt-2" >
                                    <div class="mt-4">
                                        <img :src="`${user_check_data.image_uri ?? '/assets/default-profile.jpg'}`" alt="Profile Image" class="rounded w-full h-fit">
                                    </div>
                                    <div class="mt-4">
                                        <h4 class="fw-bold">Nama</h4>
                                        <p>{{user_check_data.name}}</p>
                                        <h4 class="mt-2 fw-bold">No Whatsapp</h4>
                                        <p>{{user_check_data.phone_number}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fw-bolder" id="ticketModalLabel">List Tiket :</h2>
                    </div>
                    <div class="modal-body">
                        <Loading :active="form_props.is_loading_modal_tickets" :loader="'dots'" :is-full-page="false" />
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <h2 class="fw-bold text-lg">
                                   <v-icon name="bi-exclamation-circle-fill" class="text-primary"></v-icon> Tiket Bundle
                                </h2>
                                <table class="w-100">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-2" width="60%">Nama</th>
                                        <th class="p-2">Harga</th>
                                        <th class="p-2">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <template :key="ticket.id" v-for="ticket in ticketBundleProperties">
                                            <tr>
                                                <td class="p-2">{{ticket.ticket_bundle_name}}</td>
                                                <td class="p-2">{{ $filter.currency(ticket.ticket_bundle_price) }}</td>
                                                <td class="p-2">
                                                    <button @click="putTicketIdToDetail(ticket.id, 'bundle')" class="btn btn-primary btn-sm flex items-center justify-center !w-[10px] !h-[10px]">
                                                        <v-icon name="bi-plus"></v-icon>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Ticket Detail -->
                                            <tr class="ticket-detail-tr" v-for="item in ticket.tickets" :key="`ticket-${item.id}`">
                                                <td class="p-1 !pl-5" colspan="2">{{item.ticket_name}}<br><span class="badge badge-sm badge-info">{{ item.class_name }}</span></td>
                                                <td>
                                                    &nbsp;
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-span-2">
                                <hr class="w-100">
                            </div>
                            <div class="col-span-2">
                                <h2 class="fw-bold text-lg">
                                    <v-icon name="bi-exclamation-circle-fill" class="text-primary"></v-icon> Tiket Satuan
                                </h2>
                                <table class="w-100">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-2" width="60%">Nama</th>
                                        <th class="p-2">Harga</th>
                                        <th class="p-2">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="ticket in ticketPieceProperties" :key="ticket.id">
                                        <td class="p-2">{{ticket.name}}</td>
                                        <td class="p-2">{{ $filter.currency(ticket.price) }}</td>
                                        <td class="p-2">
                                            <button @click="putTicketIdToDetail(ticket.id, 'piece')" class="btn btn-primary btn-sm flex items-center justify-center !w-[10px] !h-[10px]">
                                                <v-icon name="bi-plus"></v-icon>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="closeTicketModal" class="btn btn-secondary">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}

.ticket-detail-tr td, .ticket-detail-tr td span{
    font-size: 12px !important;
    font-weight: lighter;
}
</style>
<script>
import {reactive, ref, onMounted} from "vue";
import useAxios from "../../src/service.js";
import { debounce } from 'lodash';
import useValidation from "../../src/validation";

export default {
    setup(){
        const {getData, basePostData} = useAxios();
        const { setErrors, getMessage, resetErrors, getStatus, removeError } = useValidation()
        const title = "Buat Pesanan Baru";

        const form_props = reactive({
            is_loading: false,
            is_loading_modal_tickets: false,
            edit_mode: false,
        })

        const breadcrumb_list = [
            {name: "Home", link: "/panel"},
            {name: "Pemesanan", link: "/panel/transactions"},
            {name: "Detail Pemesanan", link: "/panel/transactions/detail"}
        ];

        // Get Events
        const eventProperties = ref([]);
        const getEvents = async () => {
            form_props.is_loading = true;
            try {
                const response = await getData("events-properties");
                eventProperties.value = response.result;
            } catch (error) {
                console.error("Failed to fetch events properties:", error);
            } finally {
                form_props.is_loading = false;
            }
        }

        const ticketProperties = ref([]);
        const ticketPieceProperties = ref([]);
        const ticketBundleProperties = ref([]);
        const fetchTicketProperties = debounce(async (ticket_name = '', first_load = false) => {
            if (!ticket_name && !first_load){
                return;
            }
            form_props.is_loading_modal_tickets = true;
            try {
                const params = {
                    ticket_name: ticket_name,
                    event_id: form.event_id
                };
                const response = await getData("ticket-properties", params);
                ticketProperties.value = response.result;
                ticketPieceProperties.value = response.result.ticket_pieces;
                ticketBundleProperties.value = response.result.ticket_bundles;
            } catch (error) {
                console.error("Failed to fetch ticket properties:", error);
            } finally {
                form_props.is_loading_modal_tickets = false;
            }
        }, 500);

        let ticketModal = ref(null);
        onMounted(() => {
            getEvents();
            const modalElement = document.getElementById('ticketModal');
            if (modalElement) {
                ticketModal.value = new bootstrap.Modal(modalElement);
            }
        });

        /*Handle Form*/
        const form = reactive({
            event_id: null,
            user_id: null,
            ticket_type: null,
            total_price: 0,
            discount: 0,
            subtotal_price: 0,
            unique_code: Math.floor(Math.random() * (500 - 100 + 1)) + 100,
            total_qty: 0,
            details: [],
        });

        const selectedTicket = ref(null);

        const errorTicketExist = ref(false);
        const putTicketIdToDetail = (val, type) => {
            // Check If Exist with ID and TYPE return error
            if (form.details.length > 0) {
                if (form.details.find(detail => detail.id === val && detail.type === type)) {
                    errorTicketExist.value = true;
                    return;
                }
            }

            if (type === 'bundle'){
                handleBundleTicket(val);
            } else {
                handlePieceTicket(val);
            }

            syncTotal();
        }

        const handleBundleTicket = (val) => {
            const item = ticketBundleProperties.value.find(ticket => ticket.id === val);
            const detail = {
                id: item.id,
                class_name: "",
                ticket_name: item.ticket_bundle_name,
                qty: 1,
                price: item.ticket_bundle_price,
                subtotal_price: item.ticket_bundle_price,
                type: 'bundle',
                tickets: item.tickets.map((ticket) => {
                    return {
                        id: ticket.id,
                        class_name: ticket.class_name,
                        ticket_name: ticket.ticket_name,
                        type: "piece"
                    }
                })
            };
            form.details.push(detail);
        }

        const handlePieceTicket = (val) => {
            const ticket = ticketPieceProperties.value.find(ticket => ticket.id === val);
            const detail = {
                id: ticket.id,
                class_name: ticket.class_name,
                ticket_name: ticket.name,
                qty: 1,
                price: ticket.price,
                subtotal_price: ticket.price,
                type: 'piece',
                tickets: []
            };
            form.details.push(detail);
        }

        const increaseQty = (index) => {
            form.details[index].qty++;
            updateSubtotal(index);
        };

        const decreaseQty = (index) => {
            if (form.details[index].qty > 1) {
                form.details[index].qty--;
                updateSubtotal(index);
            }
        };

        const updateQty = (index) => {
            const detail = form.details[index];
            if (detail.qty < 1) {
                detail.qty = 1;
            }
            updateSubtotal(index);
        };

        const updateSubtotal = (index) => {
            const detail = form.details[index];
            detail.subtotal_price = detail.qty * detail.price;
            syncTotal();
        };

        const openTicketModal = () => {
            ticketModal.value.show();
            fetchTicketProperties('', true);
        }

        const closeTicketModal = () => {
            ticketModal.value.hide();
        }

        const deleteTicket = (index) => {
            const data_detail = form.details[index];
            removeError(`details.${data_detail.id}.${data_detail.type}`);
            form.details.splice(index, 1);
            syncTotal()
        }

        const syncTotal = () => {
            let subtotal = 0;
            let total_qty = 0;
            form.details.forEach((detail) => {
                subtotal += detail.subtotal_price;
                total_qty += detail.qty;
            });

            form.total_qty  = total_qty;
            form.subtotal_price = subtotal;
            form.total_price = form.subtotal_price + form.unique_code - form.discount;
        }

        const user_check_data = ref(null)
        const user_code_orderer = ref(null)
        const checkUser = async () => {
            form_props.is_loading = true;
            try {
                const response = await getData("users/get-by-code/" + user_code_orderer.value);
                if (response.result) {
                    user_check_data.value = response.result;
                    form.user_id = response.result.id;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'User Not Found',
                        text: 'No user found with the provided ID.',
                    });
                    form_props.is_loading = false;
                }
            } catch (error) {
                console.error("Failed to fetch user data:", error);
                form_props.is_loading = false;
            } finally {
                form_props.is_loading = false;
            }
        }

        const storeTransaction = async () => {
            resetErrors();
            form_props.is_loading = true;
            try {
                const response = await basePostData("transactions", form);
                if (response.status === 201) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Transaction has been created successfully.',
                    }).then(() => {
                        window.location.href = "/panel/transactions";
                    });
                } else {
                    setErrors(response.errors);
                }
            } catch (error) {
                let formattedErrors = {};

                if (error.response && error.response.data) {
                    if (error.response.data.message === 'error_stocks') {
                        const errors = error.response.data.errors;

                        errors.forEach((error) => {
                            const detailIndex = form.details.findIndex(
                                (detail) => detail.id === error.id_ticket && detail.type === error.type
                            );

                            if (detailIndex !== -1) {
                                if (!formattedErrors[`details.${form.details[detailIndex].id}.${form.details[detailIndex].type}`]) {
                                    formattedErrors[`details.${form.details[detailIndex].id}.${form.details[detailIndex].type}`] = [];
                                }

                                formattedErrors[`details.${form.details[detailIndex].id}.${form.details[detailIndex].type}`].push(error.message);
                            }
                        });
                    } else {
                        formattedErrors = error.response.data.errors;
                    }
                } else {
                    formattedErrors.general = ["An unexpected error occurred."];
                }

                setErrors(formattedErrors);
            } finally {
                form_props.is_loading = false;
            }
        };

        return {
            title,
            breadcrumb_list,
            form_props,
            form,
            eventProperties,
            ticketProperties,
            ticketPieceProperties,
            ticketBundleProperties,
            fetchTicketProperties,
            selectedTicket,
            putTicketIdToDetail,
            errorTicketExist,
            increaseQty,
            decreaseQty,
            updateQty,
            deleteTicket,
            openTicketModal,
            closeTicketModal,
            syncTotal,
            user_check_data,
            user_code_orderer,
            checkUser,
            storeTransaction,
            getMessage,
            getStatus
        };
    },
}
</script>
