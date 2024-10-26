<script setup>
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";

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

useInsideFormValidation(form.value.errors, emit, props.tab);

const [name] = form.value.defineField('item.name');

const onSubmit = form.value.handleSubmit((values) => {
    return true;
});

defineExpose({onSubmit});

</script>

<template>
    <div class="p-2">
        <form>
            <div class="formgrid grid">
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="name"
                        name="name"
                        label="Name"
                        :errors="form.errors"
                        required
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
