<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";

const {public: {baseURL}} = useRuntimeConfig();

// const { data, pending, error, refresh } = await useFetch(`${baseURL}/${homeStore.apiRouteName}?date=${budgetDate.value}`, {
//   headers: {
//     authorization: `Bearer ${token.value}`
//   },
// });

</script>

<template>
    <div>
        <MainMenuBar />
        <div>
            <div>

            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
