<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import Chat from "~/components/v1/modules/chatMessages/Chat.vue";
import {useMainStore} from "~/stores/main.js";
import {useChatRoomStore} from "~/stores/modules/chatRoom.js";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();

const route = useRoute();
const router = useRouter();
const store = useChatRoomStore();
const toast = useToast();

const token = useCookie('token');
</script>

<template>
    <div>
        <MainMenuBar />
        <div>
            <Chat :chat-room-id="route.params.chatRoomId" />
        </div>
    </div>
</template>

<style scoped>

</style>
