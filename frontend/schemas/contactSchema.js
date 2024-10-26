import * as yup from 'yup';

export const contactSchema = yup.object({
    item: yup.object({
        name: yup.string().required().label('Name'),
    }),
});
