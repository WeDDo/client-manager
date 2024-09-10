<script setup>

const props = defineProps({
    tabs: {
        type: Array,
        default: null,
    }
})
</script>

<template>
    <TabView>
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

<style scoped>

</style>
