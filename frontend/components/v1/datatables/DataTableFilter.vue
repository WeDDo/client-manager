<script setup>
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";

const {public: {baseURL}} = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({});

const emit = defineEmits([]);
const visible = ref(false);

const data = defineModel('data');

defineExpose({visible});

function handleFilterClick() {
    visible.value = !visible.value;
}
</script>

<template>
    <div>
        <Button
            size="small"
            icon="pi pi-filter"
            severity="secondary"
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
                <div v-for="(filter, index) in data?.filters ?? []" :key="index">
                    <div class="formgrid grid">
                        <div class="col-6 md:col-10">
                            <MainTextInput
                                v-model:value="filter.value"
                                :name="filter.name"
                                :label="filter.label"
                            />
                            {{filter.value}}
                        </div>
                        <div class="col-6 md:col-2">
                            <MainSelectInput
                                v-model:value="filter.operator"
                                name="operator"
                                label="Operator"
                                :options="['=', '<', '>', 'like', '<=', '>=']"
                                simple-options
                            />
                        </div>
                    </div>
                </div>

                <Button
                    label="Filter"
                    size="small"
                    icon="pi pi-filter"
                    class="mr-2 w-full"
                    severity="secondary"
                    @click="emit('refresh')"
                />
            </div>
        </Dialog>
    </div>
</template>

<style scoped>

</style>
