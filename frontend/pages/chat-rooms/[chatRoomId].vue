<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import Chat from "~/components/v1/modules/chatMessages/Chat.vue";
import {useMainStore} from "~/stores/main.js";
import {useChatRoomStore} from "~/stores/modules/chatRoom.js";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const toast = useToast();
const token = useCookie('token');

const mainStore = useMainStore();
const store = useChatRoomStore();

</script>

<template>
    <div>
        <MainMenuBar />
        <div v-if="route.params.chatRoomId">
            <Chat :chat-room-id="route.params.chatRoomId" />
        </div>
    </div>
</template>

<style scoped>

</style>
