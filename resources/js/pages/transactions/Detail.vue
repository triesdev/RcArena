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
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2 md:col-span-2">
                        <div class="card mb-4 card-flush">
                            <div class="card-body">
                                <div >
                                    <h2 class="fw-bold">Detail Pemesanan</h2>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <table class="w-100">
                                                <tbody class="detail-table">
                                                    <tr>
                                                        <td width="50%" class="text-gray-600">Nomor Transaksi</td>
                                                        <td>:</td>
                                                        <td class="fw-bold">{{data_content.data_detail.transaction_number}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="50%" class="text-gray-600">Tanggal Pemesanan</td>
                                                        <td>:</td>
                                                        <td class="fw-bold">
                                                            {{data_content.data_detail.transaction_date}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="50%" class="text-gray-600">Nominal Pembayaran</td>
                                                        <td>:</td>
                                                        <td class="fw-bold">
                                                            {{data_content.data_detail.total_price}}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4 card-flush">
                            <div class="card-body">
                                <div >
                                    <h3 class="fw-bold">Status Pemesanan</h3>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <table class="w-100">
                                                <tbody class="detail-table">
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Status</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.status}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Nama Rekening Pengirim</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.account_name}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Waktu Pembayaran</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.payment_date}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Bukti Bayar</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        <a href="#" class="text-primary text-hover-dark">Lihat bukti bayar</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Catatan</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.note}}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div class="flex justify-end mt-10 gap-2">
                                                <button class="btn btn-sm btn-primary">
                                                    Konfirmasi
                                                </button>
                                                <button class="btn btn-sm btn-secondary">
                                                    Upload Ulang Bukti Bayar
                                                </button>
                                                <button class="btn btn-sm btn-danger">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-flush">
                            <div class="card-body">
                                <div >
                                    <h3 class="fw-bold">Detail Tiket</h3>
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
                                                                        {{ticket.subtotal_price}}
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
                                                        0000
                                                    </span>
                                                </div>
                                                <div class="flex justify-between mb-2">
                                                    <span>Kode Unik</span>
                                                    <span>
                                                        0000
                                                    </span>
                                                </div>
                                                <div class="flex justify-between mb-2">
                                                    <span>Diskon</span>
                                                    <span>
                                                        0000
                                                    </span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Total</span>
                                                    <span>
                                                        0000
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
                            <div class="card-body">
                                <h3 class="fw-bold">Detail Pemesan</h3>
                                <div class="mt-4">
                                    <img src="/assets/default-profile.jpg" alt="Profile Image" class="rounded w-full h-fit">
                                </div>
                                <div class="mt-4">
                                    <h4 class="fw-bold">Nama</h4>
                                    <p>Nama User</p>
                                    <h4 class="mt-2 fw-bold">No Whatsapp</h4>
                                    <p>Phone Number</p>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="col-span-2 text-right">-->
<!--                        <router-link to="/panel/transactions"-->
<!--                            class="btn bg-slate-200 hover:bg-slate-100 me-5">Kembali</router-link>-->
<!--                        <button v-if="form_props.edit_mode" :disabled="form_props.is_loading" @click="acceptPayment"-->
<!--                            class="btn btn-success me-5">-->
<!--                            <span v-if="!form_props.is_loading">Accept</span>-->
<!--                            <span v-if="form_props.is_loading">Please wait...-->
<!--                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>-->
<!--                            </span>-->
<!--                        </button>-->
<!--                        <button v-if="form_props.edit_mode" :disabled="form_props.is_loading" @click="deletePayment"-->
<!--                            class="btn bg-red-600 hover:bg-red-500 text-white">-->
<!--                            <span v-if="!form_props.is_loading">Delete</span>-->
<!--                            <span v-if="form_props.is_loading">Please wait...-->
<!--                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>-->
<!--                            </span>-->
<!--                        </button>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import { reactive } from "vue";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import { useRouter, useRoute } from "vue-router";
import Axios from "axios";

export default {
    components: { Breadcrumb },
    setup() {
        const { getData, patchData, postData } = useAxios()
        const router = useRouter()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()
        const route = useRoute()
        // Cek Mode
        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: true,
        })

        const data_content = reactive({
            data_detail: {
                users: []
            }
        })

        const param_id = route.params.id

        const title = "Transaction Detail"
        const breadcrumb_list = ["Transaction ", "Detail"];

        const form = reactive({
            id: '',
            name: '',
        })

        if (form_props.edit_mode) {
            getData('transactions/' + param_id)
                .then((data) => {
                    data_content.data_detail = data.result
                })
        }

        function editData() {
            form_props.is_loading = true
            patchData('transactions-process/' + param_id, form).then((data) => {
                form_props.is_loading = false;
                if (data.success) {
                    router.push('/panel/transactions')
                    resetErrors()
                } else {
                    setErrors(data.errors)
                }
            })
        }

        function deletePayment() {
            if (confirm("Delete Payment?")) {
                form_props.is_loading = true
                patchData('transaction-delete', {
                    'transaction_id': param_id
                }).then((data) => {
                    form_props.is_loading = false;
                    if (data.status) {
                        router.push('/panel/transactions')
                        resetErrors()
                    } else {
                        setErrors(data.errors)
                    }
                })
            }
        }

        function acceptPayment() {
            if (confirm("Confirm Payment?")) {
                form_props.is_loading = true
                patchData('transaction-confirm', {
                    'transaction_id': param_id
                }).then((data) => {
                    form_props.is_loading = false;
                    if (data.success) {
                        router.push('/panel/transactions')
                        resetErrors()
                    } else {
                        setErrors(data.errors)
                    }
                })
            }
        }

        function printName(trx, trx_detail) {
            let code = trx.number
            let event_id = trx_detail.event_id
            let url = window.location.origin + '/print-by-name?name=' + trx.user_name

            Axios.post('/api/pub/scan-qrcode-event', {
                code: code,
                event_id: event_id
            }).then(({ data }) => {
                if (data.success) {
                    window.open(url, '_blank').focus();
                } else {
                    alert(data.message)
                }
            })
        }

        return {
            breadcrumb_list,
            title,
            form,
            form_props,
            getStatus,
            getMessage,
            editData,
            data_content,
            acceptPayment,
            deletePayment,
            printName
        }
    }
}
</script>

<style scoped>
.detail-table td {
    padding: 5px 0;
}
</style>
