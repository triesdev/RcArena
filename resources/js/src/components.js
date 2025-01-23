import Loading from 'vue3-loading-overlay';
import { Bootstrap4Pagination } from 'laravel-vue-pagination'
import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';

const registerComponents = (app) => {
  app.component('Loading', Loading)
  app.component('Bootstrap4Pagination', Bootstrap4Pagination)
  app.component('VueCtkDateTimePicker', VueCtkDateTimePicker);
}

export default registerComponents