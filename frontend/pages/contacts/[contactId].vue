<script setup>
import MainForm from "~/components/v1/modules/contacts/MainForm.vue";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useMainStore} from "~/stores/main.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useContactStore} from "~/stores/modules/contact.js";
import {contactSchema} from "~/schemas/contactSchema.js";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const toast = useToast();

const token = useCookie('token');

const store = useContactStore();
const mainStore = useMainStore();
const loadingStore = useLoadingStore();

const form = useForm({
    validationSchema: contactSchema,
    initialValues: {
        item: {
            name: '',
            company_name: null,
            position: null,
            phone1: null,
            phone2: null,
            email1: null,
            email2: null,
            birthday: null,
            notes: null,
            address1: null,
            address2: null,
            city: null,
            state: null,
            postal_code: null,
            country: null,
            website: null,
            preferred_contact_method: null,
            status: null,
            last_contacted_at: null,
            partner_id: null,
        },
    }
});

const mainFormRef = ref();
let tabs = reactive([
    {name: 'Main', ref: mainFormRef, errors: {}},
]);

const formHelper = useFormHelper(tabs);
const fetchHelper = useFetchHelper();

const {
    data,
    status,
    error,
    refresh
} = await useFetch(`${baseURL}/${store.apiRouteName}/${route.params.contactId}`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    fetchHelper.handleResponseAutocompleteData(data.value, form)
} else {
    fetchHelper.handleUseFetchError(error);
}

async function handleUpdate() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    loadingStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.contactId}`, {
        method: 'PUT',
        body: fetchHelper.getRequestBodyWithAutocompleteData(form),
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Updated successfully', life: 2000});
                store.lastSelection = response.item;
                fetchHelper.handleResponseAutocompleteData(response._data, form)
            } else {
                fetchHelper.handleResponseError(response, form);
            }
            loadingStore.actionLoading = false;
        },
    })
}
</script>

<template>
    <div>
        <MainMenuBar/>
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    {{ store.singleName }} edit
                </div>
                <div>
                    <Button
                        label="Save"
                        size="small"
                        icon="pi pi-save"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        @click="handleUpdate"
                    />
                    <Button
                        icon="pi pi-times"
                        size="small"
                        severity="contrast"
                        text
                        raised
                        @click="() => router.push(`/${store.frontRouteName}`)"
                    />
                </div>
            </div>
            <div class="mt-2">
                <BasicTabs
                    :tabs="tabs"
                >
                    <template #tab0>
                        <MainForm
                            ref="mainFormRef"
                            v-model:form="form"
                            :tab="0"
                            @handle-submit="handleUpdate()"
                            @set-errors="formHelper.setErrors"
                        />
                    </template>
                </BasicTabs>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
