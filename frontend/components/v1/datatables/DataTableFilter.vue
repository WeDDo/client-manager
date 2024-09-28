<script setup>
const { public: { baseURL } } = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({

});

const emit = defineEmits([]);
const visible = ref(false);

defineExpose({visible});

function handleFilterClick() {
    visible.value = !visible.value;
}

onMounted(() => {

});
</script>

<template>
    <div>
        <Button
            size="small"
            icon="pi pi-filter"
            severity="secondary"
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
                <div>

                </div>
            </div>
        </Dialog>
    </div>
</template>

<style scoped>

</style>
