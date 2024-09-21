<script setup>

const props = defineProps({
    name: {
        type: String,
        default: 'name',
    },
    label: {
        type: String,
        default: undefined,
    },
    errors: {
        type: Object,
        default: () => {
        },
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    hideErrorText: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: undefined,
    },
    endIcon: {
        type: String,
        default: undefined,
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

        <span class="p-input-icon-right">
            <InputText
                v-model="value"
                :aria-describedby="`${name}-help`"
                type="text"
                :placeholder="placeholder"
                :class="{ 'p-invalid': errors?.[`item.${name}`], 'w-full': true }"
                :disabled="disabled"
            />
            <i
                v-if="props.endIcon"
                :class="`pi ${props.endIcon}`"
                :style="{
                    color: 'var(--gray-500)'
                }"
            />
        </span>

        <!--        <FloatLabel style="margin-top: 18px">-->
        <!--            <label :for="name">-->
        <!--                {{ label }}-->
        <!--            </label>-->
        <!--            <InputText-->
        <!--                v-model="value"-->
        <!--                :aria-describedby="`${name}-help`"-->
        <!--                type="text"-->
        <!--                :class="{ 'p-invalid': errors?.[`item.${name}`], 'w-full': true }"-->
        <!--                :disabled="disabled"-->
        <!--            />-->
        <!--        </FloatLabel>-->

        <div v-if="!hideErrorText">
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
.p-input-icon-right {
    position: relative;
}

.p-input-icon-right .pi {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}
</style>
