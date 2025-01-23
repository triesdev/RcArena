import {defineStore} from "pinia";
import axios from "axios";
import {publicAxios} from "../utils/axios";

export const useAuthStore = defineStore('auth',{
    state: () => ({
        isAuthenticated: false,
        user: null as { id: number; name: string; email: string } | null,
        token: null as string | null,
        userRegisterInput: null as { id: number; name: string; email: string } | null,
    }),
    actions: {
        mappingLoginStore(user: any, token: string){
            this.user = user;
            this.token = token;
            this.isAuthenticated = true;
        },
        logout(){
            this.user = null;
            this.token = null;
            this.isAuthenticated = false;
        },
        register(userRegisterInput: any) {
            this.userRegisterInput = userRegisterInput;
            return publicAxios.post('/register', this.userRegisterInput)
        }
    }
})
