<script setup>
import MainForm from "~/components/v1/modules/emailSettings/MainForm.vue";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useMainStore} from "~/stores/main.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import {useEmailSettingStore} from "~/stores/modules/emailSetting.js";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();

const route = useRoute();
const router = useRouter();
const store = useEmailSettingStore();
const toast = useToast();

const token = useCookie('token');

let formValues = reactive({
    item: {
        name: '',
    },
});

const mainFormRef = ref();
let tabs = reactive([
    {name: 'Main', ref: mainFormRef, errors: {}},
]);

const checkConnectionResult = ref('loading');

const formHelper = useFormHelper(formValues, tabs);
const fetchHelper = useFetchHelper();

const {
    data,
    pending,
    error,
    refresh
} = await useFetch(`${baseURL}/${store.apiRouteName}/${route.params.emailSettingId}`, {
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

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.emailSettingId}`, {
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

async function checkConnection() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.emailSettingId}/check-connection`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                checkConnectionResult.value = null;
                toast.add({severity: 'success', summary: 'Connection established!', life: 2000});
            } else {
                checkConnectionResult.value = 'error';
                toast.add({severity: 'error', summary: 'Authorisation error!', life: 5000});
                fetchHelper.handleResponseError(response);
            }
            mainStore.actionLoading = false;
        },
    })
}

function getCheckConnectionButtonSeverity() {
    if(checkConnectionResult.value === 'error') return 'danger';
    if(checkConnectionResult.value === 'loading') return 'secondary';

    return undefined;
}
</script>

<template>
    <div>
        <MainMenuBar/>
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    Email setting edit
                </div>
                <div>
                    <Button
                        label="Save"
                        size="small"
                        icon="pi pi-save"
                        class="mr-2"
                        @click="handleUpdate"
                    />
                    <Button
                        label="Check"
                        size="small"
                        icon="pi pi-wifi"
                        class="mr-2"
                        :severity="getCheckConnectionButtonSeverity()"
                        @click="checkConnection"
                    />
                    <Button
                        label="Back"
                        size="small"
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
