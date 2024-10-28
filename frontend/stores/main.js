import {defineStore} from "pinia";

export const useMainStore = defineStore('main', {
    state: () => ({
        settings: null,
        user: null,
        token: null,

        tabIndices: {}
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
        setTabIndex(path, index) {
            this.tabIndices[path] = index;
        },
        getTabIndex(path) {
            return this.tabIndices[path];
        }
    },
    persist: true,
});
