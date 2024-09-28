<template>

    <div class="card">
        <Menubar :model="items">
            <template #start>
<!--                <svg width="35" height="40" viewBox="0 0 35 40" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                     class="h-2rem">-->
<!--                    <path-->
<!--                        d="M25.87 18.05L23.16 17.45L25.27 20.46V29.78L32.49 23.76V13.53L29.18 14.73L25.87 18.04V18.05ZM25.27 35.49L29.18 31.58V27.67L25.27 30.98V35.49ZM20.16 17.14H20.03H20.17H20.16ZM30.1 5.19L34.89 4.81L33.08 12.33L24.1 15.67L30.08 5.2L30.1 5.19ZM5.72 14.74L2.41 13.54V23.77L9.63 29.79V20.47L11.74 17.46L9.03 18.06L5.72 14.75V14.74ZM9.63 30.98L5.72 27.67V31.58L9.63 35.49V30.98ZM4.8 5.2L10.78 15.67L1.81 12.33L0 4.81L4.79 5.19L4.8 5.2ZM24.37 21.05V34.59L22.56 37.29L20.46 39.4H14.44L12.34 37.29L10.53 34.59V21.05L12.42 18.23L17.45 26.8L22.48 18.23L24.37 21.05ZM22.85 0L22.57 0.69L17.45 13.08L12.33 0.69L12.05 0H22.85Z"-->
<!--                        fill="var(&#45;&#45;primary-color)"-->
<!--                    />-->
<!--                    <path-->
<!--                        d="M30.69 4.21L24.37 4.81L22.57 0.69L22.86 0H26.48L30.69 4.21ZM23.75 5.67L22.66 3.08L18.05 14.24V17.14H19.7H20.03H20.16H20.2L24.1 15.7L30.11 5.19L23.75 5.67ZM4.21002 4.21L10.53 4.81L12.33 0.69L12.05 0H8.43002L4.22002 4.21H4.21002ZM21.9 17.4L20.6 18.2H14.3L13 17.4L12.4 18.2L12.42 18.23L17.45 26.8L22.48 18.23L22.5 18.2L21.9 17.4ZM4.79002 5.19L10.8 15.7L14.7 17.14H14.74H15.2H16.85V14.24L12.24 3.09L11.15 5.68L4.79002 5.2V5.19Z"-->
<!--                        fill="var(&#45;&#45;text-color)"-->
<!--                    />-->
<!--                </svg>-->
                <div
                    class="flex justify-content-center align-items-center cursor-pointer mr-2"
                    @click="router.push('/')"
                >
                    <svg width="35" height="40" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path d="M168.8 224h-8.4c-35.3 0-64 28.7-64 64v56.3c0.9-2.2 1.8-4.5 2.8-6.7 17.5-41.4 40.9-79.4 69.6-113.6zM96.5 679.7V736c0 35.3 28.7 64 64 64h8.4c-28.8-34.2-52.1-72.3-69.6-113.7-1-2.1-1.9-4.4-2.8-6.6z" fill="#FFEA00"></path>
                            <path d="M863.5 224H258.3C178.5 294.4 128 397.4 128 512s50.5 217.6 130.3 288h605.1c35.3 0 64-28.7 64-64V288c0.1-35.4-28.5-64-63.9-64z" fill="#536DFE"></path>
                            <path d="M125.1 289.7L512 673.6l382.6-385.5L512 543.9zM717.9 836.2c-14.9 9.5-19.3 29.3-9.8 44.2 6.1 9.6 16.4 14.8 27 14.8 5.9 0 11.8-1.6 17.2-5C791 865.6 825.7 835 855.1 800h-89c-15 13.2-31.1 25.3-48.2 36.2zM927.5 344.1v335.5c21.3-53 32.5-110 32.5-167.6 0-58.4-11.1-115.1-32.5-167.9z" fill="#FFEA00"></path>
                            <path d="M774.3 148.8c-38-27.5-79.9-48.8-124.3-63.1C605.5 71.3 559.1 64 512 64c-60.5 0-119.1 11.8-174.4 35.2-53.4 22.6-101.3 54.9-142.4 96-9.2 9.2-18 18.8-26.4 28.7-28.8 34.2-52.1 72.3-69.6 113.7-0.9 2.2-1.9 4.4-2.8 6.7C74.9 397.6 64 454 64 512s10.9 114.4 32.5 167.7c0.9 2.2 1.8 4.5 2.8 6.7 17.5 41.4 40.8 79.5 69.6 113.7 8.3 9.9 17.1 19.5 26.4 28.7 41.1 41.1 89 73.4 142.4 96C392.9 948.2 451.5 960 512 960c23.1 0 46.3-1.8 69-5.3 17.5-2.7 29.4-19 26.7-36.5s-19.1-29.4-36.5-26.7c-19.5 3-39.4 4.5-59.2 4.5-97.1 0-186-36.3-253.7-96C178.5 729.6 128 626.6 128 512s50.5-217.6 130.3-288c67.7-59.7 156.5-96 253.7-96 40.4 0 80.2 6.2 118.3 18.6 51.5 16.6 97.4 43.3 136.1 77.4h88.7c-23.7-28.3-50.8-53.5-80.8-75.2z" fill="#FFEA00"></path>
                        </g>
                    </svg>
                    <div class="ml-2">
                        Client manager
                    </div>
                </div>
            </template>
            <template #item="{ item, props, hasSubmenu, root }">
                <a v-ripple class="flex align-items-center text-sm" v-bind="props.action" @click="handleItemClick(item)">
                    <span :class="item.icon"/>
                    <span v-if="item.label" class="ml-2">{{ item.label }}</span>
                    <Badge v-if="item.badge" :class="{ 'ml-auto': !root, 'ml-2': root }" :value="item.badge"/>
                    <span v-if="item.shortcut"
                          class="ml-auto border-1 surface-border border-round surface-100 text-xs p-1">{{
                            item.shortcut
                        }}</span>
                    <i v-if="hasSubmenu"
                       :class="['pi pi-angle-down', { 'pi-angle-down ml-2': root, 'pi-angle-right ml-auto': !root }]"></i>
                </a>
            </template>
            <template #end>
                <div class="flex align-items-center gap-2">
