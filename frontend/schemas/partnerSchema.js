import * as yup from 'yup';

export const partnerSchema = yup.object({
    item: yup.object({
        id_name: yup.string().required().label('ID name'),
        name: yup.string().required().label('Name'),
        name2: yup.string().nullable().label('Name 2'),
        legal_status: yup.string().nullable().label('Legal status'),
        email: yup.string().nullable().label('Email'),
        phone: yup.string().nullable().label('Phone'),
    }),
});
