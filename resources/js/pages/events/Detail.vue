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
                    <div class="card card-flush p-4">
                        <img :src="event.image_uri" class="w-full h-auto" alt="">
                        <div>
                            <router-link href="#"
                               class="btn btn-primary btn-sm text-center w-full">
                                Lihat Detail Event
                            </router-link>
                        </div>
                    </div>
                    <div class="card card-flush col-span-3">
                        <div class="card-body">
                            <div class="text-xl font-semibold mb-2">Kelas</div>
                            <div>
                                <div class="w-full rounded-xl p-2 bg-amber-200">
                                    Nama
                                    <div>
                                        sub content
                                    </div>
                                    <div>
                                        sub content
                                    </div>
                                </div>
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
import {reactive, ref} from "vue";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";
import {useRouter, useRoute} from "vue-router";
import {handleFileProcessData} from "../../src/firebase_upload";
import {VueEditor} from "vue3-editor";

export default {
    components: {Breadcrumb, VueEditor},
    setup() {
        const {postData, getData, patchData} = useAxios()
        const router = useRouter()
        const {setErrors, getStatus, getMessage, resetErrors} = useValidation()
        const route = useRoute()
        // Cek Mode
        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
            organizers: [],
        })

        const event = reactive({
            id: '',
            user_organizer_id: '',
            name: '',
            image_uri: '',
            description: '',
            event_launch_at: '',
            ticket_purchasing_at: '',
            location_name: '',
            location_address: '',
            location_lat: '',
            location_long: '',
            location_uri: '',
            status: '',
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
                    event.name = data.result.name
                    event.image_uri = data.result.image_uri
                    event.description = data.result.description
                    event.event_launch_at = data.result.event_launch_at
                    event.ticket_purchasing_at = data.result.ticket_purchasing_at
                    event.location_name = data.result.location_name
                    event.location_address = data.result.location_address
                    event.location_lat = data.result.location_lat
                    event.location_long = data.result.location_long
                    event.location_uri = data.result.location_uri
                    event.status = data.result.status
                    form_props.is_loading = false
                }).catch(() => {
                form_props.is_loading = false
            })
        }

        getDetail()

        function loadClass(){
            getData('events/' + param_id)
        }

        return {
            title,
            breadcrumb_list,
            form_props,
            event
        }
    }
}
</script>
