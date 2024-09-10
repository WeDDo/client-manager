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
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    }
})

const value = defineModel('value');
</script>

<template>
    <div>
        <div>
            <label :for="name">
                {{ label }}
            </label>
            <Badge
                v-if="required"
                class="ml-1 mb-1"
                size="1"
            />
        </div>
        <Password
            v-model="value"
            :aria-describedby="`${name}-help`"
            type="text"
            :class="{ 'p-invalid': errors?.[`item.${name}`], 'w-full': true }"
            input-class="w-full"
            :disabled="disabled"
            toggle-mask
            :feedback="false"
        />
        <div>
            <small
                id="email-help"
                class="p-error"
            >
                {{ errors?.[`item.${name}`] ?? '&nbsp;' }}
            </small>
        </div>
    </div>
</template>

<style scoped>
</style>
