<script setup>

import {useFetchHelper} from "~/composables/useFetchHelper.js";

const {public: {baseURL}} = useRuntimeConfig();

const token = useCookie('token');

const props = defineProps({
    routeName: {
        type: String,
        default: null,
    },
    selection: {
        type: Object,
        default: null,
    },
    selectionTextFormat: {
        type: Function,
        default: null
    }
});

const emit = defineEmits([
    'set-deleted-id',
    'set-form-values'
]);

const toast = useToast();

const visible = ref(false);
const loading = ref(false);

defineExpose({visible});

const fetchHelper = useFetchHelper();

async function confirm() {
    loading.value = true;

    await $fetch(`${baseURL}/${props.routeName}/${props.selection.id}`, {
        method: 'DELETE',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({ response }) {
            if (response.ok) {
                toast.add({ severity: 'success', summary: 'Deleted successfully', life: 2000 });
                emit('set-deleted-id', props.selection.id);
                emit('set-form-values', response);
                visible.value = false;
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    })
}

</script>

<template>
    <Dialog
        v-model:visible="visible"
        header="Confirm delete"
        :style="{ width: '25rem' }"
        :position="'bottom'"
        :modal="true"
        :draggable="false"
        class="w-11 sm:w-auto"
    >
        <span class="p-text-secondary block mb-5">
            Are you sure you want to delete {{ props.selectionTextFormat(selection) }}?
        </span>
        <div class="flex justify-content-center gap-2">
            <Button
                type="button"
                label="Confirm"
                class="w-full"
                :loading="loading"
                @click="confirm"
            />
            <Button
                type="button"
                label="Cancel"
                class="w-full"
                severity="secondary"
                :disabled="loading"
                @click="visible = false"
            />
        </div>
    </Dialog>
</template>

<style scoped>

</style>
