<script setup>
import MainForm from "~/components/v1/modules/partners/MainForm.vue";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useMainStore} from "~/stores/main.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {usePartnerStore} from "~/stores/modules/partner.js";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();

const route = useRoute();
const router = useRouter();
const store = usePartnerStore();
const toast = useToast();

const token = useCookie('token');

let formValues = reactive({
    item: {
        id_name: null,
        name: null,
        name2: null,
        legal_status: null,
        email: null,
        phone: null,
    },
});

const mainFormRef = ref();
let tabs = reactive([
    {name: 'Main', ref: mainFormRef, errors: {}},
]);

const formHelper = useFormHelper(formValues, tabs);
const fetchHelper = useFetchHelper();

const {
    data,
    status,
    error,
    refresh
} = await useFetch(`${baseURL}/${store.apiRouteName}/${route.params.partnerId}`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    formHelper.setFormValues(data.value)
} else {
    fetchHelper.handleUseFetchError(error);
}

async function handleUpdate() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.partnerId}`, {
        method: 'PUT',
        body: formValues.item,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Updated successfully', life: 2000});
                store.lastSelection = response.item;
                router.push(`/${store.frontRouteName}`);
            } else {
                fetchHelper.handleResponseError(response);
            }
            mainStore.actionLoading = false;
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
                    Partner edit
                </div>
                <div>
                    <Button
                        label="Save"
                        size="small"
                        icon="pi pi-save"
                        class="mr-2"
                        text
                        @click="handleUpdate"
                    />
                    <Button
                        icon="pi pi-times"
                        size="small"
                        text
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
                            :tab="0"
                            :initial-form-values="formValues"
                            @set-form-values="formHelper.setFormValues($event)"
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
