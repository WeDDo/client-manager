<script setup>
import ConfirmDeleteDialog from "~/components/v1/ConfirmDeleteDialog.vue";
import DataTableFilter from "~/components/v1/datatables/DataTableFilter.vue";
import DataTableSort from "~/components/v1/datatables/DataTableSort.vue";

const {public: {baseURL}} = useRuntimeConfig();

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
        default: 'calc(100vh - 15rem)',
    },
    routeName: {
        type: String,
        default: null,
    },
    customRowDblClick: {
        type: Boolean,
        default: false,
    },
    paginate: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits([
    'row-dblclick',
    'item-deleted',
    'page',
]);

const store = defineModel('store');
const value = defineModel('value');
const data = defineModel('data');

const dataTableRef = ref();
const selection = ref(store?.value?.lastSelection);
const filters = ref({global: {value: null},});
const confirmDeleteDialogRef = ref();

const sortField = ref();
const sortOrder = ref();
const sortLoading = ref(false);

defineExpose({confirmDeleteDialogRef, selection, store});

function handleDataTableClick(event) {
    if (store.value) {
        store.value.lastSelection = event.data;
    }
}

function handleDataTableDoubleClick(event) {
    store.value.lastSelection = event.data;
    router.push(`/${store.value.frontRouteName}/${event.data.id}`)
}

function handleAfterDelete(id) {
    data.value.items.data = data.value.items.data.filter(item => item.id !== id);
    selection.value = null;
    emit('item-deleted', store.value.lastSelection);
}

onMounted(() => {
    const dataTableWrapper = dataTableRef.value.$el.querySelector('.p-datatable-wrapper');
    if (dataTableWrapper) {
        dataTableWrapper.style.height = props.scrollHeight;
    }
});

function getRefreshEventData(page = 0) {
    return {
        page,
        sort_field: sortField.value,
        sort_order: sortOrder.value,
        filters: data?.value?.filters,
    };
}

function toggleSort(field) {
    sortLoading.value = true;

    if (sortField.value === field) {
        if (sortOrder.value === 'asc') {
            sortOrder.value = 'desc';
        } else if (sortOrder.value === 'desc') {
            sortOrder.value = null;
            sortField.value = null;
        } else {
            sortOrder.value = 'asc';
            sortField.value = field;
        }
    } else {
        sortField.value = field;
        sortOrder.value = 'asc';
    }

    emit('refresh', getRefreshEventData(0));

    sortLoading.value = false;
}

function getSortIconClass(columnName) {
    if (sortField.value === columnName) {
        if (sortOrder.value === 'asc') {
            return 'pi pi-sort-amount-up-alt';
        } else if (sortOrder.value === 'desc') {
            return 'pi pi-sort-amount-down-alt';
        }
    }
    return 'pi pi-sort-alt';
}
</script>

<template>
    <div>
        <DataTable
            ref="dataTableRef"
            v-model:selection="selection"
            v-model:filters="filters"
            :value="data?.items.data ?? []"
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
                                <i class="pi pi-search"/>
                            </InputIcon>
                            <InputText
                                v-model="filters['global'].value"
                                placeholder="Search"
                                size="small"
                                class="w-12 sm:w-auto"
                            />
                        </IconField>
                    </div>
                    <div v-if="props.header"
                         class="text-xl text-900 font-bold flex justify-content-center align-items-center">
                        {{ props.header }}
                    </div>

                    <div class="flex justify-content-between w-full">
                        <div class="flex">
                            <DataTableFilter
                                v-if="data?.filters"
                                v-model:data="data"
                                class="mx-2"
                                @refresh="emit('refresh', getRefreshEventData(0))"                            />
                        </div>
                        <div>
                            <slot name="buttons"/>
                        </div>
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
                        class="pr-4 w-full text-sm cursor-pointer"
                        :class="{
                            'text-right': column.align === 'right',
                            'pr-4': column.align === 'right',
                        }"
                        :style="{
                            minWidth: column.min_width ? column.min_width + 'px' : undefined,
                            maxWidth: column.max_width ? column.max_width + 'px' : undefined
                           }"
                        @click="toggleSort(column.name)"
                    >
                        <div class="flex justify-content-between">
                            <div>
                                {{ column.header }}
                            </div>
                            <div>
                                <i
                                    :class="getSortIconClass(column.name)"
                                    class="ml-1"
                                    style="font-size: 0.85em"
                                />
                            </div>
                        </div>
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
            </template>
        </DataTable>

        <div
            v-if="paginate"
            class="flex justify-content-end"
        >
            <Paginator
                :rows="data?.items?.per_page ?? 0"
                :total-records="data?.items?.total ?? 0"
                :first="((data?.items?.current_page ?? 0) - 1) * (data?.items?.per_page ?? 0)"
                @page="emit('refresh', $event)"
            >
                <template #start="slotProps">
                    <div class="text-sm">
                        <div v-if="(data?.items?.total ?? 0) > 0">
                            {{
                                slotProps.state.first + 1
                            }}-{{ slotProps.state.first + Math.min(slotProps.state.rows, data.items.total) }} of
                            {{ data.items.total }}
                        </div>
                        <div v-else>
                            No entries
                        </div>
                    </div>
                </template>
            </Paginator>
        </div>

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
