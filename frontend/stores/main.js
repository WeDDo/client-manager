import {defineStore} from "pinia";

export const useMainStore = defineStore('main', {
    state: () => ({
        settings: null,
        fetchLoading: false,
        actionLoading: false,
        user: null,
        token: null,
    }),
    actions: {
        async getSettings(baseURL, token) {
            if(!token) {return;}

            $fetch(`${baseURL}/settings`, {
                method: 'GET',
                headers: {
                    authorization: `Bearer ${token}`
                },
            }).then((response) => {
                this.settings = response.item;
            })
        },
    },
    persist: true,
});
