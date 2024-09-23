<script setup>
import MainMenuBar from "~/components/v1/MainMenuBar.vue";
import Chat from "~/components/v1/modules/chatMessages/Chat.vue";
import {useChatRoomStore} from "~/stores/modules/chatRoom.js";
import MainDataTable from "~/components/v1/MainDataTable.vue";

const {public: {baseURL}} = useRuntimeConfig();

const route = useRoute();
const router = useRouter();
const mainStore = useMainStore();
const toast = useToast();

const token = useCookie('token');

const dataTableData = ref();
const store = useChatRoomStore();
const mainDataTableRef = ref();
const confirmDeleteDialogRef = ref();

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
                        @click="() => router.push(`/${store.frontRouteName}/create`)"
                    />
                    <Button
                        label="Enter chat room"
                        size="small"
                        icon="pi pi-comments"
                        class="mr-2"
                        :disabled="!mainDataTableRef?.selection"
                        @click="() => router.push(`/${store.frontRouteName}/${mainDataTableRef.selection.id}`)"
                    />
                    <Button
                        label="Back"
                        size="small"
                        @click="() => router.push('/')"
                    />
                </div>
            </div>
            <div class="mt-2">
                <MainDataTable
                    ref="mainDataTableRef"
                    v-model:data="dataTableData"
                    v-model:store="store"
                >
<!--                    <template #is_seen="slotProps">-->
<!--                        <div class="flex align-items-center">-->
<!--                            <i :class="`pi ${slotProps.data.is_seen ? 'pi-check-square' : 'pi-stop'}`" />-->
<!--                        </div>-->
<!--                    </template>-->
<!--                    <template #is_flagged="slotProps">-->
<!--                        <div class="flex align-items-center">-->
<!--                            <i :class="`pi ${slotProps.data.is_flagged ? 'pi-check-square' : 'pi-stop'}`" />-->
<!--                        </div>-->
<!--                    </template>-->
<!--                    <template #is_answered="slotProps">-->
<!--                        <div class="flex align-items-center">-->
<!--                            <i :class="`pi ${slotProps.data.is_answered ? 'pi-check-square' : 'pi-stop'}`" />-->
<!--                        </div>-->
<!--                    </template>-->
                </MainDataTable>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
