<template>
    <div>
        <div
            v-if="$slots.text"
            class="mt-2 px-2"
        >
            <slot name="text" />
        </div>
        <div>
            <slot name="content" />
        </div>
    </div>
</template>

<script setup>

</script>

<style scoped>

</style>