<!--                    <InputText placeholder="Search" type="text" class="w-8rem sm:w-auto" size="small"/>-->
                    <Avatar
                        image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                        shape="circle"
                        class="cursor-pointer"
                        @click="handleClickUser"
                    />
                    <Menu ref="menu" id="overlay_menu" :model="menuItems" :popup="true" />
                </div>
            </template>
        </Menubar>
    </div>
</template>

<script setup>
const router = useRouter();

const menu = ref();

const items = ref([
    // {
    //     label: null,
    //     icon: 'pi pi-home',
    //     routeName: '/'
    // },
    {
        label: 'Partners',
        icon: 'pi pi-star',
        routeName: '/partners'
    },
    // {
    //     label: 'Chat',
    //     icon: 'pi pi-comment',
    //     items: [
    //         {
    //             label: 'Public chat rooms',
    //             routeName: '/chat-rooms',
    //         },
    //         {
    //             label: 'Joined chat rooms',
    //             routeName: '/chat-rooms',
    //         },
    //     ]
    // },
    {
        label: 'Emails',
        icon: 'pi pi-envelope',
        items: [
            {
                label: 'Inbox',
                icon: 'pi pi-inbox',
                routeName: '/emails',
            },
            {
                label: 'Email settings',
                icon: 'pi pi-sliders-v',
                routeName: '/email-settings',
            },
        ]
    },
]);

const menuItems = ref([
    {
        label: 'Menu',
        items: [
            {
                label: 'Profile',
                icon: 'pi pi-user'
            },
            {
                label: 'Log out',
                icon: 'pi pi-sign-out'
            }
        ]
    }
]);

function handleClickUser(event) {
    menu.value.toggle(event);
}

function handleItemClick(item) {
    if(!item?.routeName) {
        return;
    }

    router.push(item.routeName);
}
</script>
