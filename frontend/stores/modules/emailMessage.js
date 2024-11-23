import {defineStore} from "pinia";

export const useEmailMessageStore = defineStore('emailMessage', {
    state: () => ({
        name: 'Email messages',
        singleName: 'Email message',
        multiName: 'Emails',
        apiRouteName: 'email-messages',
        frontRouteName: 'emails',
        lastSelection: null,

        selectedFolder: 'INBOX'
    }),
    actions: {

    }
});
