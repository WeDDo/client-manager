<script setup>
import MainTextInput from "~/components/v1/MainTextInput.vue";
import MainSelectInput from "~/components/v1/MainSelectInput.vue";
import MainDateInput from "~/components/v1/MainDateInput.vue";
import MainCheckbox from "~/components/v1/MainCheckbox.vue";
import {useFetchHelper} from "~/composables/useFetchHelper.js";
import {useEmailInboxSettingStore} from "~/stores/modules/emailInboxSetting.js";

const {public: {baseURL}} = useRuntimeConfig();

const router = useRouter();
const token = useCookie('token');

const store = useEmailInboxSettingStore();

const toast = useToast();

const props = defineProps({});

const mainStore = useMainStore();

const emit = defineEmits([
    'refresh-data-table',
]);

const fetchHelper = useFetchHelper();

const visible = ref(false);
const data = ref([[], []]);

defineExpose({visible});

function handleClickGetInboxes() {
    getInboxes();
    visible.value = !visible.value;
}

async function getInboxes() {
    await $fetch(`${baseURL}/${store.apiRouteName}/get-inboxes-imap`, {
        method: 'GET',
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                data.value = response._data;
            } else {
                fetchHelper.handleResponseError(response);
            }
        },
    })
}

async function createInboxes() {
    await $fetch(`${baseURL}/${store.apiRouteName}/create-inboxes`, {
        method: 'POST',
        body: data.value,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                visible.value = false;
                toast.add({severity: 'success', summary: 'Created inboxes successfully!', life: 2000});
                getInboxes();
                emit('refresh-data-table');
            } else {
                fetchHelper.handleResponseError(response, form);
            }
        },
    })
}

onMounted(() => {
    getInboxes();
});

</script>

<template>
    <div>
        <Button
            label="Get Inboxes"
            size="small"
            icon="pi pi-inbox"
            severity="contrast"
            text
            raised
            @click="handleClickGetInboxes"
        />

        <Dialog
            v-model:visible="visible"
            :style="{ width: '50vw' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        >
            <template #header>
                <div class="text-lg font-medium">
                    Email inboxes
                </div>
            </template>
            <div>
                <PickList v-model="data" listStyle="height:342px" dataKey="id" breakpoint="1400px">
                    <template #sourceheader>
                        Available
                    </template>
                    <template #targetheader>
                        Selected
                    </template>
                    <template #item="slotProps">
                        <div>
                            {{slotProps.item.name}}
                        </div>
                    </template>
                </PickList>

                <Button
                    label="Save"
                    size="small"
                    icon="pi pi-save"
                    class="mr-2 w-full"
                    severity="primary"
                    :disabled="data[1].length === 0"
                    @click="createInboxes"
                />
            </div>
        </Dialog>
    </div>
</template>

<style scoped>

</style>
