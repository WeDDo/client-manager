import {defineStore} from "pinia";

export const useEmailInboxSettingStore = defineStore('emailInboxSetting', {
    state: () => ({
        name: 'Email inbox settings',
        singleName: 'Email inbox setting',
        multiName: 'Email inbox settings',
        apiRouteName: 'email-inbox-settings',
        frontRouteName: 'email-inbox-settings',
        lastSelection: null,
    }),
    actions: {

    }
});
