import * as yup from 'yup';

export const chatRoomSchema = yup.object({
    item: yup.object({
        name: yup.string().required().label('Name'),
    }),
});
