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
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                                <Loading :active="is_loading" :loader="'dots'" :is-full-page="false" />
                                <!-- <div class="p-3" v-for="menu in form_props.menu_role">
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
                                </div> -->
                                <table class="table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Menu</th>
                                            <th v-for="type in type_list" class="text-center" style="width: 10%">
                                                {{ type.toUpperCase() }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="menu in form_props.menu_role">
                                        <tr>
                                            <td>
                                                <div class="d-flex">
                                                    <div class="ms-2" style="font-weight: bold">
                                                        {{ menu.title }} <span class="text-gray-600 font-italic"
                                                            style="font-weight: normal">{{ menu.url }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td v-for="type in type_list">
                                                <div
                                                    class="d-flex justify-center form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        @change="updateAuth(menu, type, $event.target.checked)"
                                                        :checked="menu['is_' + type]">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="menu.children && menu.children.length > 0"
                                            v-for="cld in menu.children">
                                            <td>
                                                <div class="d-flex ml-4">
                                                    <div class="ms-2" style="font-weight: bold">{{ cld.title }}
                                                        <span class="text-gray-600 font-italic"
                                                            style="font-weight: normal">
                                                            {{ cld.url }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td v-for="type in type_list">
                                                <div
                                                    class="d-flex justify-center form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        @change="updateAuth(cld, type, $event.target.checked)"
                                                        :checked="cld['is_' + type]">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
import { upperCase } from "lodash";

export default {
    components: { Breadcrumb },
    setup() {
        const title = "Menu Role"
        const breadcrumb_list = ["Menu", "Menu Role"];
        const { getData, patchData, postData } = useAxios()
        const is_loading = ref(false)

        const form_props = reactive({
            roles: [],
            menu_role: []
        })

        const form = reactive({
            role_id: ''
        })

        const type_list = ["get", "show", "post", "put", "del"]

        function loadMenuRole(role_id) {
            getData('menu-role', { role_id: role_id })
                .then((data) => {
                    form_props.menu_role = setChecked(data.result)
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

        function setChecked(menu) {
            for (var i = 0; i < menu.length; i++) {
                if (menu[i].get !== null) {
                    menu[i].is_get = 1
                } else {
                    menu[i].is_get = 0
                }
                if (menu[i].show !== null) {
                    menu[i].is_show = 1
                } else {
                    menu[i].is_show = 0
                }
                if (menu[i].post !== null) {
                    menu[i].is_post = 1
                } else {
                    menu[i].is_post = 0
                }
                if (menu[i].put !== null) {
                    menu[i].is_put = 1
                } else {
                    menu[i].is_put = 0
                }
                if (menu[i].del !== null) {
                    menu[i].is_del = 1
                } else {
                    menu[i].is_del = 0
                }

                if (menu[i].children && menu[i].children.length > 0) {
                    for (var j = 0; j < menu[i].children.length; j++) {
                        if (menu[i].children[j].get !== null) {
                            menu[i].children[j].is_get = 1
                        } else {
                            menu[i].children[j].is_get = 0
                        }
                        if (menu[i].children[j].show !== null) {
                            menu[i].children[j].is_show = 1
                        } else {
                            menu[i].children[j].is_show = 0
                        }
                        if (menu[i].children[j].post !== null) {
                            menu[i].children[j].is_post = 1
                        } else {
                            menu[i].children[j].is_post = 0
                        }
                        if (menu[i].children[j].put !== null) {
                            menu[i].children[j].is_put = 1
                        } else {
                            menu[i].children[j].is_put = 0
                        }
                        if (menu[i].children[j].del !== null) {
                            menu[i].children[j].is_del = 1
                        } else {
                            menu[i].children[j].is_del = 0
                        }
                    }
                }
            }
            return menu
        }

        loadRoles()

        function updateAuth(menu, type, val) {
            if (val) {
                postData('update-menu-role', {
                    role_id: form.role_id,
                    menu_id: menu.id,
                    method_access: upperCase(type),
                    action: 'add'
                }).then((data) => { })
            } else {
                postData('update-menu-role', {
                    role_id: form.role_id,
                    menu_id: menu.id,
                    method_access: upperCase(type),
                    action: 'delete'
                }).then((data) => { })
            }
        }
        return {
            breadcrumb_list,
            title,
            form,
            is_loading,
            form_props,
            loadMenuRole,
            type_list,
            updateAuth
        }
    }
}
</script>
