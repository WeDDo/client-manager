<script setup>
import * as yup from "yup";
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
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

const schema = yup.object({
    item: yup.object({
        name: yup.string().required().label('Name'),
    }),
});

const {defineField, handleSubmit, resetForm, errors, values, setValues} = useForm({
    validationSchema: schema,
    initialValues: {
        item: {
            name: null,
        }
    }
});

onMounted(() => {
    if (props.initialFormValues) {
        setValues(props.initialFormValues);
    }
})

useInsideFormValidation(values, errors, emit, props.tab);

const [name] = defineField('item.name');

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
                        v-model:value="name"
                        name="name"
                        label="Name"
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
