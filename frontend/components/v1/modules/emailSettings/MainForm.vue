<script setup>
import * as yup from "yup";
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import MainPasswordInput from "~/components/v1/MainPasswordInput.vue";

const props = defineProps({
    initialFormValues: {
        type: Object,
        default: null,
    },
    tab: {
        type: Number,
        default: 0,
    }
})

const emit = defineEmits([
    'set-form-values',
    'handle-submit',
    'set-errors',
]);

onMounted(() => {
    if (props.initialFormValues) {
        setValues(props.initialFormValues);
    }
})

const schema = yup.object({
    item: yup.object({
        host: yup.string().required().label('Host'),
        port: yup.number().required().label('Port'),
        encryption: yup.string().required().label('Encryption'),
        validate_cert: yup.string().nullable().label('Validate cert'),
        username: yup.string().required().label('Username'),
        password: yup.string().required().label('Password'),
        protocol: yup.string().required().label('Protocol'),
        active: yup.bool().required().label('Active'),
    }),
});

const {defineField, handleSubmit, resetForm, errors, values, setValues} = useForm({
    validationSchema: schema,
    initialValues: {
        item: {
            host: null,
            port: null,
            encryption: null,
            validate_cert: false,
            username: null,
            password: null,
            protocol: null,
            active: false,
        }
    }
});

useInsideFormValidation(values, errors, emit, props.tab);

const [host] = defineField('item.host');
const [port] = defineField('item.port');
const [encryption] = defineField('item.encryption');
const [validateCert] = defineField('item.validate_cert');
const [username] = defineField('item.username');
const [password] = defineField('item.password');
const [protocol] = defineField('item.protocol');
const [active] = defineField('item.active');

const onSubmit = handleSubmit((values) => {
    return true;
});

defineExpose({onSubmit});

</script>

<template>
    <div>
        <form>
            <div class="formgrid grid">
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="host"
                        name="host"
                        label="Host"
                        :errors="errors"
                        required
                    />
                    <MainTextInput
                        v-model:value="port"
                        name="port"
                        label="Port"
                        :errors="errors"
                        required
                    />
                    <MainTextInput
                        v-model:value="encryption"
                        name="encryption"
                        label="Encryption"
                        :errors="errors"
                        required
                    />
                    <MainTextInput
                        v-model:value="protocol"
                        name="protocol"
                        label="Protocol"
                        :errors="errors"
                        required
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="username"
                        name="username"
                        label="Username"
                        :errors="errors"
                        required
                    />
                    <MainPasswordInput
                        v-model:value="password"
                        name="password"
                        label="Password"
                        :errors="errors"
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
