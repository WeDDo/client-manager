<script setup>
import MainForm from "~/components/v1/modules/emailInboxSettings/MainForm.vue";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useMainStore} from "~/stores/main.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useEmailInboxSettingStore} from "~/stores/modules/emailInboxSetting.js";
import {emailInboxSettingSchema} from "~/schemas/emailInboxSettingSchema.js";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();
const loadingStore = useLoadingStore();

const route = useRoute();
const router = useRouter();
const store = useEmailInboxSettingStore();
const toast = useToast();

const token = useCookie('token');

const form = useForm({
    validationSchema: emailInboxSettingSchema,
    initialValues: {
        item: {
            name: null,
            auto_set_is_seen: false,
        }
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
} = await useFetch(`${baseURL}/${store.apiRouteName}/${route.params.emailInboxSettingId}`, {
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

    loadingStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.emailInboxSettingId}`, {
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
                    Email setting edit
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
