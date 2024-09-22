<script setup>
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import MainTextInput from "~/components/v1/MainTextInput.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import moment from 'moment';
import {useChatRoomStore} from "~/stores/modules/chatRoom.js";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const store = useChatRoomStore();
const token = useCookie('token');
const toast = useToast();

const mainStore = useMainStore();

const props = defineProps({
    chatRoomId: {
        type: Number,
        required: true,
    },
});

const fetchHelper = useFetchHelper();

const messages = ref([]);
const messagesContainer = ref(null);
const isSomeoneTyping = ref(false);
const isSomeoneTypingTimer = ref(null);

const showGoToBottom = ref(false);

const messageText = ref();

let typingTimeout = null;

const echo = ref();

onMounted(() => {
    echo.value = new Echo({
        broadcaster: 'reverb',
        key: 'csf7pk9cj8ezjgbq9neh',
        wsHost: 'localhost',
        wsPort: 8080,
        wssPort: 443,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: 'http://client-manager.test/broadcasting/auth',
        auth: {
            headers: {
                Authorization: `Bearer ${token.value}`,
            },
        },
    });

    echo.value.private(`chat.${route.params.chatRoomId}`)
        .listen(".MessageSent", (response) => {
            messages.value.push(response.chatMessage);
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

watch(messages, () => {
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
    const isAtBottom = Math.abs(container.scrollHeight - container.scrollTop - container.clientHeight) < 100; // Tolerance of 100px
    showGoToBottom.value = !isAtBottom;
}

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer?.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
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
                messages.value = response._data.chat_messages;
                scrollToBottom();
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    })
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
</script>

<template>
    <div class="m-2">
        <div class="flex justify-content-between text-lg px-2 my-2 line-height-4">
            <div>
                Chat
            </div>
            <div>
                <Button
                    label="Back"
                    size="small"
                    @click="() => router.push(`/${store.frontRouteName}`)"
                />
            </div>
        </div>
        <div class="chat-container">
            <div class="chat-window overflow-y-auto" ref="messagesContainer">
                <div
                    v-for="message in messages"
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
