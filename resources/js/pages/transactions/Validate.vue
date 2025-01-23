<template>
  <div class="d-flex flex-column flex-column-fluid" style="min-height: calc(100vh - 130px)">
    <div class="app-content flex-column-fluid">
      <div class="app-container container-xxl py-4">
        <div class="row">
          <div class="col-md-6">
            <div class="card p-4">
              <div v-if="data_content.data_detail && data_content.data_detail.transfer_proof">
                <div class="text-center font-bold">Transfer Proof</div>
                <div class="flex justify-center mb-4">
                  <img :src="data_content.data_detail.transfer_proof" class="w-full" alt="">
                </div>
                <div class="text-center">
                  <a :href="data_content.data_detail.transfer_proof" target="_blank"
                    class="font-semibold hover:text-blue-500 text-blue-600">Original File</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card p-5">
              <div>
                <b>{{ data_content.data_detail.number }}</b>
              </div>
              <div>
                {{ data_content.data_detail.user_name }}
              </div>
              <div>
                Rp {{ $filter.currency(data_content.data_detail.total) }}
              </div>
              <div class="mt-10 mb-2">
                <label>Note</label>
                <input type="text" class="form-control" v-model="data_content.data_detail.note">
              </div>
              <div class="mb-2">
                <label>GL Name</label>
                <input type="text" class="form-control" v-model="data_content.data_detail.gl_name">
              </div>
              <div class="mb-2">
                <label>GL Date</label>
                <input type="date" class="form-control" v-model="data_content.data_detail.gl_date">
              </div>
              <div class="mb-4">
                <label>GL Status</label>
                <select class="form-control" v-model="data_content.data_detail.gl_status">
                  <option value="0">Pending</option>
                  <option value="1">Valid</option>
                  <option value="2">Reject</option>
                </select>
              </div>
              <div class="flex justify-end">
                <button class="btn btn-primary" @click="updateValidation()">
                  Simpan
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import useAxios from "../../src/service";
import { reactive, ref } from "vue";
import { useFilterStore } from "../../src/store_filter";
import { useRouter, useRoute } from "vue-router";

export default {
  components: {},
  setup() {
    const title = "Data Transaction"
    const router = useRouter()
    const { getData, patchData, postData } = useAxios()
    const route = useRoute()

    const param_id = route.params.id

    const data_content = reactive({
      data_detail: {
        users: []
      }
    })

    function loadData(trx_id = null) {
      if (trx_id == null) {
        trx_id = param_id
      }
      getData('transactions/' + trx_id)
        .then((data) => {
          data_content.data_detail = data.result
        })
    }

    loadData()

    function updateValidation() {
      patchData('transactions-validate/' + data_content.data_detail.id, {
        gl_name: data_content.data_detail.gl_name,
        gl_date: data_content.data_detail.gl_date,
        gl_status: data_content.data_detail.gl_status,
        note: data_content.data_detail.note
      })
        .then((data) => {
          if (data.result.next_id) {
            data_content.data_detail = {
              users: []
            }
            loadData(data.result.next_id)
          }
        })
    }

    return {
      data_content,
      updateValidation
    }
  }
}
</script>
