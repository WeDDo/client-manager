<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import { useEmailMessageStore } from "~/stores/modules/emailMessage.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {useEmailInboxSettingStore} from "~/stores/modules/emailInboxSetting.js";

const { public: { baseURL } } = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const mainStore = useMainStore();
const toast = useToast();

const token = useCookie('token');

const dataTableData = ref();
const store = useEmailMessageStore();
const emailInboxSettingStore = useEmailInboxSettingStore();
const mainDataTableRef = ref();

const fetchHelper = useFetchHelper();

const { data, status, error, refresh } = await useFetch(`${baseURL}/${store.apiRouteName}${store.selectedFolder ? `?selected_folder=${store.selectedFolder}` : ''}&page=1`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    dataTableData.value = data.value;
} else {
    fetchHelper.handleUseFetchError(error);
}

watch(data, () => {
    dataTableData.value = data.value;
});

async function fetchEmails() {
    await $fetch(`${baseURL}/${store.apiRouteName}?selected_folder=${store.selectedFolder}`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({ response }) {
            if (response.ok) {
                dataTableData.value = response._data;
            } else {
                fetchHelper.handleResponseError(response);
            }
            mainStore.actionLoading = false;
        },
    });
}

watch(() => store.selectedFolder, fetchEmails);

function changeFolder() {
    const inboxSettings = dataTableData.value?.additional_data?.email_inbox_settings ?? [];
    const currentFolderIndex = inboxSettings.indexOf(store.selectedFolder);

    if (currentFolderIndex >= 0 && inboxSettings.length > 0) {
        const nextIndex = (currentFolderIndex + 1) % inboxSettings.length;
        store.selectedFolder = inboxSettings[nextIndex];
    } else if (inboxSettings.length > 0) {
        store.selectedFolder = inboxSettings[0];
    }
}

async function handleGetEmails() {
    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/get-emails-using-imap`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({ response }) {
            if (response.ok) {
                fetchEmails();
                toast.add({ severity: 'success', summary: 'Emails created successfully', life: 2000 });
            } else {
                fetchHelper.handleResponseError(response);
            }
            mainStore.actionLoading = false;
        },
    });
}

async function handleGetDataTableData(event) {
    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}?selected_folder=${store.selectedFolder}&page=${event.page + 1}`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                dataTableData.value = response._data;
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
                    {{ store.multiName }} ({{ store.selectedFolder }})
                </div>
                <div>
                    <Button
                        label="Change Inbox"
                        size="small"
                        icon="pi pi-envelope"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        @click="changeFolder"
                    />
                    <Button
                        label="Get emails"
                        size="small"
                        icon="pi pi-inbox"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        :disabled="dataTableData?.additional_data?.email_inbox_settings?.length === 0"
                        :loading="mainStore.actionLoading"
                        @click="handleGetEmails"
                    />
                    <Button
                        label="Inbox settings"
                        size="small"
                        icon="pi pi-sliders-h"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        :disabled="mainStore.actionLoading"
                        @click="() => router.push(`/${emailInboxSettingStore.frontRouteName}`)"
                    />
                    <Button
                        label="Edit"
                        size="small"
                        icon="pi pi-pencil"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        :disabled="!mainDataTableRef?.selection"
                        @click="() => router.push(`/${store.frontRouteName}/${mainDataTableRef.selection.id}`)"
                    />
                    <Button
                        size="small"
                        icon="pi pi-trash"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        :disabled="!mainDataTableRef?.selection"
                        @click="mainDataTableRef.confirmDeleteDialogRef.visible = true"
                    />
                    <Button
                        icon="pi pi-times"
                        size="small"
                        severity="contrast"
                        text
                        raised
                        @click="() => router.push('/')"
                    />
                </div>
            </div>
            <div class="mt-2">
                <MainDataTable
                    ref="mainDataTableRef"
                    v-model:data="dataTableData"
                    v-model:store="store"
                    paginate
                    @page="handleGetDataTableData"
                >
                    <template #subject="slotProps">
                        <div class="flex justify-content-between">
                            <div>
                                {{ slotProps.data.subject }}
                            </div>
                            <div v-tooltip.bottom="'Unread count'">
                                ({{ slotProps.data.unread_count }})
                            </div>
                        </div>
                    </template>
                    <template #is_flagged="slotProps">
                        <div class="flex align-items-center">
                            <i :class="`pi ${slotProps.data.is_flagged ? 'pi-check-square' : 'pi-stop'}`" />
                        </div>
                    </template>
                    <template #is_answered="slotProps">
                        <div class="flex align-items-center">
                            <i :class="`pi ${slotProps.data.is_answered ? 'pi-check-square' : 'pi-stop'}`" />
                        </div>
                    </template>
                </MainDataTable>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
