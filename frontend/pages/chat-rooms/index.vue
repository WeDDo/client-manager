<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import {useChatRoomStore} from "~/stores/modules/chatRoom.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const toast = useToast();

const token = useCookie('token');

const mainStore = useMainStore();
const store = useChatRoomStore();

const dataTableData = ref();
const mainDataTableRef = ref();

const fetchHelper = useFetchHelper();

const { data, status, error, refresh } = await useFetch(`${baseURL}/${store.apiRouteName}${store.selectedFolder ? `?selected_folder=${store.selectedFolder}` : ''}`, {
    headers: {
        authorization: `Bearer ${token.value}`
    },
});

if (!error.value) {
    dataTableData.value = data.value;
} else {
    fetchHelper.handleUseFetchError(error);
}

watch(data, () => {
    dataTableData.value = data.value;
});
</script>

<template>
    <div>
        <MainMenuBar />
        <div class="m-2">
            <div class="flex justify-content-between text-lg px-2 line-height-4">
                <div>
                    Chat rooms
                </div>
                <div>
                    <Button
                        label="Create chat room"
                        size="small"
                        icon="pi pi-plus"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        @click="() => router.push(`/${store.frontRouteName}/create`)"
                    />
                    <Button
                        label="Enter chat room"
                        size="small"
                        icon="pi pi-comments"
                        class="mr-2"
                        severity="contrast"
                        text
                        raised
                        :disabled="!mainDataTableRef?.selection"
                        @click="() => router.push(`/${store.frontRouteName}/${mainDataTableRef.selection.id}`)"
                    />
                    <Button
                        icon="pi pi-times"
                        size="small"
                        severity="contrast"
                        text
                        raised
                        @click="() => router.push('/')"
                    />
                </div>
            </div>
            <div class="mt-2">
                <MainDataTable
                    ref="mainDataTableRef"
                    v-model:data="dataTableData"
                    v-model:store="store"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
