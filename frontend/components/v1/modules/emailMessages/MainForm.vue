<script setup>
import * as yup from "yup";
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import EmailConversation from "~/components/v1/modules/emailMessages/EmailConversation.vue";
import MainEditor from "~/components/v1/MainEditor.vue";
import moment from "moment";

const {public: {baseURL}} = useRuntimeConfig();

const toast = useToast();

const token = useCookie('token');

const fetchHelper = useFetchHelper();

const props = defineProps({
    initialFormValues: {
        type: Object,
        default: null,
    },
    tab: {
        type: Number,
        default: 0,
    }
})

const emit = defineEmits([
    'set-form-values',
    'handle-submit',
    'set-errors',
]);

onMounted(() => {
    if (props.initialFormValues) {
        setValues(props.initialFormValues);
    }
})

const schema = yup.object({
    item: yup.object({
        message_id: yup.string().nullable().label('Message ID'),
        subject: yup.string().nullable().label('Subject'),
        from: yup.string().nullable().label('From'),
        to: yup.string().nullable().label('To'),
        cc: yup.string().nullable().label('Cc'),
        bcc: yup.string().nullable().label('Bcc'),
        reply_to: yup.string().nullable().label('Reply To'),
        date: yup.date().nullable().label('Date'),
        body_text: yup.string().nullable().label('Body Text'),
        body_html: yup.string().nullable().label('Body HTML'),
        is_seen: yup.boolean().default(false).label('Seen'),
        is_flagged: yup.boolean().default(false).label('Flagged'),
        is_answered: yup.boolean().default(false).label('Answered'),
        folder: yup.string().nullable().label('Folder'),
        user_id: yup.number().nullable().label('User ID'),
    }),
});

const {defineField, handleSubmit, resetForm, errors, values, setValues} = useForm({
    validationSchema: schema,
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

useInsideFormValidation(values, errors, emit, props.tab);

const [messageId] = defineField('item.message_id');
const [subject] = defineField('item.subject');
const [from] = defineField('item.from');
const [to] = defineField('item.to');
const [cc] = defineField('item.cc');
const [bcc] = defineField('item.bcc');
const [replyTo] = defineField('item.reply_to');
const [date] = defineField('item.date');
const [bodyText] = defineField('item.body_text');
const [bodyHtml] = defineField('item.body_html');
const [isSeen] = defineField('item.is_seen');
const [isFlagged] = defineField('item.is_flagged');
const [isAnswered] = defineField('item.is_answered');
const [folder] = defineField('item.folder');
const [userId] = defineField('item.user_id');

const onSubmit = handleSubmit((values) => {
    return true;
});

defineExpose({onSubmit});

const files = defineModel('files');
const replyHtml = defineModel('replyHtml');

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
    <div>
        <form>
            <div class="formgrid grid">
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="subject"
                        name="subject"
                        label="Subject"
                        :errors="errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="from"
                        name="from"
                        label="From"
                        :errors="errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="to"
                        name="to"
                        label="To"
                        :errors="errors"
                        disabled
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="replyTo"
                        name="reply_to"
                        label="Reply To"
                        :errors="errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="cc"
                        name="cc"
                        label="Cc"
                        :errors="errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="bcc"
                        name="bcc"
                        label="Bcc"
                        :errors="errors"
                        disabled
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="messageId"
                        name="message_id"
                        label="Message ID"
                        :errors="errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="date"
                        name="date"
                        label="Date"
                        :errors="errors"
                        disabled
                    />

                    <MainTextInput
                        v-model:value="folder"
                        name="folder"
                        label="Folder"
                        :errors="errors"
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
                <div class="col-12">
                    <MainEditor
                        v-model:value="replyHtml"
                        name="reply_html"
                        label="Reply HTML"
                    />

                    <div class="mt-2">
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
                            <template #empty>
                                <div class="mt-2">Drag and drop files to here to send</div>
                            </template>
                            <template #content>
                                <div v-if="files.length" class="mt-2 flex flex-wrap gap-2">
                                    <div
                                        v-for="(file, index) in files"
                                        :key="file.name"
                                        class="surface-100 p-2 border-round flex flex-column align-items-center justify-content-between w-6rem h-6rem"
                                    >
                                        <div v-if="file.type.startsWith('image/')" v-tooltip.right="file.name" class="w-full h-full flex align-items-center justify-content-center overflow-hidden border-round mb-1">
                                            <img :src="file.objectURL" :alt="file.name" class="w-full h-full object-cover" />
                                        </div>
                                        <div v-else class="w-full mb-1">
                                            <div v-tooltip.right="file.name" class="text-sm text-center text-ellipsis overflow-hidden white-space-nowrap" style="max-width: 5rem;">
                                                {{ file.name }}
                                            </div>
                                        </div>

                                        <Button
                                            label="Clear"
                                            icon="pi pi-times"
                                            class="p-button-danger p-button-text mt-auto"
                                            size="small"
                                            @click="removeFile(index)"
                                        />
                                    </div>
                                </div>

                                <div v-if="files.length" class="mt-3">
                                    <Button
                                        label="Clear All"
                                        icon="pi pi-times"
                                        class="p-button-danger p-button-text"
                                        size="small"
                                        @click="removeAllFiles"
                                    />
                                </div>
                            </template>
                        </FileUpload>
                    </div>
                </div>
                <div class="col-12">
                   <EmailConversation
                        :conversation="initialFormValues.additional.conversation"
                   />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>
</style>
