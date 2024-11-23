<script setup>
import MainForm from "~/components/v1/modules/emailMessages/MainForm.vue";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useMainStore} from "~/stores/main.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useEmailMessageStore} from "~/stores/modules/emailMessage.js";
import {useConfirm} from "primevue/useconfirm";
import {emailMessageSchema} from "~/schemas/emailMessageSchema.js";
import ActionButtonsButton from "~/components/v1/ActionButtonsButton.vue";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();
const loadingStore = useLoadingStore();

const route = useRoute();
const router = useRouter();
const store = useEmailMessageStore();
const toast = useToast();
const confirm = useConfirm();

const token = useCookie('token');

const form = useForm({
    validationSchema: emailMessageSchema,
    initialValues: {
        item: {
            message_id: '',
            subject: '',
            from: '',
            to: '',
            cc: '',
            bcc: '',
            reply_to: '',
            date: '',
            body_text: '',
            body_html: '',
            is_seen: false,
            is_flagged: false,
            is_answered: false,
            folder: '',
            reply_to_email_message_id: null,
            user_id: null,
        }
    }
});
const files = ref([]);

const replyHtml = ref('');

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
} = await useFetch(`${baseURL}/${store.apiRouteName}/${route.params.emailMessageId}`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    form.setValues(data.value);
} else {
    fetchHelper.handleUseFetchError(error);
}

async function handleUpdate() {
    console.log(123);
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    loadingStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.emailMessageId}`, {
        method: 'PUT',
        body: form.values.item,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Updated successfully', life: 2000});
                store.lastSelection = response._data.item;
            } else {
                fetchHelper.handleResponseError(response, form);
            }
            loadingStore.actionLoading = false;
        },
    })
}

async function handleReply() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }
    loadingStore.actionLoading = true;

    const formData = new FormData();
    formData.append('reply_html', replyHtml.value);
    form.values.item.from.split(',').map(email => email.trim()).forEach(email => formData.append('to_emails[]', email));
    form.values.item.cc?.split(',').map(email => email.trim()).forEach(email => formData.append('cc_emails[]', email));
    form.values.item.bcc?.split(',').map(email => email.trim()).forEach(email => formData.append('bcc_emails[]', email));

    files.value.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.emailMessageId}/send`, {
        method: 'POST',
        body: formData,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            form.setValues(response._data);
            replyHtml.value = null;
            files.value = [];
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Replied successfully', life: 2000});
            } else {
                fetchHelper.handleResponseError(response, form);
            }
            loadingStore.actionLoading = false;
        },
    })
}

function confirmReply() {
    confirm.require({
        message: 'Are you sure you want to send a reply via email?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined',
        rejectLabel: 'Cancel',
        acceptLabel: 'Confirm',
        accept: () => {
            handleReply();
        },
        reject: () => {}
    });
}

const actions = computed(() => {
    return [
        {
            label: 'Actions',
            items: [
                {
                    label: 'Save',
                    icon: 'pi pi-save',
                    command: handleUpdate
                },
                {
                    label: 'Reply',
                    icon: 'pi pi-reply',
                    disabled: form.values?.item?.folder !== 'INBOX' || !replyHtml.value,
                    command: confirmReply
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
                    {{ store.singleName }}
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
                        :disabled="loadingStore.actionLoading"
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
                            v-model:reply-html="replyHtml"
                            v-model:files="files"
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
