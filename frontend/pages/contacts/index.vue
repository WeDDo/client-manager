<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {useContactStore} from "~/stores/modules/contact.js";

const { public: { baseURL } } = useRuntimeConfig();

const router = useRouter();
const route = useRoute();

const store = useContactStore();
const mainStore = useMainStore();
const loadingStore = useLoadingStore();

const toast = useToast();

const token = useCookie('token');

const dataTableData = ref();
const mainDataTableRef = ref();

const fetchHelper = useFetchHelper();

const { data, status, error, refresh } = await useFetch(fetchHelper.getDataTableUrl(`${baseURL}/${store.apiRouteName}`, {page: 0}), {
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
                mainStore.setPage(route.path, event.page + 1);
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

</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    {{ store.multiName }}
                </div>

                <div>
                    <Button
                        label="Add"
                        size="small"
                        icon="pi pi-plus"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        @click="() => router.push(`/${store.frontRouteName}/create`)"
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
                    :delete-text-template="deleteTextTemplate"
                    @refresh="handleGetDataTableData"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
