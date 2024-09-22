<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";

const {public: {baseURL}} = useRuntimeConfig();
</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <Card>
                <template #title>
                    <div class="text-2xl">
                        <div>
                            Welcome!
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
