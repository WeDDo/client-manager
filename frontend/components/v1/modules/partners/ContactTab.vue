<script setup>
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {useContactStore} from "~/stores/modules/contact.js";
import {usePartnerStore} from "~/stores/modules/partner.js";
import ActionButtonsButton from "~/components/v1/ActionButtonsButton.vue";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const token = useCookie('token');
const toast = useToast();

const mainStore = useMainStore();
const store = useContactStore();
const partnerStore = usePartnerStore();

const dataTableData = ref();
const mainDataTableRef = ref();

const fetchHelper = useFetchHelper();

const props = defineProps({
    tab: {
        type: Number,
        default: 0,
    }
})

const emit = defineEmits([
    'set-errors',
]);

const form = defineModel('form');

function goToCreate() {
    store.setAdditionalFormData({partner_id: form.value.values.item.id});
    router.push(`/${store.frontRouteName}/create`)
}

function goToEdit() {
    router.push(`/${store.frontRouteName}/${mainDataTableRef.value.selection.id}`)
}

async function handleGetDataTableData(event) {
    await $fetch(fetchHelper.getDataTableUrl(`${baseURL}/${partnerStore.apiRouteName}/${form.value.values.item.id}/${store.apiRouteName}`, event), {
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
        },
    });
}

onMounted(() => {
    dataTableData.value = JSON.parse(JSON.stringify(form.value.values.additional.contacts_data_table));
    mainStore.getPage(route.path);
});

const actions = computed(() => {
    return [
        {
            label: 'Actions',
            items: [
                {
                    label: 'Add',
                    icon: 'pi pi-plus',
                    command: goToCreate
                },
                {
                    label: 'Edit',
                    icon: 'pi pi-pencil',
                    disabled: !mainDataTableRef?.value?.selection,
                    command: goToEdit
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
                }
            ]
        }
    ]
});
</script>

<template>
    <div>
        <MainDataTable
            ref="mainDataTableRef"
            v-model:data="dataTableData"
            v-model:store="store"
            paginate
            scroll-height="calc(100vh - 18.3rem)"
            @refresh="handleGetDataTableData"
        >
            <template #buttons>
                <ActionButtonsButton
                    class="mr-2"
                    :actions="actions"
                />
            </template>
        </MainDataTable>
    </div>
</template>

<style scoped>

</style>
