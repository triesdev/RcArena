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
        <div class="card card-flush p-8 mb-4">
          <div class="row">
            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
              <label class="form-label text-slate-400">Nama Paket</label>
              <input type="text" class="form-control form-control-sm mb-2" v-model="form.name">
              <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('name')">
                {{ getMessage('name') }}
              </div>
            </div>
            <div class="mb-5 col-md-12 fv-row fv-plugins-icon-container">
              <label class="form-label text-slate-400">Harga</label>
              <input type="text" class="form-control form-control-sm mb-2" v-model="form.price">
              <div class="fv-plugins-message-container invalid-feedback" v-if="getStatus('price')">
                {{ getMessage('price') }}
              </div>
            </div>
          </div>
          <div class="mb-4">
            <button @click="showTicketModal()" class="btn btn-primary">
              Tambah Varian
            </button>
          </div>
          <div>
            <div class="relative overflow-x-auto">
              <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-sm text-gray-700 uppercase bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3">
                      Kelas
                    </th>
                    <th scope="col" class="px-6 py-3">
                      Variant
                    </th>
                    <th scope="col" class="px-6 py-3">
                      Harga
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="form.tickets.length === 0">
                    <td colspan="4" class="text-center"><i>Tidak ada data.</i></td>
                  </tr>
                  <tr class="bg-white border-b  border-gray-200" v-for="item in form.tickets">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                      {{ item.class_name }}
                    </th>
                    <td class="px-6 py-4">
                      {{ item.name }}
                    </td>
                    <td class="px-6 py-4">
                      {{ $filter.currency(item.price) }}
                    </td>
                    <td class="px-6 py-4">
                      <button @click="subTicket(item)"
                        class="btn btn-sm btn-danger flex items-center justify-center w-1 h-[2rem]">
                        <v-icon name="bi-trash"></v-icon>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="mb-4 text-right">
          <button @click="addTicketBundle()" class="btn btn-primary" v-if="!form_props.edit_mode">
            Tambah
          </button>
          <button @click="editTicketBundle()" class="btn btn-primary" v-if="form_props.edit_mode">
            Simpan
          </button>
        </div>
      </div>
      <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h2 class="modal-title fw-bolder">Tiket</h2>
            </div>
            <div class="modal-body">
              <div class="w-full rounded-xl p-4 bg-slate-50 mb-2" v-for="item in form_props.classes">
                <div class="flex justify-content-between border-b border-black">
                  <div>
                    <div>
                      <span class="font-semibold">{{ item.name }}</span> <span class="ml-2">({{
                        item.ticket.length }} varian)</span>
                    </div>
                    <div class="text-sm">
                      <span>Rp {{ $filter.currency(item.price) }}</span><span class="ml-2">
                        {{ item.is_active === 1 ? "Aktif" : "Tidak Aktif" }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex justify-between my-1" v-for="ticket in item.ticket">
                  <div>
                    <span class="font-semibold">{{ ticket.name }}</span>
                  </div>
                  <div>
                    <button @click="addTicket(ticket)" v-if="!isAdded(ticket)" class="btn btn-success btn-sm">
                      <v-icon name="bi-plus"></v-icon>
                    </button>
                    <button @click="subTicket(ticket)" v-if="isAdded(ticket)" class="btn btn-danger btn-sm">
                      <v-icon name="bi-trash"></v-icon>
                    </button>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <button class="btn btn-secondary" @click="closeTicketModal()">Selesai</button>
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
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import useAxios from "../../src/service";
import useValidation from "../../src/validation";

export default {
  components: { Breadcrumb },
  setup() {
    const { postData, getData, patchData } = useAxios()
    const router = useRouter()
    const route = useRoute()
    const { setErrors, getStatus, getMessage, resetErrors } = useValidation()

    // Cek Mode
    const form_props = reactive({
      is_loading: false,
      errors: [],
      edit_mode: false,
      classes: [],
    })

    const form = reactive({
      id: "",
      event_id: event_id,
      name: "",
      price: "",
      tickets: []
    })

    const title = "Tiket Bundle"
    const breadcrumb_list = ["Event", "Detail", "Tiket Bundle"];

    const ticket_bundle_id = route.params.id
    const event_id = route.params.event_id
    if (ticket_bundle_id !== 'add') {
      form_props.edit_mode = true
      loadTicketBundle()
    } else {
      form_props.edit_mode = false
    }

    function loadTicketBundle() {
      getData("ticket-bundle/" + ticket_bundle_id).then((data) => {
        form.id = ticket_bundle_id
        form.event_id = data.result.event_id
        form.name = data.result.name
        form.price = data.result.price
        form.tickets = data.result.tickets
      })
    }

    function loadClasses() {
      getData("panel-classes", {
        event_id: route.params.event_id
      }).then((data) => {
        form_props.classes = data.result
      })
    }

    loadClasses()

    let ticketModal = ref(null);
    onMounted(() => {
      const ticketModalElement = document.getElementById('ticketModal');
      if (ticketModalElement) {
        ticketModal.value = new bootstrap.Modal(ticketModalElement);
      }
    });

    function showTicketModal() {
      ticketModal.value.show();
    }

    function addTicket(ticket) {
      if (form.tickets.find(item => item.id === ticket.id)) {
        return
      }
      form.tickets.push(ticket)
    }

    function subTicket(ticket) {
      form.tickets = form.tickets.filter(item => item.id !== ticket.id)
    }

    function isAdded(ticket) {
      return form.tickets.find(item => item.id === ticket.id)
    }

    function closeTicketModal() {
      ticketModal.value.hide()
    }

    function editTicketBundle() {
      patchData("ticket-bundle/" + form.id, {
        event_id: form.event_id,
        name: form.name,
        price: form.price,
        tickets: form.tickets
      }).then((data) => {
        if (data.success) {
          router.push({
            name: 'event-detail',
            params: {
              id: event_id
            }
          })
        } else {
          setErrors(data.errors)
        }
      })
    }

    function addTicketBundle() {
      postData("ticket-bundle", {
        event_id: event_id,
        name: form.name,
        price: form.price,
        tickets: form.tickets
      }).then((data) => {
        if (data.success) {
          router.push({
            name: 'event-detail',
            params: {
              id: event_id
            }
          })
        } else {
          setErrors(data.errors)
        }
      })
    }

    return {
      title,
      breadcrumb_list,
      form,
      getStatus,
      getMessage,
      showTicketModal,
      form_props,
      addTicket,
      subTicket,
      isAdded,
      closeTicketModal,
      addTicketBundle,
      editTicketBundle
    }
  }
}
</script>
