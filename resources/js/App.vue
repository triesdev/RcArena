<template>
    <router-view />
</template>

<script lang="ts" setup>
import { nextTick, onMounted } from "vue";
import { useRoute } from "vue-router";
import KTComponent from "../metronic/core/index";

const route = useRoute();

const initializeLayout = () => {
    KTComponent.init();
};

onMounted(() => {
    nextTick(() => {
        initializeLayout();
    });
});
</script>
