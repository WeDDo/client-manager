<script setup>
const { public: { baseURL } } = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const props = defineProps({

});

const emit = defineEmits([]);

const visible = ref(false);
const menu = ref();
const menuItems = ref([
    {
        label: 'Applied sort',
        items: [
            {
                label: 'Asc',
                icon: 'pi pi-sort-up'
            },
            {
                label: 'Desc',
                icon: 'pi pi-sort-down'
            },
        ]
    }
]);

defineExpose({visible});

function handleClick() {
    // visible.value = !visible.value;
}

function toggleMenu(event) {
    menu.value.toggle(event);
}

onMounted(() => {

});
</script>

<template>
    <div>
        <Button
            size="small"
            icon="pi pi-sort-alt"
            severity="secondary"
            @click="toggleMenu"
        />
        <Menu ref="menu" id="overlay_menu" :model="menuItems" :popup="true" />
    </div>
</template>

<style scoped>

</style>
