import {defineStore} from "pinia";

export const useEmailSettingStore = defineStore('emailSetting', {
    state: () => ({
        name: 'Email settings',
        multiName: 'Email settings',
        apiRouteName: 'email-settings',
        frontRouteName: 'email-settings',
        lastSelection: null,
    }),
    actions: {

    }
});
