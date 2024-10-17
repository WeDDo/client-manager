import * as yup from 'yup';

export const emailInboxSettingSchema = yup.object({
    item: yup.object({
        name: yup.string().required().label('Name'),
    }),
});
