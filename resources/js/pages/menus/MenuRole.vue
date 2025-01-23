<template>
    <div class="d-flex flex-column flex-column-fluid" style="min-height: calc(100vh - 130px)">
        <div class="app-toolbar py-3 py-lg-6">
            <div class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        {{ title }}
                    </h1>
                    <Breadcrumb :list="breadcrumb_list"></Breadcrumb>
                </div>
            </div>
        </div>
        <div class="app-content flex-column-fluid">
            <div class="app-container container-xxl">
                <div class="card card-flush">
                    <div class="card-header align-items-center py-5 gap-2 gap-md-5"
                        data-select2-id="select2-data-124-lq0k">
                        <div class="card-title">
                            <select v-model="form.role_id" @change="loadMenuRole(form.role_id)"
                                class="form-control form-control-solid w-250px">
                                <option :value="role.id" v-for="role in form_props.roles">{{ role.name }}</option>
                            </select>
                        </div>
                        <div class="card-toolbar flex-row-fluid justify-content-end gap-5"
                            data-select2-id="select2-data-123-4p2n">
                            <button class="btn btn-sm fw-bold btn-primary" @click="updateData">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <Loading :active="is_loading" :loader="'dots'" :is-full-page="false" />
                                <div class="p-3" v-for="menu in form_props.menu_role">
                                    <div class="d-flex">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" v-model="menu.selected"
                                                value="1">
                                        </div>
                                        <div class="ms-2" style="font-weight: bold">
                                            {{ menu.title }} <span class="text-gray-600 font-italic"
                                                style="font-weight: normal">{{ menu.url }}</span>
                                        </div>
                                    </div>
                                    <div v-if="menu.children" class="ms-10">
                                        <div v-for="cld in menu.children" class="d-flex pt-3">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" v-model="cld.selected"
                                                    value="1">
                                            </div>
                                            <div class="ms-2" style="font-weight: bold">{{ cld.title }}
                                                <span class="text-gray-600 font-italic" style="font-weight: normal">{{
                                                    cld.url }}</span>
                                            </div>
                                        </div>
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
import useAxios from "../../src/service";
import { reactive, ref } from "vue";
import SwalToast from "../../src/swal_toast";

export default {
    components: { Breadcrumb },
    setup() {
        const title = "Menu Role"
        const breadcrumb_list = ["Menu", "Menu Role"];
        const { getData, patchData } = useAxios()
        const is_loading = ref(false)

        const form_props = reactive({
            roles: [],
            menu_role: []
        })

        const form = reactive({
            role_id: ''
        })

        function loadMenuRole(role_id) {
            getData('menu-role', { role_id: role_id })
                .then((data) => {
                    form_props.menu_role = data.result
                })
        }

        function loadRoles() {
            getData('roles-list')
                .then((data) => {
                    form_props.roles = data.result
                    form.role_id = form_props.roles[0]['id']
                    loadMenuRole(form.role_id)
                })
        }

        loadRoles()

        function updateData() {
            patchData('menu-role', {
                role_id: form.role_id,
                menu_roles: form_props.menu_role
            }).then((data) => {
                emitter.emit("sidebar-update-menu");
                SwalToast('Berhasil memperbarui data.')
            })
        }

        return {
            breadcrumb_list,
            title,
            form,
            is_loading,
            form_props,
            updateData,
            loadMenuRole
        }
    }
}
</script>
