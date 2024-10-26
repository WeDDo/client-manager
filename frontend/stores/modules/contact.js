import {defineStore} from "pinia";

export const useContactStore = defineStore('contact', {
    state: () => ({
        name: 'contacts',
        singleName: 'Contact',
        multiName: 'Contact',
        apiRouteName: 'contacts',
        frontRouteName: 'contacts',
        lastSelection: null,
    }),
});
