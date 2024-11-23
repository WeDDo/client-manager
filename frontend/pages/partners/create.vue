<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainForm from "~/components/v1/modules/partners/MainForm.vue";
import {usePartnerStore} from "~/stores/modules/partner.js";
import {partnerSchema} from "~/schemas/partnerSchema.js";
import ActionButtonsButton from "~/components/v1/ActionButtonsButton.vue";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();
const loadingStore = useLoadingStore();

const router = useRouter();
const store = usePartnerStore();
const toast = useToast();

const token = useCookie('token');

const form = useForm({
    validationSchema: partnerSchema,
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

const mainFormRef = ref();
let tabs = reactive([
    {name: "Main", ref: mainFormRef, errors: {}},
    {name: 'Contacts', ref: null, errors: {}, disabled: true},
]);

const formHelper = useFormHelper(tabs);
const fetchHelper = useFetchHelper();

async function handleCreate() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    loadingStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}`, {
        method: 'POST',
        body: form.values.item,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Created successfully', life: 2000});
                store.lastSelection = response._data.item;
                fetchHelper.handleRouteTo(`/${store.frontRouteName}`, response);
            } else {
                fetchHelper.handleResponseError(response, form);
            }
            loadingStore.actionLoading = false;
        },
    })
}

const actions = computed(() => {
    return [
        {
            label: 'Actions',
            items: [
                {
                    label: 'Save',
                    icon: 'pi pi-save',
                    command: handleCreate
                },
            ]
        }
    ]
});
</script>

<template>
    <div>
        <MainMenuBar/>
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    {{ store.singleName }} add
                </div>
                <div class="flex justify-content-center">
                    <ActionButtonsButton
                        class="mr-2"
                        :actions="actions"
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
                            @handle-submit="handleCreate()"
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
