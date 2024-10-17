import {defineStore} from "pinia";

export const usePartnerStore = defineStore('partner', {
    state: () => ({
        name: 'partners',
        singleName: 'Partner',
        multiName: 'Partners',
        apiRouteName: 'partners',
        frontRouteName: 'partners',
        lastSelection: null,
    }),
    actions: {

    }
});
