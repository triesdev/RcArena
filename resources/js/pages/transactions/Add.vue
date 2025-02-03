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
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2 md:col-span-2">
                        <div class="card mb-4 card-flush">
                            <div class="card-body">
                                <div >
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
                                                        'Regular',
                                                        'Community'
                                                    ]"
                                                    >
                                                </vSelect>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <div class="form-group">
                                                <label>Pilih Tiket</label>
                                                <vSelect
                                                    v-model="selectedTicket"
                                                    :disabled="!form.event_id"
                                                    :options="ticketProperties"
                                                    @search="fetchTicketProperties"
                                                    :reduce = "ticket => ticket.id"
                                                    label="name"
                                                    @update:modelValue="putTicketIdToDetail"
                                                >
                                                </vSelect>
                                            </div>
                                            <span v-if="errorTicketExist" class="badge badge-danger">
                                                Tiket sudah ada !
                                            </span>
                                        </div>
                                    </div>
                                    <div id="detailOrders" class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <table class="w-100">
                                                <thead>
                                                <tr class="bg-gray-50">
                                                    <th class="text-gray-600">Kelas</th>
                                                    <th class="text-gray-600">Jenis Burung</th>
                                                    <th class="text-gray-600 text-right">Qty</th>
                                                    <th class="text-gray-600 text-right">Harga</th>
                                                    <th class="text-gray-600 text-right">Subtotal</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="p-2" v-if="form.details.length > 0" v-for="(detail, index) in form.details" :key="index">
                                                    <td class="fw-normal py-2">{{detail.class_name}}</td>
                                                    <td class="fw-normal py-2">{{detail.ticket_name}}</td>
                                                    <td class="w-20 text-right py-2 font-normal flex items-center space-x-1 no-spinner">
                                                        <button @click="decreaseQty(index)" class="px-2 py-1 text-white bg-gray-500 rounded-md hover:bg-gray-600">-</button>
                                                        <input @keyup="updateQty(index)" type="number" v-model="detail.qty" class="w-12 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="1" />
                                                        <button @click="increaseQty(index)" class="px-2 py-1 text-white bg-gray-500 rounded-md hover:bg-gray-600">+</button>
                                                    </td>
                                                    <td class="fw-normal py-2 text-right">{{ $filter.currency(detail.price) }}</td>
                                                    <td class="fw-normal py-2 text-right">{{ $filter.currency(detail.subtotal_price) }}</td>
                                                </tr>
                                                <tr class="p-2" v-else colspan="5">
                                                    <td class="text-center text-gray-700" colspan="5">Belum ada data</td>
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
                                            <table class="w-100">
                                                <tbody class="detail-table">
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Event</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.event_name}}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div id="detailTicket mt-4">
                                                <div class="mt-2" v-for="item in data_content.data_detail.transaction_details">
                                                    <span class="fw-bolder">{{item.name}}</span>
                                                    <div class="ml-4">
                                                        <table class="w-100">
                                                            <tbody>
                                                            <tr v-for="ticket in item.transaction_details">
                                                                <td width="40%" class="fw-normal">
                                                                    {{ticket.ticket_name}}
                                                                </td>
                                                                <td width="30%" class="fw-normal text-right">
                                                                    {{ticket.qty}}x Tiket
                                                                </td>
                                                                <td width="30%" class="fw-normal text-right">
                                                                    {{$filter.currency(ticket.subtotal_price)}}
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="my-2" />
                                            <div id="summaryTransaction" class="fw-bold">
                                                <div class="flex justify-between mb-2">
                                                    <span>Sub Total</span>
                                                    <span>
                                                        {{ $filter.currency(data_content.data_detail.subtotal_price) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between mb-2">
                                                    <span>Kode Unik</span>
                                                    <span>
                                                        {{ $filter.currency(data_content.data_detail.unique_code_price) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between mb-2">
                                                    <span>Diskon</span>
                                                    <span>
                                                        {{ $filter.currency(data_content.data_detail.discount_price) }}
                                                    </span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Total</span>
                                                    <span>
                                                        {{ $filter.currency(data_content.data_detail.total_price) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-1">
                        <div class="card card-flush">
                            <div v-if="data_content.data_detail.user" class="card-body">
                                <h3 class="fw-bold">Detail Pemesan</h3>
                                <div class="mt-4">
                                    <img :src="`${data_content.data_detail.user.image_uri ?? '/assets/default-profile.jpg'}`" alt="Profile Image" class="rounded w-full h-fit">
                                </div>
                                <div class="mt-4">
                                    <h4 class="fw-bold">Nama</h4>
                                    <p>{{data_content.data_detail.user.name}}</p>
                                    <h4 class="mt-2 fw-bold">No Whatsapp</h4>
                                    <p>{{data_content.data_detail.user.phone_number}}</p>
                                </div>
                            </div>
                        </div>
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
</style>
<script>
import {container as WidgetContainerModal} from "jenesius-vue-modal";
import Breadcrumb from "../../components/Breadcrumb.vue";
import {reactive, ref, onMounted} from "vue";
import useAxios from "../../src/service.js";
import { debounce } from 'lodash';

export default {
    setup(){
        const {getData} = useAxios();
        const title = "Detail Pemesanan";

        const form_props = reactive({
            is_loading: false,
            edit_mode: false,
        })

        const breadcrumb_list = [
            {name: "Home", link: "/panel"},
            {name: "Pemesanan", link: "/panel/transactions"},
            {name: "Detail Pemesanan", link: "/panel/transactions/detail"}
        ];

        const data_content = reactive({
            data_detail: {
                transaction_number: "TRX-0001",
                transaction_date: "2021-12-12",
                total_price: 100000,
                payment: {
                    payment_status: "new",
                    account_name: "John Doe",
                    payment_date: "2021-12-12",
                    payment_proof_image_uri: "https://via.placeholder.com/150",
                    note: "Pembayaran sudah dilakukan"
                },
                event_name: "Event Test",
                transaction_details: [
                    {
                        name: "Tiket VIP",
                        transaction_details: [
                            {
                                ticket_name: "Tiket VIP",
                                qty: 1,
                                subtotal_price: 100000
                            }
                        ]
                    }
                ],
                subtotal_price: 100000,
                unique_code_price: 0,
                discount_price: 0,
                user: {
                    image_uri: "https://via.placeholder.com/150",
                    name: "John Doe",
                    phone_number: "08123456789"
                }
            }
        });

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
        const fetchTicketProperties = debounce(async (ticket_name = '') => {
            if (!ticket_name){
                return;
            }
            form_props.is_loading = true;
            try {
                const params = {
                    ticket_name: ticket_name,
                    event_id: form.event_id
                };
                const response = await getData("ticket-properties", params);
                ticketProperties.value = response.result;
            } catch (error) {
                console.error("Failed to fetch ticket properties:", error);
            } finally {
                form_props.is_loading = false;
            }
        }, 500);

        onMounted(() => {
            getEvents();
        });

        /*Handle Form*/
        const form = reactive({
            event_id: null,
            ticket_type: null,
            details: []
        });

        const selectedTicket = ref(null);

        const errorTicketExist = ref(false);
        const putTicketIdToDetail = (val) => {
            // Check If Exist return error
            if (form.details.find(detail => detail.ticket_id === val)){
                errorTicketExist.value = true;
                return;
            }

            const ticket = ticketProperties.value.find(ticket => ticket.id === val);

            const detail = {
                ticket_id: ticket.id,
                class_name: ticket.class_name,
                ticket_name: ticket.name,
                qty: 1,
                price: ticket.price,
                subtotal_price: ticket.price
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
        };

        return {
            title,
            breadcrumb_list,
            form_props,
            data_content,
            form,
            eventProperties,
            ticketProperties,
            fetchTicketProperties,
            selectedTicket,
            putTicketIdToDetail,
            errorTicketExist,
            increaseQty,
            decreaseQty,
            updateQty,
        };
    },
}
</script>
