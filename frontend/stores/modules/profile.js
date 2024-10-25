import {defineStore} from "pinia";

export const useProfileStore = defineStore('profile', {
    state: () => ({
        name: 'profile',
        singleName: 'Profile',
        multiName: 'Profiles',
        apiRouteName: 'profile',
        frontRouteName: 'profile',
    }),
});
