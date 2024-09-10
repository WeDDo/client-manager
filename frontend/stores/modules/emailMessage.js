import {defineStore} from "pinia";

export const useEmailMessageStore = defineStore('emailMessage', {
    state: () => ({
        name: 'Email messages',
        multiName: 'Email messages',
        apiRouteName: 'email-messages',
        frontRouteName: 'emails',
        lastSelection: null,
    }),
    actions: {

    }
});
