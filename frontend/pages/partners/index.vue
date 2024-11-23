<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {usePartnerStore} from "~/stores/modules/partner.js";
import ActionButtonsButton from "~/components/v1/ActionButtonsButton.vue";

const { public: { baseURL } } = useRuntimeConfig();

const route = useRoute();
const router = useRouter();

const store = usePartnerStore();
const mainStore = useMainStore();
const loadingStore = useLoadingStore();

const toast = useToast();

const token = useCookie('token');

const dataTableData = ref();
const mainDataTableRef = ref();

const fetchHelper = useFetchHelper();

const { data, status, error, refresh } = await useFetch(`${baseURL}/${store.apiRouteName}?page=1`, {
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

async function handleCopy() {
    loadingStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${mainDataTableRef.value.selection.id}/copy`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({ severity: 'success', summary: 'Copied successfully', life: 2000 });
                dataTableData.value.items.push(response._data.item);
                mainDataTableRef.value.selection = response._data.item;
            } else {
                fetchHelper.handleResponseError(response);
            }
            loadingStore.actionLoading = false;
        },
    });
}

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
                            toast.add({ severity: 'warn', summary: 'No Selection', detail: 'Please select an item to edit.', life: 3000 });
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
                            toast.add({ severity: 'warn', summary: 'No Selection', detail: 'Please select an item to delete.', life: 3000 });
                        }
                    }
                },
            ]
        }
    ]
});
</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>{{ store.multiName }}</div>
                <div class="flex justify-content-center">
                    <ActionButtonsButton
                        class="mr-2"
                        :actions="actions"
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
.arrow {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-color, black);
    padding: 0.5rem;
}

.arrow:hover {
    color: var(--hover-color, gray);
}
</style>
