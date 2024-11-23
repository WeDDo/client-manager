<script setup>
const route = useRoute();
const mainStore = useMainStore();

const router = useRouter();

const props = defineProps({
    actions: {
        type: Array,
        default: () => [{
            label: 'Actions',
            items: [],
        }],
    }
});

const menu = ref();

function toggle(event) {
    menu.value.toggle(event);
}
</script>

<template>
    <div>
        <Button
            size="small"
            icon="pi pi-ellipsis-v"
            raised
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_menu"
        />
        <Menu ref="menu" id="overlay_menu" :model="props.actions" :popup="true">
            <template #item="{ item, props }">
                <a
                    v-ripple
                    class="flex align-items-center"
                    v-bind="props.action"
                >
                    <span :class="item.icon" />
                    <span class="ml-2">{{ item.label }}</span>
                    <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                    <span v-if="item.shortcut" class="ml-auto border-1 surface-border border-round surface-100 text-xs p-1">{{ item.shortcut }}</span>
                </a>
            </template>
        </Menu>
    </div>
</template>

<style scoped>
</style>
