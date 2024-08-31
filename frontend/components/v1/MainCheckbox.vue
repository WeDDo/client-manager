<script setup>

const props = defineProps({
    name: {
        type: String,
        default: 'name',
    },
    label: {
        type: String,
        default: '',
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
    },
    showError: {
        type: Boolean,
        default: true,
    }
})

const value = defineModel('value');
</script>

<template>
    <div>
        <Checkbox
            v-model="value"
            :name="name"
            :binary="true"
            :disabled="disabled"
            :invalid="errors?.[`item.${name}`]"
        />
        <label
            v-if="label"
            class="ml-1 text-sm"
            :for="name"
        >
            {{ label ?? null }}
        </label>
        <div v-if="showError" >
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
