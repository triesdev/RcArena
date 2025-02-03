import Loading from 'vue3-loading-overlay';
import { Bootstrap4Pagination } from 'laravel-vue-pagination'
import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';

const registerComponents = (app) => {
  app.component('Loading', Loading)
  app.component('Bootstrap4Pagination', Bootstrap4Pagination)
  app.component('VueCtkDateTimePicker', VueCtkDateTimePicker);
  app.component('vSelect', vSelect)
}

export default registerComponents
