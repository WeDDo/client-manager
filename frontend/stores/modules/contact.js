import {defineStore} from "pinia";

export const useContactStore = defineStore('contact', {
    state: () => ({
        name: 'contacts',
        singleName: 'Contact',
        multiName: 'Contacts',
        apiRouteName: 'contacts',
        frontRouteName: 'contacts',
        lastSelection: null,

        additionalFormData: {},
    }),
    actions: {
        getAdditionalFormData() {
            const additionalFormData = { ...this.additionalFormData };
            this.additionalFormData = null;
            return additionalFormData;
        },
        async setAdditionalFormData(data) {
            this.additionalFormData = data;
        }
    }
});
