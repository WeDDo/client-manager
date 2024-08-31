<script setup>
import MainMenuBar from "~/components/v1/modules/MainMenuBar.vue";

const {public: {baseURL}} = useRuntimeConfig();

// const { data, pending, error, refresh } = await useFetch(`${baseURL}/${homeStore.apiRouteName}?date=${budgetDate.value}`, {
//   headers: {
//     authorization: `Bearer ${token.value}`
//   },
// });

async function login() {
    await $fetch(`${baseURL}/login`, {
        method: 'POST',
        body: {
            email: 'jonas@gmail.com',
            password: '123456789'
        },
        onResponse({ response }) {
            console.log(response._data);
        },
    })
}

onMounted(() => {
    // login();
})


</script>

<template>
    <div>
        <MainMenuBar />
        <div>

        </div>
    </div>
</template>

<style scoped>

</style>
