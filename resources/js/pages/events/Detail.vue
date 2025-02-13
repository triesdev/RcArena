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
                <div class="text-2xl font-semibold mb-2">
                    {{ event.name }}
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <div class="card card-flush p-4">
                            <img :src="event.image_uri" class="w-full h-auto" alt="">
                            <div>
                                <button @click="showDetailModal" class="btn btn-primary btn-sm text-center w-full">
                                    Lihat Detail Event
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-3">
                        <!-- <DetailTicket></DetailTicket> -->
                        <DetailTicketBundle></DetailTicketBundle>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fw-bolder" id="ticketModalLabel">Detail Event</h2>
                    </div>
                    <div class="modal-body">
                        <div class="fw-bolder">Nama</div>
                        <div class="mb-2">{{ event.name }}</div>
                        <div class="fw-bolder">Deskripsi</div>
                        <div class="mb-2">{{ event.description }}</div>
                        <div class="fw-bolder">Tanggal Acara</div>
                        <div class="mb-2">{{ event.event_date }}</div>
                        <div class="fw-bolder">Jadwal</div>
                        <div class="mb-2" v-html="event.schedules"></div>
                        <div class="grid grid-cols-2">
                            <div>
                                <div class="fw-bolder">Lokasi</div>
                                <div class="mb-2">{{ event.location_name }}</div>
                            </div>
                            <div>
                                <div class="fw-bolder">Alamat</div>
                                <div class="mb-2">{{ event.location_address }}</div>
                            </div>
                            <div>
                                <div class="fw-bolder">Launching</div>
                                <div class="mb-2">{{ event.event_launch_at }}</div>
                            </div>
                            <div>
                                <div class="fw-bolder">Mulai Penjualan Tiket</div>
                                <div class="mb-2">{{ event.ticket_purchasing_at }}</div>
                            </div>
                            <div>
                                <div class="fw-bolder">Mulai Acara</div>
                                <div class="mb-2">{{ event.event_start }}</div>
                            </div>
                            <div>
                                <div class="fw-bolder">Selesai Acara</div>
                                <div class="mb-2">{{ event.event_end }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Breadcrumb from "../../components/Breadcrumb";
import { onMounted, reactive, ref } from "vue";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import { useRouter, useRoute } from "vue-router";
import { VueEditor } from "vue3-editor";
import collapse from "bootstrap/js/src/collapse";
import DetailTicket from "./DetailTicket.vue";
import DetailTicketBundle from "./DetailTicketBundle.vue";

export default {
    computed: {
        collapse() {
            return collapse
        }
    },
    components: { Breadcrumb, VueEditor, DetailTicket, DetailTicketBundle },
    setup() {
        const { postData, getData, patchData } = useAxios()
        const router = useRouter()
        const { setErrors, getStatus, getMessage, resetErrors } = useValidation()
        const route = useRoute()

        let detailModal = ref(null);
        onMounted(() => {
            const detailModalElement = document.getElementById('detailModal');
            if (detailModalElement) {
                detailModal.value = new bootstrap.Modal(detailModalElement);
            }
        });

        // Cek Mode
        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
            organizers: [],
            is_collapsed: false
        })

        const form = reactive({
            id: "",
            event_id: "",
            event_name: "",
            name: "",
            price: "",
            reward: "",
            is_active: "",
        })

        const event = reactive({
            id: "",
            user_organizer_id: "",
            created_by_user_id: "",
            name: "",
            image_uri: "",
            description: "",
            event_launch_at: "",
            ticket_purchasing_at: "",
            location_name: "",
            location_address: "",
            event_date: "",
            event_start: "",
            event_end: "",
            schedules: "",
            is_active: "",
        })

        const param_id = route.params.id
        form_props.edit_mode = param_id !== 'add'

        const title = "Detail Event"
        const breadcrumb_list = ["Event", "Detail"];

        function getDetail() {
            form_props.is_loading = true
            getData('events/' + param_id)
                .then((data) => {
                    event.id = data.result.id
                    event.user_organizer_id = data.result.user_organizer_id
                    event.created_by_user_id = data.result.created_by_user_id
                    event.name = data.result.name
                    event.image_uri = data.result.image_uri
                    event.description = data.result.description
                    event.event_launch_at = data.result.event_launch_at
                    event.ticket_purchasing_at = data.result.ticket_purchasing_at
                    event.location_name = data.result.location_name
                    event.location_address = data.result.location_address
                    event.event_date = data.result.event_date
                    event.event_start = data.result.event_start
                    event.event_end = data.result.event_end
                    event.schedules = data.result.schedules
                    event.is_active = data.result.is_active

                    form_props.is_loading = false
                }).catch(() => {
                    form_props.is_loading = false
                })
        }

        getDetail()

        function showDetailModal() {
            detailModal.value.show();
        }

        return {
            title,
            breadcrumb_list,
            form_props,
            event,
            showDetailModal,
            form,
            getStatus
        }
    }
}
</script>
