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
    options: {
        type: Array,
        default: () => [],
    },
    optionValue: {
        type: String,
        default: 'id',
    },
    optionLabel: {
        type: String,
        default: 'name',
    },
    simpleOptions: {
        type: Boolean,
        default: false,
    },
    formPrefix: {
        type: String,
        default: 'item.',
    },
    showClear: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
})

const value = defineModel('value');
</script>

<template>
    <div>
        <div v-if="label">
            <label :for="name">
                {{ label }}
            </label>
            <Badge
                v-if="required"
                class="ml-1 mb-1"
                size="1"
            />
        </div>
        <Dropdown
            v-model="value"
            :options="options"
            :option-value="simpleOptions ? undefined : props.optionValue"
            :option-label="simpleOptions ? undefined : props.optionLabel"
            :show-clear="showClear"
            :class="{ 'p-invalid': errors?.[`${formPrefix}${name}`], 'w-full': true }"
        >
            <template
                v-for="(_, slotName) in $slots"
                #[slotName]="slotProps"
            >
                <slot
                    :name="slotName"
                    v-bind="slotProps"
                />
            </template>
        </Dropdown>
        <div>
            <small
                id="email-help"
                class="p-error"
            >
                {{ errors?.[`${formPrefix}${name}`] ?? '&nbsp;' }}
            </small>
        </div>
    </div>
</template>

<style scoped>

</style>
