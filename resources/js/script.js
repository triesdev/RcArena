// import './bootstrap';
//
// import {createApp} from 'vue'
//
// import App from './App.vue'
//
// createApp(App).mount("#acc-app")

import "../css/app.css";
import { createApp } from "vue";
import App from "./App.vue";
import router from "./routes.ts";
import "./bootstrap";
import {createPinia} from "pinia";
const pinia = createPinia();
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const app = createApp(App);

app.component('vue-date-picker', VueDatePicker);

app.use(pinia);
app.use(router);

app.mount('#rocket-app');

export default app;
