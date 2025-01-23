import {defineStore} from "pinia";
import {authAxios} from "../utils/axios";

export const useEventStore = defineStore('account',{
    state: () => ({
        accounts: [],
        account: null,
        accountInput: {
            id: null,
        },
    }),
    actions: {
        getAccounts(queryParams){
            return authAxios.get('/accounts?pagination=false' + queryParams)
                .then(response => {
                    const {data} = response.data.result
                    this.accounts = data;
                })
        },
        getAccountById(id){
            return authAxios.get(`/accounts/${id}`)
                .then(response => {
                    const {result} = response.data
                    this.account = result;
                })
        },
        createAccount(accountInput){
            this.accountInput = accountInput;
            return authAxios.post('/accounts', this.accountInput)
        },
        updateAccount(accountInput){
            this.accountInput = accountInput;
            return authAxios.put(`/accounts/${this.accountInput.id}`, this.accountInput)
        },
        deleteAccount(id){
            return authAxios.delete(`/accounts/${id}`)
        }
    }
});
