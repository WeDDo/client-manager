<script setup>
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";
import {useContactStore} from "~/stores/modules/contact.js";

const {public: {baseURL}} = useRuntimeConfig();

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

// useInsideFormValidation(form.value.errors, emit, props.tab);
//
// const [idName] = form.value.defineField('item.id_name');
// const [name] = form.value.defineField('item.name');
// const [name2] = form.value.defineField('item.name2');
// const [legalStatus] = form.value.defineField('item.legal_status');
// const [email] = form.value.defineField('item.email');
// const [phone] = form.value.defineField('item.phone');
//
// const onSubmit = form.value.handleSubmit((values) => {
//     return true;
// });
//
// defineExpose({onSubmit});
</script>

<template>
    <div>
        <div>
            <MainDataTable
                ref="mainDataTableRef"
                v-model:data="form.values.additional.contacts_data_table"
                v-model:store="store"
                paginate
                scroll-height="calc(100vh - 18.3rem)"
            />
        </div>
    </div>
</template>

<style scoped>

</style>
