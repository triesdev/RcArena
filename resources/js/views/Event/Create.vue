<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Add Event
                </h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="handleSubmit">
                    <div class="px-8 pt-6 pb-8 mb-4 flex flex-col">
                        <div class="-mx-3 md:flex mb-6">
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Name
                                </label>
                                <input v-model="inputEvent.name" class="w-full bg-gray-200 text-black border border-gray-200 rounded px-3 py-[.4rem]" id="name" type="text" placeholder="Name">
                                <span v-if="errors.name" class="text-red-500 text-xs">{{ errors.name[0] }}</span>
                            </div>
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Organizer
                                </label>
                                <select v-model="inputEvent.organizer_id" class="w-full text-sm bg-gray-200 text-black border border-gray-200 rounded px-3 py-[.4rem]">
                                    <option value="">Pilih Organizer</option>
                                </select>
                                <span v-if="errors.category" class="text-red-500 text-xs">{{ errors.organizer_id[0] }}</span>
                            </div>
                        </div>
                        <div class="-mx-3 md:flex mb-6">
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Description
                                </label>
                                <textarea class="w-full bg-gray-200 text-black border border-gray-200 rounded px-3 py-[.4rem]" v-model="inputEvent.description"></textarea>
                                <span v-if="errors.description" class="text-red-500 text-xs">{{ errors.description[0] }}</span>
                            </div>
                        </div>
                        <div class="-mx-3 md:flex mb-6">
                           <h5 class="font-bold">
                              <i class="ki-filled ki-information-2 mr-1"></i> Detail Event
                           </h5>
                        </div>
                        <div class="-mx-3 md:flex mb-6">
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Event Launch At
                                </label>
                                <vue-date-picker v-model="inputEvent.event_launch_at"></vue-date-picker>
                                <span v-if="errors.event_launch_at" class="text-red-500 text-xs">{{ errors.event_launch_at[0] }}</span>
                            </div>
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Ticket Purchasing At
                                </label>
                                <vue-date-picker v-model="inputEvent.ticket_purchasing_at"></vue-date-picker>
                                <span v-if="errors.ticket_purchasing_at" class="text-red-500 text-xs">{{ errors.ticket_purchasing_at[0] }}</span>
                            </div>
                        </div>
                        <div class="-mx-3 md:flex mb-6">
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Location Name
                                </label>
                                <input class="w-full bg-gray-200 text-black border border-gray-200 rounded px-3 py-[.4rem]" v-model="inputEvent.location_name" />
                                <span v-if="errors.location_name" class="text-red-500 text-xs">{{ errors.location_name[0] }}</span>
                            </div>
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Location Address
                                </label>
                                <input class="w-full bg-gray-200 text-black border border-gray-200 rounded px-3 py-[.4rem]" v-model="inputEvent.location_address" />
                                <span v-if="errors.location_address" class="text-red-500 text-xs">{{ errors.location_address[0] }}</span>
                            </div>
                        </div>
                        <div class="-mx-3 md:flex mb-6">
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Event Date
                                </label>
                                <vue-date-picker v-model="inputEvent.event_date"></vue-date-picker>
                                <span v-if="errors.event_date" class="text-red-500 text-xs">{{ errors.event_date[0] }}</span>
                            </div>
                            <div class="md:w-1/2 px-3 mb-6 md:mb-0">
                                <label class="uppercase tracking-wide text-black text-xs font-bold mb-2" for="company">
                                    Start - End Date
                                </label>
                                <vue-date-picker range :multi-calendars="{ solo: true }" v-model="inputEvent.start_end_date"></vue-date-picker>
                                <span v-if="errors.start_end_date" class="text-red-500 text-xs">{{ errors.start_end_date[0] }}</span>
                            </div>
                        </div>
                        <div class="-mx-3 flex justify-end mt-2">
                            <div class="px-3">
                                <router-link class="btn btn-danger text-white font-bold mr-1 py-2 px-4" :to="{
                                    name: 'Event'
                                }"><i class="ki-filled ki-arrow-left mr-1"></i>Back</router-link>
                                <button type="submit" class=" btn btn-primary text-white font-bold py-2 px-4">
                                    <i class="ki-filled ki-plus mr-1"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from "vue";
import Multiselect from 'vue-multiselect';
import { useEventStore } from "../../stores/Event";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";

const router = useRouter();

// Reactive state
const value = ref(null);
const options = ref([]);

// Input Account
const inputEvent = ref({
    name: "",
    organizer_id: 0,
    description: "",
    event_launch_at: "",
    ticket_purchasing_at: "",
    location_name: "",
    location_address: "",
    event_date: "",
    event_start: "",
    event_end: "",
    schedules: "",
    is_active: true,
    start_end_date: null
});

// Handle Submit & Errors
const errors = ref({
    name: "",
    organizer_id: 0,
    description: "",
    event_launch_at: "",
    ticket_purchasing_at: "",
    location_name: "",
    location_address: "",
    event_date: "",
    event_start: "",
    event_end: "",
    schedules: "",
    is_active: true,
    start_end_date: null
});


const selectedOption = ref(null);

// Get Accounts

const eventStore = useEventStore();
const getAccounts = async () => {
    // Convert become query params
    let queryParams = `&category=${inputEvent.value.category}`;

    // Get Accounts
    await eventStore.getAccounts(queryParams);
    options.value = eventStore.accounts;
};

const handleSubmit = async () => {
    try {
        const store = await eventStore.createAccount(inputEvent.value);

        // Check if success
        if (store) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Account has been created successfully!',
            }).then(()=>{
                // Return to Accounts Page
                router.push({ name: "Accounts" });
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            });
        }

    } catch (error) {
        if (error.response && error.response.data && error.response.data.result) {
            errors.value = error.response.data.result;
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
            });
        }
    }
};

// Trigger Change Value Category
const handleChangeCategory = async () => {
    await getAccounts();
    selectedOption.value = null;
    inputEvent.value.account = "";
};

const updateCode = (option) => {
    inputEvent.value.account = option ? option.account : "";
};

</script>
