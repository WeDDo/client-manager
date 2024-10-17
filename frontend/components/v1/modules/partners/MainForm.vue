<script setup>
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";

const {public: {baseURL}} = useRuntimeConfig();

const token = useCookie('token');

const fetchHelper = useFetchHelper();

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

const [idName] = form.value.defineField('item.id_name');
const [name] = form.value.defineField('item.name');
const [name2] = form.value.defineField('item.name2');
const [legalStatus] = form.value.defineField('item.legal_status');
const [email] = form.value.defineField('item.email');
const [phone] = form.value.defineField('item.phone');

const onSubmit = form.value.handleSubmit((values) => {
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
                        v-model:value="idName"
                        name="id_name"
                        label="ID name"
                        :errors="form.errors"
                        required
                    />

                    <MainTextInput
                        v-model:value="name"
                        name="name"
                        label="Name"
                        :errors="form.errors"
                        required
                    />

                    <MainTextInput
                        v-model:value="name2"
                        name="name2"
                        label="Name 2"
                        :errors="form.errors"
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainSelectInput
                        v-model:value="legalStatus"
                        name="legal_status"
                        label="Legal status"
                        :errors="form.errors"
                        :options="['F', 'J']"
                        form-prefix=""
                        simple-options
                        show-clear
                    />

                    <MainTextInput
                        v-model:value="email"
                        name="email"
                        label="Email"
                        :errors="form.errors"
                    />

                    <MainTextInput
                        v-model:value="phone"
                        name="phone"
                        label="Phone"
                        :errors="form.errors"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
