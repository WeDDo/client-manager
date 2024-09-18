<script setup>
import MainForm from "~/components/v1/modules/emailMessages/MainForm.vue";
import {useFormHelper} from "~/composables/useFormHelper.js";
import {useMainStore} from "~/stores/main.js";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useEmailMessageStore} from "~/stores/modules/emailMessage.js";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();

const route = useRoute();
const router = useRouter();
const store = useEmailMessageStore();
const toast = useToast();

const token = useCookie('token');

let formValues = reactive({
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
        user_id: null,
    },
});

const mainFormRef = ref();
let tabs = reactive([
    {name: 'Main', ref: mainFormRef, errors: {}},
]);

const replyHtml = ref('');

const formHelper = useFormHelper(formValues, tabs);
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
    formHelper.setFormValues(data.value)
} else {
    fetchHelper.handleUseFetchError(error);
}


async function handleUpdate() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.emailMessageId}`, {
        method: 'PUT',
        body: formValues.item,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Updated successfully', life: 2000});
                store.lastSelection = response._data.item;
                router.push(`/${store.frontRouteName}`);
            } else {
                fetchHelper.handleResponseError(response);
            }
            mainStore.actionLoading = false;
        },
    })
}

async function handleReply() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${route.params.emailMessageId}/send`, {
        method: 'POST',
        body: {
            reply_html: replyHtml.value,
            to_emails: formValues.item.from.split(',').map(email => email.trim()),
            cc_emails: formValues.item?.cc?.split(',').map(email => email.trim()),
            bcc_emails: formValues.item?.bcc?.split(',').map(email => email.trim()),
        },
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Replied successfully', life: 2000});
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
                    Email message
                </div>
                <div>
                    <Button
                        v-if="formValues?.item?.folder === 'INBOX'"
                        label="Reply"
                        size="small"
                        icon="pi pi-reply"
                        class="mr-2"
                        @click="handleReply"
                    />
                    <Button
                        label="Save"
                        size="small"
                        icon="pi pi-save"
                        class="mr-2"
                        @click="handleUpdate"
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
                            v-model:reply-html="replyHtml"
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
