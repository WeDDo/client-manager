import * as yup from 'yup';

export const emailMessageSchema = yup.object({
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
