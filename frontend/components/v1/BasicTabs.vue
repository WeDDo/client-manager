<script setup>
const route = useRoute();
const mainStore = useMainStore();

const props = defineProps({
    tabs: {
        type: Array,
        default: null,
    }
});


const currentTab = ref(0);

onMounted(() => {
    const savedTabIndex = mainStore.getTabIndex(route.path);
    if (savedTabIndex !== undefined) {
        currentTab.value = savedTabIndex;
    }
});

watch(currentTab, async (newValue) => {
    mainStore.setTabIndex(route.path, newValue);
});
</script>

<template>
    <TabView v-model:activeIndex="currentTab">
        <TabPanel
            v-for="(tab, index) in props.tabs"
            :disabled="tab.disabled"
        >
            <template #header>
                <div class="flex align-items-center gap-2">
                    <div>
                        <span
                            class="font-bold white-space-nowrap mr-1"
                            style="color: var(--red-500)"
                        >
                            {{ Object.keys(props.tabs[index].errors).length > 0 ? '!' : null }}
                        </span>
                        <span>
                            {{ tab.name }}
                        </span>
                    </div>
                </div>
            </template>
            <slot :name="`tab${index}`" />
        </TabPanel>
    </TabView>
</template>

<style>
.p-tabview .p-tabview-panels {
    padding: 0
}
</style>
