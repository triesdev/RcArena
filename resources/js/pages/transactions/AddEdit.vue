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
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 md:col-span-1">
                        <div class="card card-flush">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div v-if="data_content.data_detail && data_content.data_detail.user">
                                        <img :src="data_content.data_detail.user.image" class="w-20" alt="">
                                    </div>
                                    <div class="m-4" v-if="data_content.data_detail && data_content.data_detail.user">
                                        <div class="text-lg font-medium">{{ data_content.data_detail.user_name }}</div>
                                        <div class="">{{ data_content.data_detail.job_type_code }}</div>
                                        <div class="">{{ data_content.data_detail.user_email }}</div>
                                    </div>
                                </div>
                                <div v-if="data_content.data_detail">
                                    <div class="mb-2">
                                        <label class="text-sm font-medium italic text-slate-500">Nomor</label>
                                        <div>{{ data_content.data_detail.number }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-sm font-medium italic text-slate-500">Status</label>
                                        <div>{{ data_content.data_detail.status_label }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-sm font-medium italic text-slate-500">Subtotal</label>
                                        <div>{{ $filter.currency(data_content.data_detail.subtotal) }}</div>
                                    </div>
                                    <div class="mb-2" v-if="data_content.data_detail.voucher_code">
                                        <label class="text-sm font-medium italic text-slate-500">Voucher Code</label>
                                        <div>{{ data_content.data_detail.voucher_code }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-sm font-medium italic text-slate-500">Vucher Discount</label>
                                        <div>{{ $filter.currency(data_content.data_detail.voucher_discount) }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-sm font-medium italic text-slate-500">Package
                                            Discount</label>
                                        <div>{{ $filter.currency(parseInt(data_content.data_detail.package_discount)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-sm">Total</label>
                                        <div class="font-semibold text-lg">Rp {{
                                            $filter.currency(data_content.data_detail.total) }}</div>
                                    </div>
                                </div>
                                <div class="mt-4 font-semibold text-lg">
                                    Detail Event
                                </div>
                                <div v-if="data_content.data_detail && data_content.data_detail.transaction_details">
                                    <div v-for="trx in data_content.data_detail.transaction_details"
                                        class="border-b mb-2 py-2">
                                        <div class="font-semibold">{{ trx.event_name }}</div>
                                        <div v-if="trx.event">
                                            <div class="text-slate-500">{{ trx.event.title }}</div>
                                            <div class="text-sm italic text-slate-500">
                                                {{ $filter.formatDateTime(trx.event.date_start) }}
                                            </div>
                                            <div>
                                                <button class="btn btn-primary btn-sm"
                                                    @click="printName(data_content.data_detail, trx)">Print
                                                    Name</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-flush mt-4" v-if="data_content.data_detail.users.length > 0">
                            <div class="card-body">
                                <div class="mt-4 font-semibold text-lg">
                                    Collective Registration
                                </div>
                                <div v-for="cld in data_content.data_detail.users" class="mb-2">
                                    <div>{{ cld.user_name }}</div>
                                    <div class="flex justify-between text-sm text-slate-500">
                                        <div>{{ cld.user_email }}</div>
                                        <div :title="cld.nik.length">{{ cld.nik }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <div class="card card-flush">
                            <div class="card-body">
                                <div class="mb-4"
                                    v-if="data_content.data_detail && data_content.data_detail.user && data_content.data_detail.user.identity_photo">
                                    <div class="text-center font-bold">Identity Photo</div>
                                    <div class="flex justify-center mb-4">
                                        <a :href="data_content.data_detail.user.identity_photo" target="_blank">
                                            <img :src="data_content.data_detail.user.identity_photo" class="max-h-96"
                                                alt="">
                                        </a>
                                    </div>
                                </div>
                                <div v-if="data_content.data_detail" class="mb-4">
                                    <div class="text-center font-bold">Plataran Account</div>
                                    <div class="flex justify-center mb-4">
                                        <a :href="data_content.data_detail.plataran_img" target="_blank">
                                            <img :src="data_content.data_detail.plataran_img" class="max-h-96" alt="">
                                        </a>
                                    </div>
                                </div>
                                <div v-if="data_content.data_detail && data_content.data_detail.transfer_proof">
                                    <div class="text-center font-bold">Transfer Proof</div>
                                    <div class="flex justify-center mb-4">
                                        <img :src="data_content.data_detail.transfer_proof" class="w-1/2" alt="">
                                    </div>
                                    <div class="text-center">
                                        <a :href="data_content.data_detail.transfer_proof" target="_blank"
                                            class="font-semibold hover:text-blue-500 text-blue-600">Original File</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 text-right">
                        <router-link to="/panel/transactions"
                            class="btn bg-slate-200 hover:bg-slate-100 me-5">Kembali</router-link>
                        <button v-if="form_props.edit_mode" :disabled="form_props.is_loading" @click="acceptPayment"
                            class="btn btn-success me-5">
                            <span v-if="!form_props.is_loading">Accept</span>
                            <span v-if="form_props.is_loading">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <button v-if="form_props.edit_mode" :disabled="form_props.is_loading" @click="deletePayment"
                            class="btn bg-red-600 hover:bg-red-500 text-white">
                            <span v-if="!form_props.is_loading">Delete</span>
                            <span v-if="form_props.is_loading">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
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

        const title = form_props.edit_mode ? "Update Transaction" : ""
        const breadcrumb_list = ["Transaction ", form_props.edit_mode ? "Update" : "Tambah"];

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
