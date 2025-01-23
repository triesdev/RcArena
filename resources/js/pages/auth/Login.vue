<template>
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <!--begin::Aside-->
        <div class="d-flex flex-lg-row-fluid">

        </div>
        <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
            <div class="bg-body d-flex flex-center rounded-4 w-md-600px p-10">
                <div class="w-md-400px">
                    <div class="text-center mb-11">
                        <h1 class="text-dark fw-bolder mb-3">Sign In</h1>
                    </div>
                    <div class="separator separator-content my-14">
                        <span class="w-125px text-gray-500 fw-semibold fs-7">With Account</span>
                    </div>
                    <form>
                        <div class="fv-row mb-8">
                            <input type="text" placeholder="username" autocomplete="new-password"
                                v-model="credential.email" class="form-control bg-transparent" />
                        </div>

                        <div class="fv-row mb-3">
                            <input type="password" placeholder="Password" name="password" autocomplete="new-password"
                                v-model="credential.password" @keyup.enter="doLogin"
                                class="form-control bg-transparent" />
                        </div>
                    </form>
                    <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                        <div></div>
                        <router-link to="/auth/forgot-password" class="link-primary">Forgot Password ?
                        </router-link>
                    </div>
                    <div class="d-grid mb-10">
                        <button @click="doLogin" id="kt_sign_in_submit" class="btn btn-primary">
                            <span v-if="!credential.is_loading">Sign In</span>
                            <span v-if="credential.is_loading">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { reactive } from 'vue'
import useAxios from '../../src/service'

export default {
    setup() {
        const credential = reactive({
            email: '',
            password: '',
            is_loading: false,
        })

        const { postData } = useAxios();

        function doLogin() {
            credential.is_loading = true
            postData('login', {
                email: credential.email,
                password: credential.password
            }).then((data) => {
                credential.is_loading = false
                if (data.success) {
                    localStorage.setItem('user_token', data.result.token)
                    localStorage.setItem('user_id', data.result.user.id)
                    window.location = '/panel/dashboard'
                } else {
                    alert('Email atau password salah!')
                }
            })
        }

        return {
            credential,
            doLogin
        }
    }
}

</script>
