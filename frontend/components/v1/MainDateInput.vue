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
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: 'date',
    },
    formPrefix: {
        type: String,
        default: 'item.',
    },
    hourFormat: {
        type: String,
        default: undefined,
    },
    showTime: {
        type: Boolean,
        default: false,
    }
})

const dateFormat = computed(() => {

    if (props.type === 'month') {return 'yy-mm';}

    return 'yy-mm-dd';
});

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
        <Calendar
            v-model="value"
            :view="type"
            :date-format="dateFormat"
            :show-time="showTime"
            hour-format="24"
            :aria-describedby="`${name}-help`"
            :class="{ 'p-invalid': errors?.[`${formPrefix}${name}`], 'w-full': true }"
            :disabled="disabled"
        />
        <div>
            <small
                id="email-help"
                class="p-error"
            >{{ errors?.[`${formPrefix}${name}`] ?? '&nbsp;' }}</small>
        </div>
    </div>
</template>

<style scoped>

</style>
