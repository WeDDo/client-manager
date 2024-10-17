<script setup>
import MainForm from "~/components/v1/modules/emailSettings/MainForm.vue";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useMainStore} from "~/stores/main.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import {useEmailSettingStore} from "~/stores/modules/emailSetting.js";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {emailSettingSchema} from "~/schemas/emailSettingSchema.js";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();

const route = useRoute();
const router = useRouter();
const store = useEmailSettingStore();
const toast = useToast();

const token = useCookie('token');

const form = useForm({
    validationSchema: emailSettingSchema,
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
        },
    }
});

const mainFormRef = ref();
let tabs = reactive([
    {name: 'Main', ref: mainFormRef, errors: {}},
]);

const checkConnectionResult = ref('loading');

const formHelper = useFormHelper(tabs);
const fetchHelper = useFetchHelper();

const {
    data,
    status,
    error,
    refresh
} = await useFetch(`${baseURL}/${store.apiRouteName}/${route.params.emailSettingId}`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    form.setValues(data.value)
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
        body: form.values.item,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Updated successfully', life: 2000});
                store.lastSelection = response.item;
                form.setValues(response._data);
            } else {
                fetchHelper.handleResponseError(response, form);
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
                toast.add({severity: 'success', summary: 'Connection successfully established!', life: 2000});
            } else {
                checkConnectionResult.value = 'error';
                fetchHelper.handleResponseError(response, form);
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
                    {{ store.singleName }} edit
                </div>
                <div>
                    <Button
                        label="Check"
                        size="small"
                        icon="pi pi-wifi"
                        class="mr-2"
                        text
                        raised
                        :severity="getCheckConnectionButtonSeverity()"
                        @click="checkConnection"
                    />
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
