<script setup>
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import MainTextInput from "~/components/v1/MainTextInput.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import moment from 'moment';
import {useChatRoomStore} from "~/stores/modules/chatRoom.js";
import {useEcho} from "~/composables/useEcho.js";

const {public: {baseURL}} = useRuntimeConfig();

const store = useChatRoomStore();
const mainStore = useMainStore();

const route = useRoute();
const router = useRouter();
const token = useCookie('token');
const toast = useToast();

const props = defineProps({
    chatRoomId: {
        type: Number,
        required: true,
    },
});

const fetchHelper = useFetchHelper();

const chatMessages = ref([]);
const chatUsers = ref([]);

const messagesContainer = ref(null);
const isSomeoneTyping = ref(false);
const isSomeoneTypingTimer = ref(null);

const showGoToBottom = ref(false);

const messageText = ref();

let typingTimeout = null;

const echo = useEcho();

const leaveLoading = ref(false);
const joinLoading = ref(false);

onMounted(() => {
    echo.value.private(`chat.${props.chatRoomId}`)
        .listen(".MessageSent", (response) => {
            chatMessages.value.push(response.chatMessage);
            if (!showGoToBottom.value) {
                scrollToBottom();  // Auto scroll only if the user is at the bottom
            }
        })
        .listenForWhisper("typing", (response) => {
            isSomeoneTyping.value = true;

            if (isSomeoneTypingTimer.value) {
                clearTimeout(isSomeoneTypingTimer.value);
            }

            isSomeoneTypingTimer.value = setTimeout(() => {
                isSomeoneTyping.value = false;
            }, 1000);
        });

    getChatMessages();
    scrollToBottom();
    setupScrollListener();
});

watch(chatMessages, () => {
    nextTick(() => {
        handleScrollCheck();
    });
});

function setupScrollListener() {
    if (messagesContainer?.value) {
        messagesContainer.value.addEventListener('scroll', handleScrollCheck);
    }
}

function handleScrollCheck() {
    const container = messagesContainer?.value;
    const isAtBottom = Math.abs(container.scrollHeight - container.scrollTop - container.clientHeight) < 500;
    showGoToBottom.value = !isAtBottom;
}

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer?.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
}

async function joinChatRoom() {
    joinLoading.value = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${props.chatRoomId}/join`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                getChatMessages();
                scrollToBottom();
            } else {
                fetchHelper.handleResponseError(response);
            }
            joinLoading.value = false;
        },
    });
}

async function leaveChatRoom() {
    leaveLoading.value = true;

    await $fetch(`${baseURL}/${store.apiRouteName}/${props.chatRoomId}/leave`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                getChatMessages();
            } else {
                fetchHelper.handleResponseError(response);
            }
            leaveLoading.value = false;
        },
    });
}

async function getChatMessages() {
    await $fetch(`${baseURL}/${store.apiRouteName}/${props.chatRoomId}/chat/get-chat-messages`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                chatMessages.value = response._data.chat_messages;
                chatUsers.value = response._data.chat_users;
                scrollToBottom();
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    });
}

async function sendMessage() {
    await $fetch(`${baseURL}/${store.apiRouteName}/${props.chatRoomId}/chat/send-chat-message-to-chat-room`, {
        method: 'POST',
        body: { message: messageText.value },
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Sent successfully', life: 2000});
                messageText.value = null;
                scrollToBottom();
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    })
}

const sendTypingEvent = () => {
    echo.value.private(`chat.${route.params.chatRoomId}`).whisper("typing", {
        chatRoomId: route.params.chatRoomId,
    });
};

const previousMessageLength = ref(0);

function handleUserTyping() {
    const currentMessageLength = messageText.value ? messageText.value.length : 0;

    if (currentMessageLength !== previousMessageLength.value) {
        previousMessageLength.value = currentMessageLength;

        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }

        sendTypingEvent();

        typingTimeout = setTimeout(() => {
            if (isSomeoneTyping.value) {
                echo.value.private(`chat.${route.params.chatRoomId}`).whisper('typing', {
                    chatRoomId: route.params.chatRoomId
                });
            }
        }, 1000);
    }
}

const isJoined = computed(() => {
    return chatUsers.value && (chatUsers.value ?? [])?.some((chatUser) => chatUser.id === mainStore.user?.item?.id);
});
</script>

<template>
    <div class="m-2">
        <div class="flex justify-content-between text-lg px-2 my-2 line-height-4">
            <div>
                Chat
            </div>
            <div>
                <Button
                    v-if="isJoined"
                    label="Leave"
                    size="small"
                    class="mr-2"
                    severity="contrast"
                    text
                    raised
                    :disabled="(chatUsers ?? []).length === 0 || joinLoading"
                    :loading="leaveLoading"
                    @click="leaveChatRoom"
                />
                <Button
                    v-else
                    label="Join"
                    size="small"
                    class="mr-2"
                    severity="contrast"
                    text
                    raised
                    :disabled="(chatUsers ?? []).length === 0 || leaveLoading"
                    :loading="joinLoading"
                    @click="joinChatRoom"
                />

                <Button
                    icon="pi pi-times"
                    size="small"
                    severity="contrast"
                    text
                    raised
                    @click="() => router.push(`/${store.frontRouteName}`)"
                />
            </div>
        </div>
        <div class="chat-container">
            <div class="chat-window overflow-y-auto" ref="messagesContainer">
                <div
                    v-if="isJoined"
                    v-for="message in chatMessages"
                    :key="message.id"
                    class="message-bubble-container"
                >
                    <div
                        :class="{
                        'message-bubble message-bubble-sent': message.sender_user_id === mainStore.user?.item?.id,
                        'message-bubble message-bubble-received': message.sender_user_id !== mainStore.user?.item?.id
                    }"
                    >
                        <div>{{ message.message }}</div>
                        <div class="flex justify-content-end text-xs">
                            <div>{{ message.sender_user.name }} {{ moment(message.created_at).format('YYYY-MM-DD HH:mm') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <button
                v-if="showGoToBottom"
                @click="scrollToBottom"
                class="go-to-bottom-btn"
            >
                Go to Bottom
            </button>

            <div class="chat-input-bar">
                <div class="formgrid grid">
                    <div class="col-12">
                        <small
                            :style="{ visibility: isSomeoneTyping ? 'visible' : 'hidden' }"
                            class="text-gray-700 text-xs"
                        >
                            Someone is typing...
                        </small>
                        <MainTextInput
                            v-model:value="messageText"
                            class="w-full"
                            placeholder="Enter your message here.."
                            hide-error-text
                            end-icon="pi-caret-right"
                            :disabled="!isJoined"
                            @keyup="handleUserTyping"
                            @keyup.enter="sendMessage"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.chat-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 91px - 2rem);
    max-height: 100vh;
    overflow: hidden;
}

.chat-window {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    background-color: #f5f5f5;
}

.chat-input-bar {
    padding: 1rem;
    background-color: white;
    border-top: 1px solid #ddd;
}

.message-bubble-container {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 0.5rem;
}

.message-bubble {
    padding: 0.75rem;
    border-radius: 10px;
    max-width: 100%;
    word-wrap: break-word;
    line-height: 1.5;
}

.message-bubble-sent {
    background-color: #007bff;
    color: white;
    margin-left: auto;
}

.message-bubble-received {
    background-color: #e0e0e0;
    color: #000;
    margin-right: auto;
}
</style>
