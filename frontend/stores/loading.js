import {defineStore} from "pinia";

export const useLoadingStore = defineStore('loading', {
    state: () => ({
        fetchLoading: false,
        actionLoading: false,
    }),
});
