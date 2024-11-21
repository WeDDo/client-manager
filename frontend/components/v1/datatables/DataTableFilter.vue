<script setup>
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";
import MainDateInput from "~/components/v1/MainDateInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";

const {public: {baseURL}} = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({
});

const mainStore = useMainStore();

const emit = defineEmits([
    'refresh',
]);
const visible = ref(false);

const filterData = ref([]);
const clearFilterLoading = ref(false);

const data = defineModel('data');

const fetchHelper = useFetchHelper();

defineExpose({visible});

function handleFilterClick() {
    visible.value = !visible.value;
}

watch(data, () => {
    filterData.value = data.value.filters;
});

watch(filterData, () => {
    data.value.filters = filterData.value;
});

onMounted(() => {
    filterData.value = data.value?.filters ? JSON.parse(JSON.stringify(data.value.filters)) : [];
});

const areFiltersEmpty = computed(() => {
    return filterData.value.every(filter => (!filter.value || filter.value === ''));
});

function filter() {
    emit('refresh', { update_filter: true });
}

async function clearFilter() {
    clearFilterLoading.value = true;

    await $fetch(`${baseURL}/data-tables/clear-filter`, {
        method: 'POST',
        body: {
            name: data.value.name,
        },
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            clearFilterLoading.value = false;
            if (response.ok) {
                emit('refresh', { update_filter: false });
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    })
}

</script>

<template>
    <div>
        <Button
            size="small"
            icon="pi pi-filter"
            :severity="areFiltersEmpty ? 'secondary' : 'primary'"
            text
            @click="handleFilterClick"
        />
        <Dialog
            v-model:visible="visible"
            :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <template #header>
                <div class="text-lg font-medium">
                    Filter
                </div>
            </template>
            <div>
                <div v-for="(filter, index) in filterData ?? []" :key="index">
                    <div class="formgrid grid">
                        <div class="col-6 md:col-10">
                            <MainTextInput
                                v-if="filter?.field_type === 'text'"
                                v-model:value="filter.value"
                                :name="filter.name"
                                :label="filter.label"
                            />

                            <MainDateInput
                                v-if="filter?.field_type === 'date'"
                                v-model:value="filter.value"
                                :name="filter.name"
                                :label="filter.label"
                            />

                            <MainSelectInput
                                v-if="filter?.field_type === 'bool'"
                                v-model:value="filter.value"
                                :name="filter.name"
                                :label="filter.label"
                                :options="['true', 'false']"
                                simple-options
                                show-clear
                            />
                        </div>
                        <div class="col-6 md:col-2">
                            <MainSelectInput
                                v-model:value="filter.operator"
                                name="operator"
                                label="Operator"
                                :options="['=', '<', '>', 'like', 'ilike', '<=', '>=']"
                                simple-options
                            />
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <Button
                        label="Clear"
                        size="small"
                        icon="pi pi-filter-slash"
                        class="w-full"
                        severity="secondary"
                        :loading="clearFilterLoading"
                        @click="clearFilter"
                    />
                    <Button
                        label="Filter"
                        size="small"
                        icon="pi pi-filter"
                        class="w-full"
                        severity="primary"
                        @click="filter"
                    />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<style scoped>

</style>
