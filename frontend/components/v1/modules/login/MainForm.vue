<script setup>
import * as yup from "yup";
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainPasswordInput from "~/components/v1/MainPasswordInput.vue";
import MainTextInput from "~/components/v1/MainTextInput.vue";

const props = defineProps({
    tab: {
        type: Number,
        default: 0,
    }
})

const emit = defineEmits(
    'set-errors',
);

const form = defineModel('form');

useInsideFormValidation(form.value.errors, emit, props.tab);

const [email] = form.value.defineField('item.email');
const [password] = form.value.defineField('item.password');

const onSubmit = form.value.handleSubmit((values) => {
    return true;
});

defineExpose({onSubmit});

</script>

<template>
    <div>
        <form>
            <div class="formgrid grid">
                <div class="col-12">
                    <div class="text-2xl text-center">
                        Sign in here
                    </div>
                </div>
                <div class="col-12">
                    <MainTextInput
                        v-model:value="email"
                        name="email"
                        label="Email"
                        :errors="form.errors"
                        required
                    />
                </div>
                <div class="col-12">
                    <main-password-input
                        v-model:value="password"
                        name="password"
                        label="Password"
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
