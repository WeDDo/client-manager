<script setup>
const { public: { baseURL } } = useRuntimeConfig();

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
      console.log(response);
    },
  })
}

onMounted(() => {
  login();
})


</script>

<template>
  <div>
    Client manager
  </div>
</template>

<style scoped>

</style>
