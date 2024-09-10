<script setup>
import * as yup from "yup";
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainPasswordInput from "~/components/v1/MainPasswordInput.vue";
import MainTextInput from "~/components/v1/MainTextInput.vue";

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
        email: yup.string().email().required().label('Email'),
        password: yup.string().required().label('Password'),
    }),
});

const {defineField, handleSubmit, resetForm, errors, values, setValues} = useForm({
    validationSchema: schema,
    initialValues: {
        item: {
            email: '',
            password: '',
        }
    }
});

useInsideFormValidation(values, errors, emit, props.tab);

const [email] = defineField('item.email');
const [password] = defineField('item.password');

const onSubmit = handleSubmit((values) => {
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
                        :errors="errors"
                        required
                    />
                </div>
                <div class="col-12">
                    <main-password-input
                        v-model:value="password"
                        name="password"
                        label="Password"
                        :errors="errors"
                        required
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
