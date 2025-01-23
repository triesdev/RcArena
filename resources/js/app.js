require('./bootstrap');

import { createApp } from 'vue'
import App from './App.vue'
import router from './src/router'
import VueSweetalert2 from 'vue-sweetalert2';
import { createPinia } from 'pinia'
import mitt from 'mitt';
import filter from './src/filter'
import registerIcon from "./src/oh_icon";
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';
import registerComponents from './src/components';
import CKEditor from '@ckeditor/ckeditor5-vue';

const app = createApp(App);
const pinia = createPinia()

app.use(VueSweetalert2)
window.Swal = app.config.globalProperties.$swal;

const emitter = mitt();
app.config.globalProperties.emitter = emitter;
window.emitter = emitter

app.config.globalProperties.$filter = filter

registerIcon(app)
registerComponents(app)

app.use(CKEditor)
app.use(pinia)
app.use(router)
    .mount('#app')
