import { defineStore } from 'pinia'
import { reactive } from "vue";

export const useFilterStore = defineStore('filter', () => {
    const app_store = reactive({
        role_id: null,
    })

    const date_config = reactive({
        'range': true,
        'no-shortcuts': true,
        'no-label': true,
        'no': true,
        'formatted': 'll',
        'locale': "id",
        'only-date': true,
        'input-size': 'sm',
    })

    const category_store = reactive({
        page: 1,
        type: '',
    })

    const transaction_store = reactive({
        page: 1,
        per_page: 25,
        user_name: '',
        section: 'carvep',
        status: '',
        dates: '',
    })

    const presence_store = reactive({
        event_id: 1,
        page: 1,
        per_page: 50,
        name: '',
        align: 'center',
    })

    const post_store = reactive({
        page: 1,
        per_page: 25,
        title: '',
        year: '2024',
        status: '',
        category: '',
    })

    const event_store = reactive({
        page: 1,
        per_page: 25,
        name: '',
    })

    const user_store = reactive({
        page: 1,
        per_page: 25,
        role: 'user',
        name: '',
    })

    const role_store = reactive({
        page: 1
    })

    const config_ctk = reactive({
        range: true,
        'only-date': true,
        'no-shortcuts': true,
        format: "YYYY-MM-DD",
        formatted: "ll"
    })

    return {
        role_store,
        category_store,
        app_store,
        config_ctk,
        transaction_store,
        event_store,
        user_store,
        date_config,
        presence_store,
        post_store
    }
})
