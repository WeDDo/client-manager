<script setup>
import moment from "moment";
import {useRouter} from "vue-router";
import echo from "~/components/app/echo.js";
import {useAuthHelper} from "~/composables/useAuthHelper.js";

const { public: { baseURL } } = useRuntimeConfig();

const authHelper = useAuthHelper();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({

});

const menu = ref();
const notifications = reactive([]);

const value = defineModel('value');

onMounted(() => {
    getNotifications();

    const user = authHelper.getAuthUser();
    echo.private(`App.Models.User.${user.item.id}`).notification((notification) => {
        const formattedNotification = {
            id: notification.id,
            data: notification,
            read_at: notification.read_at,
            created_at: notification.created_at,
        };

        notifications.unshift(formattedNotification);
    });
});

function getNotifications() {
    $fetch(`${baseURL}/notifications`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({ response }) {
            if (response.ok) {
                notifications.length = 0;
                notifications.push(...response._data);
            } else {
                console.log('notifications error');
            }
        },
    })
}

function setNotificationRead(notificationId) {
    $fetch(`${baseURL}/notifications/${notificationId}/read`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({ response }) {
            if (response.ok) {
                getNotifications();
            } else {
                console.log('notifications read error');
            }
        },
    });
}

function toggleMenu(event) {
    menu.value.toggle(event);
}

function handleNotificationClick(notification) {
    if(!notification?.read_at) {
        setNotificationRead(notification.id);
    }
    if (notification.data.url) {
        router.push(notification.data.url);
    }
}

function handleClickViewAllNotifications() {

}
</script>

<template>
    <div>
        <Menu
            id="menu"
            ref="menu"
            :model="notifications"
            :popup="true"
        >
            <template #item="{ item, props }">
                <div
                    v-ripple
                    class="flex align-items-center p-1 text-sm"
                    :style="{
                        background: !item.read_at ? 'var(--gray-100)' : null,
                    }"
                    v-bind="props.action"
                    @click.stop="handleNotificationClick(item)"
                >
                    <div>
                        <span :class="`pi ${item.data.icon}`" />
                    </div>
                    <div class="mt-2">
                        <div>
                            <span class="ml-2">{{ item.data.message }}</span>
                        </div>
                        <div class="text-xs flex justify-content-end text-400">
                            <span>{{ moment(item.created_at).format('YYYY-MM-DD HH:mm') }}</span>
                        </div>
                    </div>
                </div>
            </template>
            <template #end>
                <div
                    class="p-1 text-center cursor-pointer"
                    @click="handleClickViewAllNotifications"
                >
                    View all notifications
                </div>
            </template>
        </Menu>
        <div>
            <Button
                :badge="notifications?.length ?? 0"
                icon="pi pi-bell"
                severity="secondary"
                text
                rounded
                size="large"
                class="m-0 px-2"
                style="height: 32px"
                aria-label="Notifications"
                @click="toggleMenu"
            />
        </div>
    </div>
</template>

<style scoped>

</style>
