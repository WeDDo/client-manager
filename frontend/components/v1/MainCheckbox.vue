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
        default: () => ({}),
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
    },
    alignWithInputs: {
        type: Boolean,
        default: false,
    },
});

const value = defineModel('value');
</script>

<template>
    <div class="form-group" :class="{'align-with-inputs': alignWithInputs}">
        <div class="checkbox-container" :class="{'aligned': alignWithInputs}">
            <Checkbox
                v-model="value"
                :name="name"
                :binary="true"
                :disabled="disabled"
                :invalid="errors?.[`item.${name}`]"
                class="checkbox-input"
            />
            <label
                v-if="label"
                class="checkbox-label"
                :for="name"
            >
                {{ label }}
            </label>
        </div>
        <div v-if="showError">
            <small
                id="email-help"
                class="p-error"
            >
                {{ errors?.[`item.${name}`] ?? '\u00A0' }}
            </small>
        </div>
    </div>
</template>

<style scoped>
.form-group {
    margin-bottom: 1rem;
}

.checkbox-container {
    display: flex;
    align-items: center;
}

.checkbox-label {
    margin-left: 8px;
    line-height: 1;
    font-size: 14px;
}

.p-error {
    font-size: 12px;
    margin-top: 4px;
}

/* Add these classes when alignWithInputs is true */
.align-with-inputs {
    margin-top: 20px;
    margin-bottom: 40px;
}

.aligned {
    height: 38px;
}
</style>
