<script setup>
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";
import MainDateInput from "~/components/v1/MainDateInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";

const {public: {baseURL}} = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({});

const mainStore = useMainStore();

const emit = defineEmits([
    'refresh',
]);
const visible = ref(false);

// const columns = ref([]);
const selectedColumns = ref([
    [
        {id: 1, name: '1'},
        {id: 2, name: '2'},
    ],
    []
]);

const data = defineModel('data');

defineExpose({visible});

function handleClickColumnSelect() {
    visible.value = !visible.value;
}

watch(data, () => {
    selectedColumns.value = data.value.selectable_columns;
    console.log('data', data.value.selectable_columns)
}, {immediate: true});
//
// watch(filterData, () => {
//     data.value.filters = filterData.value;
// });
//
// onMounted(() => {
//     filterData.value = data.value?.filters ? JSON.parse(JSON.stringify(data.value.filters)) : [];
// });

// const areFiltersEmpty = computed(() => {
//     return filterData.value.every(filter => (!filter.value || filter.value === ''));
// });

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
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <template #header>
                <div class="text-lg font-medium">
                    Column select
                </div>
            </template>
            <div>
                <PickList v-model="selectedColumns" listStyle="height:342px" dataKey="id" breakpoint="1400px">
                    <template #sourceheader> Available </template>
                    <template #targetheader> Selected </template>
                    <template #item="slotProps">
                        <div>
                            {{slotProps.item.name}}
                        </div>
                    </template>
                </PickList>

                <Button
                    label="Save"
                    size="small"
                    icon="pi pi-save"
                    class="w-full mt-2"
                    severity="primary"
                    @click="emit('refresh')"
                />
            </div>
        </Dialog>
    </div>
</template>

<style scoped>

</style>
