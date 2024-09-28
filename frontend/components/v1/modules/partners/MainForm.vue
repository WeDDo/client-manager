<script setup>
import * as yup from "yup";
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import EmailConversation from "~/components/v1/modules/emailMessages/EmailConversation.vue";
import MainEditor from "~/components/v1/MainEditor.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";

const {public: {baseURL}} = useRuntimeConfig();

const token = useCookie('token');

const fetchHelper = useFetchHelper();

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
        id_name: yup.string().required().label('ID name'),
        name: yup.string().required().label('Name'),
        name2: yup.string().nullable().label('Name 2'),
        legal_status: yup.string().nullable().label('Legal status'),
        email: yup.string().nullable().label('Email'),
        phone: yup.string().nullable().label('Phone'),
    }),
});

const {defineField, handleSubmit, resetForm, errors, values, setValues} = useForm({
    validationSchema: schema,
    initialValues: {
        item: {
            id_name: null,
            name: null,
            name2: null,
            legal_status: null,
            email: null,
            phone: null,
        }
    }
});

useInsideFormValidation(values, errors, emit, props.tab);

const [idName] = defineField('item.id_name');
const [name] = defineField('item.name');
const [name2] = defineField('item.name2');
const [legalStatus] = defineField('item.legal_status');
const [email] = defineField('item.email');
const [phone] = defineField('item.phone');

const onSubmit = handleSubmit((values) => {
    return true;
});

defineExpose({onSubmit});

const replyHtml = defineModel('replyHtml');
</script>

<template>
    <div>
        <form>
            <div class="formgrid grid">
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="idName"
                        name="id_name"
                        label="ID name"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="name"
                        name="name"
                        label="Name"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="name2"
                        name="name2"
                        label="Name 2"
                        :errors="errors"
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainSelectInput
                        v-model:value="legalStatus"
                        name="legal_status"
                        label="Legal status"
                        :errors="errors"
                        :options="['F', 'J']"
                        form-prefix=""
                        simple-options
                        show-clear
                    />

                    <MainTextInput
                        v-model:value="email"
                        name="email"
                        label="Email"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="phone"
                        name="phone"
                        label="Phone"
                        :errors="errors"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
