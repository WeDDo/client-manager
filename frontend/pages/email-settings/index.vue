<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useEmailSettingStore} from "~/stores/modules/emailSetting.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";

const { public: { baseURL } } = useRuntimeConfig();

const router = useRouter();
const store = useEmailSettingStore();
const mainStore = useMainStore();
const toast = useToast();

const token = useCookie('token');

const dataTableData = ref();
const mainDataTableRef = ref();
const confirmDeleteDialogRef = ref();

const fetchHelper = useFetchHelper();

const { data, pending, error, refresh } = await useFetch(`${baseURL}/${store.apiRouteName}`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    dataTableData.value = data.value;
} else {
    fetchHelper.handleUseFetchError(error);
}

function deleteTextTemplate(item) {
    return `the item ID: ${item.id}`;
}

async function handleCopy() {
    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${mainDataTableRef.value.selection.id}/copy`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            console.log('response', response)
            if (response.ok) {
                toast.add({ severity: 'success', summary: 'Copied successfully', life: 2000 });
                dataTableData.value.items.push(response._data.additional?.data_table_item ?? response._data.item);
                mainDataTableRef.value.selection = response._data.additional?.data_table_item ?? response._data.item;
            } else {
                fetchHelper.handleResponseError(response);
            }
            mainStore.actionLoading = false;
        },
    });
}

</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    Email settings
                </div>
                <div>
                    <Button
                        label="Add"
                        size="small"
                        icon="pi pi-plus"
                        class="mr-2"
                        @click="() => router.push(`/email-settings/create`)"
                    />
                    <Button
                        label="Edit"
                        size="small"
                        icon="pi pi-pencil"
                        class="mr-2"
                        :disabled="!mainDataTableRef?.selection"
                        @click="() => router.push(`/email-settings/${mainDataTableRef.selection.id}`)"
                    />
                    <Button
                        label="Delete"
                        size="small"
                        icon="pi pi-trash"
                        class="mr-2"
                        :disabled="!mainDataTableRef?.selection"
                        @click="mainDataTableRef.confirmDeleteDialogRef.visible = true"
                    />
                    <Button
                        label="Copy"
                        size="small"
                        icon="pi pi-copy"
                        class="mr-2"
                        :disabled="!mainDataTableRef?.selection"
                        @click="handleCopy"
                    />
                    <Button
                        label="Back"
                        size="small"
                        @click="() => router.push('/')"
                    />
                </div>
            </div>
            <div class="mt-2">
                <MainDataTable
                    ref="mainDataTableRef"
                    v-model:data="dataTableData"
                    v-model:store="store"
                    :delete-text-template="deleteTextTemplate"
                >
                </MainDataTable>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
