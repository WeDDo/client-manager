<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useEmailMessageStore} from "~/stores/modules/emailMessage.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const mainStore = useMainStore();
const toast = useToast();

const token = useCookie('token');

const dataTableData = ref();
const store = useEmailMessageStore();
const mainDataTableRef = ref();
const confirmDeleteDialogRef = ref();


const fetchHelper = useFetchHelper();

const { data, status, error, refresh } = await useFetch(`${baseURL}/${store.apiRouteName}${store.selectedFolder ? `?selected_folder=${store.selectedFolder}` : ''}`, {
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

watch(() => store.selectedFolder, fetchEmails);

function changeFolder() {
    store.selectedFolder = store.selectedFolder === 'INBOX' ? 'SENT' : 'INBOX';
}

async function handleGetEmails() {
    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/get-emails-using-imap`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
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

</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    Email messages ({{ store.selectedFolder }})
                </div>
                <div>
                    <Button
                        label="Change Folder"
                        size="small"
                        icon="pi pi-envelope"
                        class="mr-2"
                        @click="changeFolder"
                    />
                    <Button
                        label="Get emails"
                        size="small"
                        icon="pi pi-inbox"
                        class="mr-2"
                        @click="handleGetEmails"
                    />
                    <Button
                        label="Edit"
                        size="small"
                        icon="pi pi-pencil"
                        class="mr-2"
                        :disabled="!mainDataTableRef?.selection"
                        @click="() => router.push(`/${store.frontRouteName}/${mainDataTableRef.selection.id}`)"
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
                >
                    <template #is_seen="slotProps">
                        <div class="flex align-items-center">
                            <i :class="`pi ${slotProps.data.is_seen ? 'pi-check-square' : 'pi-stop'}`" />
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
