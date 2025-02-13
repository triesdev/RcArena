<template>
    <div class="card card-flush mb-4">
        <div class="card-body">
            <div class="flex justify-between mb-4">
                <div class="text-xl font-bold mb-4">Paket</div>
                <div>
                    <router-link :to="'/panel/ticket-bundle/' + route.params.id + '/add'"
                        class="btn btn-primary btn-sm text-center">
                        Tambah Paket
                    </router-link>
                </div>
            </div>
            <div>
                <div class="w-full rounded-xl p-4 bg-slate-50 mb-2" v-for="item in form_props.ticket_bundles">
                    <div class="flex justify-content-between border-b border-black">
                        <div>
                            <div>
                                <span class="font-semibold">{{ item.name }}</span>
                            </div>
                            <div class="text-sm">
                                <span>Rp {{ $filter.currency(item.price) }}</span><span class="ml-2">
                                    {{ item.is_active === 1 ? "Aktif" : "Tidak Aktif" }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle btn-sm" data-toggle="dropdown">
                                    Aksi
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <router-link :to="'/panel/ticket-bundle/' + route.params.id + '/' + item.id"
                                        class="dropdown-item">
                                        Edit
                                    </router-link>
                                    <button class="dropdown-item" @click="deleteModal(item.id)">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between my-2" v-for="ticket in item.tickets">
                        <div>
                            <span class="font-semibold">{{ ticket.name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import { reactive, ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import useAxios from "../../src/service";
import router from "../../src/router";

export default {
    setup() {
        const route = useRoute()
        const { postData, getData, patchData } = useAxios()

        const form_props = reactive({
            is_loading: false,
            errors: [],
            edit_mode: false,
            ticket_bundles: [],
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

        function loadTicketBundle() {
            getData("ticket-bundle", {
                event_id: route.params.id
            }).then((data) => {
                form_props.ticket_bundles = data.result
            })
        }

        loadTicketBundle()

        function showAddModal() {
            form_props.edit_mode = false

            form.id = ""
            form.event_id = ""
            form.event_name = ""
            form.name = ""
            form.price = ""
            form.reward = ""

            editAddModal.value.show();
        }

        function deleteModal() {

        }

        return {
            showAddModal,
            form_props,
            form,
            route,
            deleteModal
        }
    }
}
</script>
