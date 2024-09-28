<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";

const {public: {baseURL}} = useRuntimeConfig();

const token = useCookie('token');

const fetchHelper = useFetchHelper();

const {
    data,
    status,
    error,
    refresh
} = await useFetch(`${baseURL}/dashboard`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    // formHelper.setFormValues(data.value)
} else {
    fetchHelper.handleUseFetchError(error);
}
</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <Card>
                <template #title>
                    <div class="text-xl">
                        <div>
                            Welcome!
                        </div>
                    </div>
                </template>
            </Card>
        </div>
        <div class="m-2">
            <Card>
                <template #title>
                    <div class="text-xl">
                        <div>
                            You have {{ data.unread_email_count }} unread emails
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </div>
</template>

<style>
.p-card .p-card-body {
    gap: 0;
}
</style>
