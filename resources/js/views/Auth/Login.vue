<template>
  <div class="w-full h-full flex items-center justify-center">
    <div class="w-full max-w-[600px]">
      <div class="flex justify-center mb-5">
<!--        <img src="/public/assets/logo.jpg" alt="logo" class="h-8 w-auto" />-->
      </div>
      <div class="text-center mb-10">
        <h1 class="text-3xl font-bold mb-4 text-gray-800">Login</h1>
      </div>
      <form @submit="handleSubmit">
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
            Masuk
          </button>
        </div>
        <div class="text-center">
          <a href="#" class="text-indigo-500">Lupa Password?</a>
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
import {publicAxios, publicAxios as axios} from "../../utils/axios";

const router = useRouter();
const showPassword = ref(false);
const authStore = useAuthStore();

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const handleSubmit = async (e: Event) => {
  e.preventDefault();
  try {
      const login = await publicAxios.post("/login", {
          email: (e.target as HTMLFormElement).email.value,
          password: (e.target as HTMLFormElement).password.value,
      });

      const {token, user} = login.data.result;

      authStore.mappingLoginStore(user, token);

      localStorage.setItem("token", token);

      router.push("/adm/dashboard");
  } catch (error: any) {
      // Handle 401 Unauthorized error
      if (error.response && error.response.status === 401) {
        showAlert("Error !", "Email atau password salah",'error');
      } else {
          console.error("An unexpected error occurred:", error);
      }
  }

};

</script>
