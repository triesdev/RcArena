<template>
    <div class="w-full h-full flex items-center justify-center">
        <div class="w-full max-w-[600px]">
            <div class="flex justify-center mb-5">
                <img src="/public/assets/logo.jpg" alt="logo" class="h-8 w-auto" />
            </div>
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold mb-4 text-gray-800">Daftar</h1>
            </div>
            <form @submit="handleSubmit">
                <div class="mb-5">
                    <label for="email" class="text-sm font-medium text-gray-700 block">
                        Nama
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Nama"
                        class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm input"
                    />
                </div>
                <div class="mb-5">
                    <label for="email" class="text-sm font-medium text-gray-700 block">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Email"
                        class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm input"
                    />
                </div>
                <div class="mb-5">
                    <label for="password" class="text-sm font-medium text-gray-700 block">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            placeholder="Password"
                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm input pr-10"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500"
                            @click="togglePassword"
                        >
                            <i v-if="showPassword" class="ki-filled ki-eye"></i>
                            <i v-else class="ki-filled ki-eye-slash"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-5">
                    <button
                        type="submit"
                        class="w-full py-3 bg-indigo-500 text-white rounded-md hover:bg-indigo-600"
                    >
                        Daftar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { showAlert } from "../../utils/sweetalert";
import { useAuthStore } from "../../stores/Auth";

const router = useRouter();
const showPassword = ref(false);

const handleSubmit = async (e: Event) => {
    e.preventDefault();
    const form = new FormData(e.target as HTMLFormElement);
    const name = form.get("name") as string;
    const email = form.get("email") as string;
    const password = form.get("password") as string;

    try {
        await useAuthStore().register({ name, email, password });
        showAlert("success", "Registrasi berhasil, silahkan login.");
        await router.push({ name: "login" });
    } catch (error) {
        showAlert("error", error.response.data.message);
    }
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

</script>
