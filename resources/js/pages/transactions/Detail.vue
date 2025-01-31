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
                                                            {{$filter.currency(data_content.data_detail.total_price)}}
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
                                <div v-if="data_content.data_detail.payment">
                                    <h3 class="fw-bold">Status Pemesanan</h3>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <table class="w-100">
                                                <tbody class="detail-table">
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Status</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        <span class="badge badge-sm" :class="{
                                                            'badge-primary': data_content.data_detail.payment.payment_status === 'new',
                                                            'badge-success': data_content.data_detail.payment.payment_status === 'confirmed',
                                                            'badge-danger': data_content.data_detail.payment.payment_status === 'reject',
                                                            'badge-warning': data_content.data_detail.payment.payment_status === 'pending'
                                                        }">
                                                            {{data_content.data_detail.payment.payment_status.toUpperCase()}}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Nama Rekening Pengirim</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.payment.account_name}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Waktu Pembayaran</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.payment.payment_date}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Bukti Bayar</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        <a target="_blank" :href="data_content.data_detail.payment.payment_proof_image_uri" class="text-primary text-hover-dark">Lihat bukti bayar</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" class="text-gray-600">Catatan</td>
                                                    <td>:</td>
                                                    <td class="fw-bold">
                                                        {{data_content.data_detail.payment.note}}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <div v-if="data_content.data_detail.payment.payment_status == 'new'" class="flex justify-end mt-10 gap-2">
                                                <button @click="openModalConfirm('Konfirmasi','confirmed')" class="btn btn-sm btn-primary">
                                                    Konfirmasi
                                                </button>
                                                <button @click="openModalConfirm('Upload Ulang','pending')" class="btn btn-sm btn-secondary">
                                                    Upload Ulang Bukti Bayar
                                                </button>
                                                <button @click="openModalConfirm('Reject','reject')" class="btn btn-sm btn-danger">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <h3 class="fw-bold">Status Pemesanan</h3>
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div class="col-span-2">
                                            <span class="fw-bold text-danger flex align-middle">
                                                <v-icon name="bi-exclamation-circle-fill" class="mr-2"></v-icon> Belum ada pembayaran
                                            </span>
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
                        <router-link class="btn btn-danger btn-sm mt-2 w-100" to="/panel/transactions">
                            Kembali
                        </router-link>
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
        <WidgetContainerModal></WidgetContainerModal>
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import { reactive, ref } from "vue";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import { useRouter, useRoute } from "vue-router";
import Axios from "axios";
import { container, promptModal} from "jenesius-vue-modal";
import ModalApprove from "./ModalApprove.vue";
import SwalToast from '../../src/swal_toast'

export default {
    components: { Breadcrumb, WidgetContainerModal: container },
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

        const payment_id = ref(0);

        function getDetail(){
            form_props.is_loading = true
            getData('transactions/' + param_id)
                .then((data) => {
                    data_content.data_detail = data.result
                    if (data_content.data_detail.payment){
                        payment_id.value = data_content.data_detail.payment.id
                    }

                    form_props.is_loading = false
                })
        }

        if (form_props.edit_mode) {
           getDetail();
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

         async function openModalConfirm(title, confirm_type) {
            const boolResp = await promptModal(ModalApprove, {
                title: title,
                payment_id: payment_id.value,
                confirm_type: confirm_type
            },{
                backgroundClose: false,
            });

            if (boolResp) {
                SwalToast('Berhasil memproses pembayaran.')
                getDetail()
            }
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
            printName,
            openModalConfirm,
        }
    }
}
</script>

<style scoped>
.detail-table td {
    padding: 5px 0;
}
</style>
