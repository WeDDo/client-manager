<script setup>
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {useContactStore} from "~/stores/modules/contact.js";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const token = useCookie('token');

const store = useContactStore();

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
    console.log(form.value.values.item)
    store.setAdditionalFormData({ partner_id: form.value.values.item.id });
    // Object.assign(store.additionalFormData, { partner_id: form.value.values.item.partner_id });
    // store.additionalFormData = { partner_id: form.value.values.item.partner_id };
    router.push(`/${store.frontRouteName}/create`)
}
</script>

<template>
    <div>
        <MainDataTable
            ref="mainDataTableRef"
            v-model:data="form.values.additional.contacts_data_table"
            v-model:store="store"
            paginate
            scroll-height="calc(100vh - 18.3rem)"
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
