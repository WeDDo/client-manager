<script setup>
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import EmailConversation from "~/components/v1/modules/emailMessages/EmailConversation.vue";
import MainEditor from "~/components/v1/MainEditor.vue";

const {public: {baseURL}} = useRuntimeConfig();

const toast = useToast();
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
const files = defineModel('files');
const replyHtml = defineModel('replyHtml');

useInsideFormValidation(form.value.errors, emit, props.tab);

const [messageId] = form.value.defineField('item.message_id');
const [subject] = form.value.defineField('item.subject');
const [from] = form.value.defineField('item.from');
const [to] = form.value.defineField('item.to');
const [cc] = form.value.defineField('item.cc');
const [bcc] = form.value.defineField('item.bcc');
const [replyTo] = form.value.defineField('item.reply_to');
const [date] = form.value.defineField('item.date');
const [bodyText] = form.value.defineField('item.body_text');
const [bodyHtml] = form.value.defineField('item.body_html');
const [isSeen] = form.value.defineField('item.is_seen');
const [isFlagged] = form.value.defineField('item.is_flagged');
const [isAnswered] = form.value.defineField('item.is_answered');
const [folder] = form.value.defineField('item.folder');
const [userId] = form.value.defineField('item.user_id');

const onSubmit = form.value.handleSubmit((values) => {
    return true;
});

defineExpose({onSubmit});

function uploadFile(event) {
    files.value = event.files;
}

function removeFile(index) {
    files.value.splice(index, 1);
}

function removeAllFiles() {
    files.value = [];
}
</script>

<template>
    <div class="p-2">
        <form>
            <div class="formgrid grid">
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="subject"
                        name="subject"
                        label="Subject"
                        :errors="form.errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="from"
                        name="from"
                        label="From"
                        :errors="form.errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="to"
                        name="to"
                        label="To"
                        :errors="form.errors"
                        disabled
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="replyTo"
                        name="reply_to"
                        label="Reply To"
                        :errors="form.errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="cc"
                        name="cc"
                        label="Cc"
                        :errors="form.errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="bcc"
                        name="bcc"
                        label="Bcc"
                        :errors="form.errors"
                        disabled
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="messageId"
                        name="message_id"
                        label="Message ID"
                        :errors="form.errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="date"
                        name="date"
                        label="Date"
                        :errors="form.errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="folder"
                        name="folder"
                        label="Folder"
                        :errors="form.errors"
                        disabled
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainCheckbox
                        v-model:value="isSeen"
                        name="is_seen"
                        label="Is Seen"
                        :show-error="false"
                        align-with-inputs
                    />

                    <MainCheckbox
                        v-model:value="isFlagged"
                        name="is_flagged"
                        label="Is Flagged"
                        :show-error="false"
                        align-with-inputs
                    />

                    <MainCheckbox
                        v-model:value="isAnswered"
                        name="is_answered"
                        label="Is Answered"
                        :show-error="false"
                        align-with-inputs
                        disabled
                    />
                </div>

                <Divider />
                <div class="col-12">
                    <div class="mt-2">
                        <Accordion multiple>
                            <AccordionTab
                            >
                                <template #header>
                                    <div>
                                        Reply
                                    </div>
                                </template>
                                <div>
                                    <MainEditor
                                        v-model:value="replyHtml"
                                        name="reply_html"
                                        label="Reply text"
                                    />

                                    <label>
                                        Reply files
                                    </label>
                                    <FileUpload
                                        name="files[]"
                                        choose-label="Change"
                                        :max-file-size="10000000"
                                        multiple
                                        auto
                                        :show-upload-button="false"
                                        :show-cancel-button="false"
                                        custom-upload
                                        @uploader="uploadFile($event)"
                                    >
                                        <template #header="{ chooseCallback }">
                                            <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                                                <div class="flex gap-2">
                                                    <Button @click="chooseCallback" icon="pi pi-images" rounded outlined></Button>
                                                    <Button @click="removeAllFiles()" icon="pi pi-times" rounded outlined severity="danger" :disabled="!files || files.length === 0"></Button>
                                                </div>
                                            </div>
                                        </template>
                                        <template #empty>
                                            <div class="mt-2">
                                                Drag and drop files to here to send
                                            </div>
                                        </template>
                                        <template #content>
                                            <div v-if="files.length" class="mt-2 flex flex-wrap gap-2">
                                                <div
                                                    v-for="(file, index) in files"
                                                    :key="file.name"
                                                    class="p-2 border-round flex flex-column align-items-center justify-content-between w-6rem h-6rem"
                                                >
                                                    <div v-if="file.type.startsWith('image/')" v-tooltip.right="file.name" class="w-full h-full flex align-items-center justify-content-center overflow-hidden border-round mb-1">

                                                        <Image alt="Image" preview>
                                                            <template #indicatoricon>
                                                                <i class="pi pi-search"></i>
                                                            </template>
                                                            <template #image>
                                                                <img
                                                                    :src="file.objectURL"
                                                                    :alt="file.name"
                                                                    class="w-full h-full object-cover"
                                                                />
                                                            </template>
                                                            <template #preview="slotProps">
                                                                <img
                                                                    :src="file.objectURL"
                                                                    alt="preview"
                                                                    :style="slotProps.style"
                                                                    @click="slotProps.onClick"
                                                                />
                                                            </template>
                                                        </Image>

                                                    </div>
                                                    <div v-else class="w-full mb-1">
                                                        <div v-tooltip.right="file.name" class="text-sm text-center text-ellipsis overflow-hidden white-space-nowrap" style="max-width: 5rem;">
                                                            {{ file.name }}
                                                        </div>
                                                    </div>

                                                    <Button
                                                        label="Remove"
                                                        icon="pi pi-times"
                                                        class="p-button-danger p-button-text mt-auto"
                                                        size="small"
                                                        @click="removeFile(index)"
                                                    />
                                                </div>
                                            </div>
                                        </template>
                                    </FileUpload>
                                </div>
                            </AccordionTab>
                        </Accordion>
                    </div>
                </div>
                <Divider />
                <div class="col-12">
                   <EmailConversation
                        :conversation="form.values.additional.conversation"
                   />
                </div>
            </div>
        </form>
    </div>
</template>

<style>

</style>
