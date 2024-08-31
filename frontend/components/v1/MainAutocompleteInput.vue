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
    items: {
        type: Array,
        default: () => [],
    },
    initialLocalValue: {
        type: Object,
        default: null,
    }
})

const value = defineModel('value');
const localValue = ref(props.initialLocalValue);

const filteredItems = ref();

// onMounted(() => {
//     localValue.value = props.initialLocalValue;
// });

// watch(value, async (newValue, oldValue) => {
//     // if(!oldValue && !localValue) {
//     //     localValue.value = value.value
//     // }
//
//     if(!oldValue && !localValue) {
//         localValue.value = props.initialLocalValue;
//     }
//     console.log('watch mainAutocompleteInput', newValue)
// });

// watch(props.initialLocalValue, async (newValue, oldValue) => {
//     console.log('watch(initialLocalValue, async (newValue, oldValue)', props.initialLocalValue)
//     // if(!oldValue && !localValue) {
//     //     localValue.value = value.value
//     // }
//     // console.log('watch mainAutocompleteInput', newValue)
// });

watch(() => props.initialLocalValue, (newVal) => {
    localValue.value = newVal;
}, { deep: true });

async function search(event) {
    setTimeout(() => {
        if (!event.query.trim().length) {
            filteredItems.value = [...props.items];
        } else {
            filteredItems.value = props.items.filter((item) => {
                return item.name.toLowerCase().includes(event.query.toLowerCase());
            });
        }
    }, 250);
}

function handleInput(event) {
    localValue.value = event.value;
    value.value = event.value.id;
}
</script>

<template>
    <div>
        <div><label :for="name">{{ label }}</label></div>
        <AutoComplete
            :model-value="localValue"
            option-label="name"

            data-key="id"
            :suggestions="filteredItems"
            :aria-describedby="`${name}-help`"
            type="text"
            dropdown
            :class="{ 'p-invalid': errors?.[`item.${name}`] }"
            @complete="search($event)"
            @item-select="handleInput($event)"
        />
        {{value}} {{localValue}}
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
