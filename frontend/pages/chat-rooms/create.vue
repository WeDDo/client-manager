<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import BasicTabs from "~/components/v1/BasicTabs.vue";
import MainForm from "~/components/v1/modules/chatRooms/MainForm.vue";
import {useChatRoomStore} from "~/stores/modules/chatRoom.js";

const {public: {baseURL}} = useRuntimeConfig();

const mainStore = useMainStore();

const router = useRouter();
const store = useChatRoomStore();
const toast = useToast();

const token = useCookie('token');

let formValues = reactive({
    item: {
        name: null,
    },
});

const mainFormRef = ref();
let tabs = reactive([
    {name: "Main", ref: mainFormRef, errors: {}},
]);

const formHelper = useFormHelper(formValues, tabs);
const fetchHelper = useFetchHelper();

async function handleCreate() {
    if (!await formHelper.validateForm(formHelper.errors)) {
        return;
    }

    mainStore.actionLoading = true;

    await $fetch(`${baseURL}/${store.apiRouteName}`, {
        method: 'POST',
        body: formValues.item,
        headers: {
            authorization: `Bearer ${token.value}`
        },
        onResponse({response}) {
            if (response.ok) {
                toast.add({severity: 'success', summary: 'Created successfully', life: 2000});
                store.lastSelection = response._data.item;
                router.push(`/${store.frontRouteName}`);
            } else {
                fetchHelper.handleResponseError(response);
            }
            mainStore.actionLoading = false;
        },
    })
}
</script>

<template>
    <div>
        <MainMenuBar/>
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    Chat room creation
                </div>
                <div>
                    <Button
                        label="Save"
                        size="small"
                        icon="pi pi-save"
                        class="mr-2"
                        @click="handleCreate"
                    />
                    <Button
                        label="Back"
                        size="small"
                        @click="() => router.push(`/${store.frontRouteName}`)"
                    />
                </div>
            </div>
            <div class="mt-2">
                <BasicTabs
                    :tabs="tabs"
                >
                    <template #tab0>
                        <MainForm
                            ref="mainFormRef"
                            :tab="0"
                            :initial-form-values="formValues"
                            @set-form-values="formHelper.setFormValues($event)"
                            @handle-submit="handleCreate()"
                            @set-errors="formHelper.setErrors"
                        />
                    </template>
                </BasicTabs>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
