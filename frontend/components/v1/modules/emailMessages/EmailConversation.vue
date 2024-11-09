<script setup>
import {useFetchHelper} from "~/composables/useFetchHelper.js";

const {public: {baseURL}} = useRuntimeConfig();

const token = useCookie('token');

const fetchHelper = useFetchHelper();

const props = defineProps({
    conversation: {
        type: Array,
        default: () => [],
    },
});

async function handleDownloadAttachment(attachment) {
    await $fetch(`${baseURL}/attachments/${attachment.id}/download`, {
        method: 'GET',
        responseType: 'blob',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({ response }) {
            if (response.ok) {
                fetchHelper.handleDownloadBlob(response);
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    })
}

</script>

<template>
    <Accordion multiple>
        <AccordionTab
            v-for="(email, index) in conversation"
            :key="index"
        >
            <template #header>
                <div>
                    <div>{{`${email.subject} - ${email.from}`}}</div>
                    <div class="text-xs">{{ email.date }}</div>
                </div>
            </template>
            <div>
                <div><strong>From:</strong> {{ email.from }}</div>
                <div><strong>To:</strong> {{ email.to }}</div>
                <div><strong>Date:</strong> {{ email.date }}</div>
                <div>
                    <strong>Attachments ({{ email.attachments?.length ?? 0 }}):</strong>
                    <div
                        v-for="attachment in email.attachments"
                        :key="attachment.id"
                    >
                        <i
                            class="pi pi-download cursor-pointer"
                            @click="handleDownloadAttachment(attachment)"
                        />
                        {{attachment.filename}}
                    </div>
                </div>
                <div><strong>HTML Body:</strong></div>
                <div v-html="email.body_html"></div>
            </div>
        </AccordionTab>
    </Accordion>
</template>

<style scoped>

</style>
