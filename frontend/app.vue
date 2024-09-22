<template>
    <div>
        <NuxtRouteAnnouncer/>
        <NuxtPage/>
        <Toast
            position="top-center"
            class="w-11 sm:w-auto"
        />
        <ConfirmDialog />
    </div>
</template>

<script setup>
import 'primevue/resources/themes/aura-light-green/theme.css'
import 'primeicons/primeicons.css'
import 'primeflex/primeflex.css'
import {useAuthHelper} from "~/composables/useAuthHelper.js";
import {useMainStore} from "~/stores/main.js";

const authHelper = useAuthHelper();

const mainStore = useMainStore();

onMounted(() => {
    mainStore.user = authHelper.getAuthUserFromLocalStorage();
});
</script>

<style>
.p-datatable .p-datatable-footer {
    border: none;
    border-top: 1px solid #e2e8f0;
}

html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
}
</style>
