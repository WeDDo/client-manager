import {defineStore} from "pinia";

export const useEmailSettingStore = defineStore('emailSetting', {
    state: () => ({
        name: 'Email settings',
        singleName: 'Email setting',
        multiName: 'Email settings',
        apiRouteName: 'email-settings',
        frontRouteName: 'email-settings',
        lastSelection: null,
    }),
    actions: {

    }
});
