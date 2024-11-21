<script setup>
import {useFetchHelper} from "~/composables/useFetchHelper.js";

const {public: {baseURL}} = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({});

const mainStore = useMainStore();

const emit = defineEmits([
    'refresh',
]);
const visible = ref(false);
const resetColumnsLoading = ref(false);

const fetchHelper = useFetchHelper();

const data = defineModel('data');

defineExpose({visible});

function handleClickColumnSelect() {
    visible.value = !visible.value;
}

async function updateColumns() {
    // clearFilterLoading.value = true;

    await $fetch(`${baseURL}/data-tables/update-active-columns`, {
        method: 'POST',
        body: {
            name: data.value.name,
            selected_columns: data.value.selected_columns,
        },
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            // clearFilterLoading.value = false;
            if (response.ok) {
                emit('refresh');
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    })
}

async function resetColumns() {
    resetColumnsLoading.value = true;

    await $fetch(`${baseURL}/data-tables/reset-columns`, {
        method: 'POST',
        body: {
            name: data.value.name,
        },
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            resetColumnsLoading.value = false;
            if (response.ok) {
                emit('refresh');
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
            icon="pi pi-objects-column"
            severity="secondary"
            text
            @click="handleClickColumnSelect"
        />

        <Dialog
            v-model:visible="visible"
            :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '80vw', '575px': '90vw' }"
        >
            <template #header>
                <div class="text-lg font-medium">
                    Column select
                </div>
            </template>
            <div v-if="data.selected_columns">
                <PickList
                    v-model="data.selected_columns"
                    listStyle="height:200px"
                    dataKey="id"
                >
                    <template #sourceheader>
                        Active
                    </template>
                    <template #targetheader>
                        Inactive
                    </template>
                    <template #item="slotProps">
                        <div>
                            {{slotProps.item.name}}
                        </div>
                    </template>
                </PickList>

                <div class="flex gap-2">
                    <Button
                        label="Reset"
                        size="small"
                        icon="pi pi-refresh"
                        class="w-full"
                        severity="secondary"
                        :loading="resetColumnsLoading"
                        @click="resetColumns"
                    />
                    <Button
                        label="Save"
                        size="small"
                        icon="pi pi-save"
                        class="w-full mt-2"
                        severity="primary"
                        @click="updateColumns"
                    />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<style scoped>

</style>
