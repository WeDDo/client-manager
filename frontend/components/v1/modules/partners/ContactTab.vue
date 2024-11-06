<script setup>
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {useContactStore} from "~/stores/modules/contact.js";
import {usePartnerStore} from "~/stores/modules/partner.js";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const token = useCookie('token');

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
    dataTableData.value = form.value.values.additional.contacts_data_table;
    mainStore.getPage(route.path);
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
                <Button
                    label="Add"
                    size="small"
                    icon="pi pi-plus"
                    severity="secondary"
                    text
                    @click="goToCreate"
                />
                <Button
                    label="Edit"
                    size="small"
                    class="mr-2"
                    icon="pi pi-pencil"
                    severity="secondary"
                    text
                    :disabled="!mainDataTableRef?.selection"
                    @click="() => router.push(`/${store.frontRouteName}/${mainDataTableRef.selection.id}`)"
                />
            </template>
        </MainDataTable>
    </div>
</template>

<style scoped>

</style>
