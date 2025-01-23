<template>
    <div class="container">
        <div class="grid">
            <div class="card card-grid min-w-full">
                <div class="card-header py-5 flex-wrap">
                    <h3 class="card-title">
                        Events
                    </h3>
                    <router-link :to="{ name: 'CreateEvent' }" class="btn btn-primary">
                        <i class="ki-filled ki-plus"></i>Add Event
                    </router-link>
                </div>
                <div class="card-body">
                    <div class="flex m-3">

                        <!-- Category select field -->
                        <div class="flex flex-col md:flex-row gap-3">

                            <div class="flex-1">
                                <label for="name" class="text-sm font-medium text-gray-700">Name</label>
                                <input
                                    type="text"
                                    v-model="filter.name"
                                    id="name"
                                    placeholder="Name"
                                    class="input input-bordered p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <!-- Filter button -->
                            <div class="flex">
                                <button
                                    @click="getData"
                                    class="btn mt-6 btn-primary px-4 py-2 bg-blue-500 text-white font-semibold rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <i class="ki-filled ki-filter-search"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="scrollable-x-auto pt-4">
                        <table class="table table-auto table-border align-middle text-gray-700 font-medium text-sm" data-datatable-table="true">
                            <thead class="h-[35px]">
                            <tr>
                                <th class="w-[50px] text-center">
                                    No
                                </th>
                                <th class="w-[100px] text-center">
                                    Image
                                </th>
                                <th class="w-[100px] text-center">
                                    Name
                                </th>
                                <th class="w-[100px] text-center">
                                    Event Date
                                </th>
                                <th class="w-[125px] text-center">Status</th>
                                <th class="w-[100px] text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody class="h-[1rem]">
                            <tr class="h-[200px]" v-if="loading">
                                <td colspan="6" class="text-center h-[50px]">
                                    <div class="flex justify-center w-full">
                                        <div class="loader"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else-if="dataAccounts.length === 0">
                                <td colspan="6" class="text-center h-[50px]">No data available</td>
                            </tr>
                            <tr v-else v-for="(item, index) in dataAccounts" :key="index">
                                <td class="text-center h-[50px]">{{ dynamicIndex(index) }}</td>
                                <td class="text-center h-[50px]">
                                    <img :src="item.image" alt="image" class="w-[50px] h-[50px] object-cover rounded-md">
                                </td>
                                <td class="text-center h-[50px]">{{ item.name }}</td>
                                <td class="text-center h-[50px]">{{ item.event_date }}</td>
                                <td class="text-center h-[50px]">
                                <span class="badge badge-primary badge-sm" v-if="item.is_active === 1">
                                    Active
                                </span>
                                    <span v-else class="badge badge-danger">
                                    Inactive
                                </span>
                                </td>
                                <td class="text-center h-[50px]">
                                    <div class="btn-group btn">
                                        <router-link :to="`/adm/edit-accounts/${item.id}`" class="btn btn-warning btn-sm">
                                            <i class="ki-filled ki-pencil"></i>
                                        </router-link>
                                        <button
                                            class="btn btn-danger btn-sm"
                                        >
                                            <i class="ki-filled ki-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-3 text-gray-600 text-2sm font-medium">
                    <div class="flex items-center gap-2">
                        Show
                        <select :value="perpage" @change="changeTotalRow" class="select select-sm w-16" data-datatable-size="true" name="perpage">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                        </select>
                        per page
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="pagination">
                            <button class="btn" @click="changePage(page - 1)" :disabled="page === 1">
                                <i class="ki-outline ki-black-left"></i>
                            </button>
                            <button class="btn" :class="{ 'active disabled': pageNumber === page }" v-for="pageNumber in visiblePages" :key="pageNumber" @click="changePage(pageNumber)">
                                {{ pageNumber }}
                            </button>
                            <button class="btn" @click="changePage(page + 1)" :disabled="page === totalPages">
                                <i class="ki-outline ki-black-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import {onMounted, ref, computed} from "vue";
import {authAxios} from "../../utils/axios";
import Swal from "sweetalert2";

const dataAccounts = ref([]);
const page = ref(1);
const perpage = ref(10);
const lastPage = ref(1);
const loading = ref(false);
const filter = ref({
    name: '',
    category: '',
    type: ''
});

const totalPages = computed(() => lastPage.value);

const visiblePages = computed(() => {
    const pages = [];
    const startPage = Math.max(1, page.value - 2);
    const endPage = Math.min(totalPages.value, page.value + 2);

    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    if (pages.length < 5 && startPage > 1) {
        for (let i = startPage - 1; i >= 1 && pages.length < 5; i--) {
            pages.unshift(i);
        }
    }

    if (pages.length < 5 && endPage < totalPages.value) {
        for (let i = endPage + 1; i <= totalPages.value && pages.length < 5; i++) {
            pages.push(i);
        }
    }

    return pages;
});

const getData = () => {
    loading.value = true;
    authAxios.get(`/accounts?pagination=true&page=${page.value}&per_page=${perpage.value}&name=${filter.value.name}&category=${filter.value.category}&type=${filter.value.type}`)
        .then(({data}) => {
            const {result} = data;
            dataAccounts.value = result.data;
            lastPage.value = result.last_page;
            page.value = result.current_page;
            loading.value = false;
        })
        .catch(error => {
            loading.value = false
        });
};

const changePage = (newPage: number) => {
    page.value = newPage;
    getData();
};

const changeTotalRow = (e: Event) => {
    perpage.value = parseInt((e.target as HTMLSelectElement).value);
    page.value = 1;
    getData();
};

const dynamicIndex = (index: number) => {
    return (page.value - 1) * perpage.value + index + 1;
};

// Delete Account

const deleteAccount = (id: number) => {

    // Swal Confirmations
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            authAxios.delete(`/accounts/${id}`)
                .then(() => {
                    getData();
                })
                .catch(() => {
                    getData();
                });
        }
    });
};

onMounted(() => {
    getData()
});
</script>
<style scoped>
.active {
    background-color: #007bff;
    color: white;
}

.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
