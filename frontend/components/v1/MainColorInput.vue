<script setup>

const props = defineProps({
    name: {
        type: String,
        default: 'name',
    },
    label: {
        type: String,
        default: 'default_label',
    },
    errors: {
        type: Object,
        default: () => {},
    }
})

const value = defineModel('value');
</script>

<template>
    <div>
        <div><label :for="name">{{ label }}</label></div>
        <ColorPicker
            v-model="value"
            input-id="cp-hex"
            :aria-describedby="`${name}-help`"
            :class="{ 'p-invalid': errors?.[`item.${name}`], 'w-full': true }"
        />
        <div>
            <small
                id="email-help"
                class="p-error"
            >{{ errors?.[`item.${name}`] ?? '&nbsp;' }}</small>
        </div>
    </div>
</template>

<style scoped>

</style>
