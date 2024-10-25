<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useProfileStore} from "~/stores/modules/profile.js";

const {public: {baseURL}} = useRuntimeConfig();

const store = useProfileStore();
</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    {{ store.singleName }}
                </div>
<!--                <div>-->
<!--                    <Button-->
<!--                        label="Add"-->
<!--                        size="small"-->
<!--                        icon="pi pi-plus"-->
<!--                        class="mr-2"-->
<!--                        severity="contrast"-->
<!--                        text-->
<!--                        raised-->
<!--                        @click="() => router.push(`/${store.frontRouteName}/create`)"-->
<!--                    />-->
<!--                    <Button-->
<!--                        label="Edit"-->
<!--                        size="small"-->
<!--                        icon="pi pi-pencil"-->
<!--                        class="mr-2"-->
<!--                        severity="contrast"-->
<!--                        text-->
<!--                        raised-->
<!--                        :disabled="!mainDataTableRef?.selection"-->
<!--                        @click="() => router.push(`/${store.frontRouteName}/${mainDataTableRef.selection.id}`)"-->
<!--                    />-->
<!--                    <Button-->
<!--                        size="small"-->
<!--                        icon="pi pi-trash"-->
<!--                        class="mr-2"-->
<!--                        severity="contrast"-->
<!--                        text-->
<!--                        raised-->
<!--                        :disabled="!mainDataTableRef?.selection"-->
<!--                        @click="mainDataTableRef.confirmDeleteDialogRef.visible = true"-->
<!--                    />-->
<!--                    <Button-->
<!--                        icon="pi pi-times"-->
<!--                        size="small"-->
<!--                        severity="contrast"-->
<!--                        text-->
<!--                        raised-->
<!--                        @click="() => router.push('/')"-->
<!--                    />-->
<!--                </div>-->
            </div>
            <div class="mt-2">
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
