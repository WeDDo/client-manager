import {defineStore} from "pinia";

export const useChatRoomStore = defineStore('chatRoom', {
    state: () => ({
        name: 'Chat rooms',
        multiName: 'Chat rooms',
        apiRouteName: 'chat-rooms',
        frontRouteName: 'chat-rooms',
        lastSelection: null,
    }),
    actions: {

    }
});
