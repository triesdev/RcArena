<template>
  <div class="flex grow">
    <Sidebar />
    <div class="wrapper flex grow flex-col">
      <Header />
      <main class="grow content pt-5" id="content" role="content">
        <div class="container-fixed">
          <router-view />
        </div>
      </main>
      <Footer />
    </div>
  </div>
  <SearchModal />
</template>
<script lang="ts" setup>
import Sidebar from "../../components/Sidebar.vue";
import Header from "../../components/Header.vue";
import Footer from "../../components/Footer.vue";
import SearchModal from "../../components/SearchModal.vue";
import { nextTick, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
// import KTComponent from "../../../metronic/core/index";
import KTLayout from "../../../metronic/app/layouts/demo1.js";

const route = useRoute();

const initializeLayout = () => {
    // Ensure KTComponent and KTLayout are initialized after DOM is ready
    if (document.querySelector("#sidebar")) {
        KTLayout.init();
    } else {
        console.warn("Sidebar not found! Delaying initialization.");
        setTimeout(initializeLayout, 50); // Retry after 50ms
    }
};

onMounted(() => {
    nextTick(() => {
        initializeLayout();
    });
});

// Reinitialize layout on route change (optional, if required)
watch(route, () => {
    nextTick(() => {
        initializeLayout();
    });
});
</script>
