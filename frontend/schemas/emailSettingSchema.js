import * as yup from 'yup';

export const emailSettingSchema = yup.object({
    item: yup.object({
        host: yup.string().required().label('Host'),
        port: yup.number().required().label('Port'),
        encryption: yup.string().required().label('Encryption'),
        validate_cert: yup.string().nullable().label('Validate cert'),
        username: yup.string().required().label('Username'),
        password: yup.string().required().label('Password'),
        protocol: yup.string().required().label('Protocol'),
        active: yup.bool().required().label('Active'),
    }),
});
