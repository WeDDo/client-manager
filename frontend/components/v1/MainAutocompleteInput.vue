<script setup>
import {useFetchHelper} from "~/composables/useFetchHelper.js";

const {public: {baseURL}} = useRuntimeConfig();

const token = useCookie('token');

const fetchHelper = useFetchHelper();

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
    table: {
        type: String,
        required: true,
    },
    searchFields: {
        type: Array,
        default: () => ['id', 'name'],
    },
    required: {
        type: Boolean,
        default: false,
    },
})

const value = defineModel('value');
const items = ref();

async function search(event) {
    console.log('event', event)

    await $fetch(`${baseURL}/autocomplete/search`, {
        method: 'POST',
        body: {
            table: props.table,
            query: event.query,
            search_fields: props.searchFields,
        },
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                items.value = response._data;
                console.log('response', response);
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    });
}

async function fetchById(id) {
    if (!id || typeof id === 'object') return;

    await $fetch(`${baseURL}/autocomplete/search-by-id`, {
        method: 'POST',
        body: {
            table: props.table,
            id: id,
            search_fields: props.searchFields,
        },
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({ response }) {
            if (response.ok) {
                value.value = response._data;
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    });
}

onMounted(() => {
    fetchById(value.value);
})
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
        <AutoComplete
            v-model="value"
            dropdown
            dropdown-mode="blank"
            option-label="label"
            :suggestions="items"
            force-selection
            :class="{ 'p-invalid': errors?.[`item.${name}`] }"
            :aria-describedby="`${name}-help`"
            @complete="search"
        />
        <div>
            <small
                id="email-help"
                class="p-error"
            >
                {{ errors?.value?.[`item.${name}`] ?? '&nbsp;' }}
            </small>
        </div>
    </div>
</template>

<style scoped>

</style>
