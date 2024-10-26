<script setup>
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import MainPasswordInput from "~/components/v1/MainPasswordInput.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";

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

const [host] = form.value.defineField('item.host');
const [port] = form.value.defineField('item.port');
const [encryption] = form.value.defineField('item.encryption');
const [validateCert] = form.value.defineField('item.validate_cert');
const [username] = form.value.defineField('item.username');
const [password] = form.value.defineField('item.password');
const [protocol] = form.value.defineField('item.protocol');
const [active] = form.value.defineField('item.active');

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
                        v-model:value="host"
                        name="host"
                        label="Host"
                        :errors="form.errors"
                        required
                    />
                    <MainTextInput
                        v-model:value="port"
                        name="port"
                        label="Port"
                        :errors="form.errors"
                        required
                    />
                    <MainSelectInput
                        v-model:value="encryption"
                        name="encryption"
                        label="Encryption"
                        :errors="form.errors"
                        :options="['ssl', 'tls']"
                        form-prefix=""
                        simple-options
                        required
                    />
                    <MainSelectInput
                        v-model:value="protocol"
                        name="protocol"
                        label="Protocol"
                        :errors="form.errors"
                        :options="['smtp', 'imap']"
                        form-prefix=""
                        simple-options
                        required
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="username"
                        name="username"
                        label="Username"
                        :errors="form.errors"
                        required
                    />
                    <MainPasswordInput
                        v-model:value="password"
                        name="password"
                        label="Password"
                        :errors="form.errors"
                        required
                    />
                    <MainCheckbox
                        v-model:value="active"
                        name="active"
                        label="Active"
                        :show-error="false"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
