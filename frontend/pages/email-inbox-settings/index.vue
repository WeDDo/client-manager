<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {useEmailInboxSettingStore} from "~/stores/modules/emailInboxSetting.js";
import {useEmailMessageStore} from "~/stores/modules/emailMessage.js";
import EmailCreateInboxes from "~/components/v1/modules/emailInboxSettings/EmailCreateInboxes.vue";
import ActionButtonsButton from "~/components/v1/ActionButtonsButton.vue";

const {public: {baseURL}} = useRuntimeConfig();

const router = useRouter();

const store = useEmailInboxSettingStore();
const emailMessageStore = useEmailMessageStore();
const mainStore = useMainStore();
const loadingStore = useLoadingStore();

const toast = useToast();

const token = useCookie('token');

const dataTableData = ref();
const mainDataTableRef = ref();
const emailCreateInboxesRef = ref();

const fetchHelper = useFetchHelper();

const {data, status, error, refresh} = await useFetch(`${baseURL}/${store.apiRouteName}?page=1`, {
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

async function handleGetDataTableData(event) {
    loadingStore.actionLoading = true;

    await $fetch(fetchHelper.getDataTableUrl(`${baseURL}/${store.apiRouteName}`, event), {
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
            loadingStore.actionLoading = false;
        },
    });
}

// async function handleCopy() {
//     loadingStore.actionLoading = true;
//
//     await $fetch(`${baseURL}/${store.apiRouteName}/${mainDataTableRef.value.selection.id}/copy`, {
//         method: 'GET',
//         headers: {
//             authorization: `Bearer ${token.value}`
//         },
//         onResponse({response}) {
//             if (response.ok) {
//                 toast.add({ severity: 'success', summary: 'Copied successfully', life: 2000 });
//                 dataTableData.value.items.push(response._data.additional?.data_table_item ?? response._data.item);
//                 mainDataTableRef.value.selection = response._data.additional?.data_table_item ?? response._data.item;
//             } else {
//                 fetchHelper.handleResponseError(response);
//             }
//             loadingStore.actionLoading = false;
//         },
//     });
// }

const actions = computed(() => {
    return [
        {
            label: 'Actions',
            items: [
                {
                    label: 'Add',
                    icon: 'pi pi-plus',
                    command: () => router.push(`/${store.frontRouteName}/create`)
                },
                {
                    label: 'Edit',
                    icon: 'pi pi-pencil',
                    disabled: !mainDataTableRef?.value?.selection,
                    command: () => {
                        if (mainDataTableRef.value.selection) {
                            router.push(`/${store.frontRouteName}/${mainDataTableRef.value.selection.id}`);
                        } else {
                            toast.add({
                                severity: 'warn',
                                summary: 'No Selection',
                                detail: 'Please select an item to edit.',
                                life: 3000
                            });
                        }
                    }
                },
                {
                    label: 'Delete',
                    icon: 'pi pi-trash',
                    disabled: !mainDataTableRef?.value?.selection,
                    command: () => {
                        if (mainDataTableRef.value.selection) {
                            mainDataTableRef.value.confirmDeleteDialogRef.visible = true;
                        } else {
                            toast.add({
                                severity: 'warn',
                                summary: 'No Selection',
                                detail: 'Please select an item to delete.',
                                life: 3000
                            });
                        }
                    }
                },
                {
                    label: 'Get inboxes',
                    icon: 'pi pi-inbox',
                    command: () => emailCreateInboxesRef.value.handleClickGetInboxes()
                },
            ]
        }
    ]
});

</script>

<template>
    <div>
        <MainMenuBar/>
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    {{ store.singleName }}
                </div>
                <div class="flex justify-content-center">
                    <EmailCreateInboxes
                        ref="emailCreateInboxesRef"
                        class="mr-2"
                        @refresh-data-table="mainDataTableRef.refreshData()"
                    />
                    <ActionButtonsButton
                        class="mr-2"
                        :actions="actions"
                    />
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
                    <!--                        label="Copy"-->
                    <!--                        size="small"-->
                    <!--                        icon="pi pi-copy"-->
                    <!--                        class="mr-2"-->
                    <!--                        :disabled="!mainDataTableRef?.selection"-->
                    <!--                        @click="handleCopy"-->
                    <!--                    />-->
                    <Button
                        icon="pi pi-times"
                        size="small"
                        severity="contrast"
                        text
                        raised
                        @click="() => router.push(`/${emailMessageStore.frontRouteName}`)"
                    />
                </div>
            </div>
            <div class="mt-2">
                <MainDataTable
                    ref="mainDataTableRef"
                    v-model:data="dataTableData"
                    v-model:store="store"
                    paginate
                    :delete-text-template="deleteTextTemplate"
                    @refresh="handleGetDataTableData"
                >
                    <template #auto_set_is_seen="slotProps">
                        <div class="flex align-items-center">
                            <i :class="`pi ${slotProps.data.auto_set_is_seen ? 'pi-check-square' : 'pi-stop'}`"/>
                        </div>
                    </template>
                </MainDataTable>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
