<script setup>
import ConfirmDeleteDialog from "~/components/v1/ConfirmDeleteDialog.vue";
import DataTableFilter from "~/components/v1/datatables/DataTableFilter.vue";
import DataTableSort from "~/components/v1/datatables/DataTableSort.vue";

const { public: { baseURL } } = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({
    header: {
        type: String,
        default: null,
    },
    rowStyle: {
        type: Function,
        default: null,
    },
    deleteTextTemplate: {
        type: Function,
        default: function (item) {
            return `item ${item.id}`
        },
    },
    scrollHeight: {
        type: String,
        default: 'calc(100vh - 12rem)',
    },
    routeName: {
        type: String,
        default: null,
    },
    customRowDblClick: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits([
    'row-dblclick',
    'item-deleted',
]);

const store = defineModel('store');
const value = defineModel('value');
const data = defineModel('data');

const dataTableRef = ref();
const selection = ref(store?.value?.lastSelection);
const filters = ref({global: { value: null},});
const confirmDeleteDialogRef = ref();

// const confirmDeleteDialogRef = ref();

defineExpose({confirmDeleteDialogRef, selection, store});

function handleDataTableClick(event) {
    if(store.value) {
        store.value.lastSelection = event.data;
    }
}

function handleDataTableDoubleClick(event) {
    store.value.lastSelection = event.data;
    router.push(`/${store.value.frontRouteName}/${event.data.id}`)
}

function handleAfterDelete(id) {
    data.value.items = data.value.items.filter(item => item.id !== id);
    selection.value = null;
    emit('item-deleted', store.value.lastSelection);
}

onMounted(() => {
    const dataTableWrapper = dataTableRef.value.$el.querySelector('.p-datatable-wrapper');
    if (dataTableWrapper) {
        dataTableWrapper.style.height = props.scrollHeight;
    }
});
</script>

<template>
    <div>
        <DataTable
            ref="dataTableRef"
            v-model:selection="selection"
            v-model:filters="filters"
            :value="data?.items ?? []"
            selection-mode="single"
            scrollable
            data-key="id"
            size="small"
            :row-style="props.rowStyle"
            :scroll-height="props.scrollHeight"
            @row-click="handleDataTableClick($event)"
            @row-dblclick="props.customRowDblClick ? emit('row-dblclick', $event) : handleDataTableDoubleClick($event)"
        >
            <template #header>
                <div class="flex justify-content-between">
                    <div>
                        <IconField
                            icon-position="left"
                        >
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText
                                v-model="filters['global'].value"
                                placeholder="Search"
                                size="small"
                                class="w-12 sm:w-auto"
                            />
                        </IconField>
                    </div>
                    <div v-if="props.header" class="text-xl text-900 font-bold flex justify-content-center align-items-center">
                        {{ props.header }}
                    </div>

                    <div class="flex">
                        <DataTableFilter
                            class="mr-2"
                        />
                        <DataTableSort />
                    </div>
                </div>
            </template>


            <Column
                v-for="(column, index) in data?.active_columns ?? []"
                :key="index"
                :field="column.name"
            >
                <template #header>
                    <div
                        class="pr-4 w-full text-sm"
                        :class="{
                            'text-right': column.align === 'right',
                            'pr-4': column.align === 'right',
                        }"
                        :style="{
                            minWidth: column.min_width ? column.min_width + 'px' : undefined,
                            maxWidth: column.max_width ? column.max_width + 'px' : undefined
                        }"
                    >
                        {{ column.header }}
                    </div>
                </template>
                <template #body="slotProps">
                    <div
                        :class="{
                            'text-right': column.align === 'right',
                            'pr-4': column.align === 'right',
                        }"
                        class="text-sm"
                        :style="{
                            minWidth: column.min_width ? column.min_width + 'px' : undefined,
                            maxWidth: column.max_width ? column.max_width + 'px' : undefined
                        }"
                    >
                        <slot
                            :name="column.name"
                            :data="slotProps.data"
                        >
                            {{ slotProps.data?.[column.name] }}
                        </slot>
                    </div>
                </template>
            </Column>

            <template #footer>
                <div class="text-xs">
                    <slot name="footer" />
                    <div>
                        Showing {{ data?.items?.length ?? 0 }} {{ data.items_total_count ? `of ${data.items_total_count} entries` : '' }}
                    </div>
                </div>
            </template>
        </DataTable>

        <ConfirmDeleteDialog
            ref="confirmDeleteDialogRef"
            :route-name="props.routeName ?? store?.apiRouteName"
            :selection="selection"
            :selection-text-format="deleteTextTemplate"
            @set-deleted-id="handleAfterDelete($event)"
        />
    </div>
</template>

<style scoped>

</style>
