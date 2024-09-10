<script setup>
import * as yup from "yup";
import {useInsideFormValidation} from "~/composables/useInsideFormValidation.js";
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import MainPasswordInput from "~/components/v1/MainPasswordInput.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";
import MainEditor from "~/components/v1/MainEditor.vue";

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

</script>

<template>
    <div>
        <form>
            <div class="formgrid grid">
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="messageId"
                        name="message_id"
                        label="Message ID"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="subject"
                        name="subject"
                        label="Subject"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="from"
                        name="from"
                        label="From"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="to"
                        name="to"
                        label="To"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="cc"
                        name="cc"
                        label="Cc"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="bcc"
                        name="bcc"
                        label="Bcc"
                        :errors="errors"
                    />
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-3">
                    <MainTextInput
                        v-model:value="replyTo"
                        name="reply_to"
                        label="Reply To"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="date"
                        name="date"
                        label="Date"
                        :errors="errors"
                    />

                    <MainTextInput
                        v-model:value="folder"
                        name="folder"
                        label="Folder"
                        :errors="errors"
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
                    />
                </div>
                <div class="col-12">
                    <MainEditor
                        v-model:value="bodyHtml"
                        name="body_html"
                        label="Body HTML"
                        :errors="errors"
                    />

                    <MainEditor
                        v-model:value="bodyText"
                        name="body_text"
                        label="Body Text"
                        :errors="errors"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
